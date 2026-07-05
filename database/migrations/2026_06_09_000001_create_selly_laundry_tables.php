<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('phone', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedBigInteger('free_shipping_threshold')->default(0);
            $table->unsignedBigInteger('base_shipping_fee')->default(0);
            $table->unsignedBigInteger('fee_per_km')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('icon', 80)->nullable();
            $table->string('color', 20)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_categories');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets');
            $table->string('name', 120);
            $table->string('description', 255)->nullable();
            $table->enum('pricing_type', ['weight', 'unit']);
            $table->unsignedBigInteger('unit_price');
            $table->string('unit_label', 20)->default('kg');
            $table->decimal('min_qty', 6, 2)->default(0);
            $table->integer('est_duration_hours')->default(48);
            $table->string('icon', 80)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('pricing_type');
        });

        Schema::create('service_modifiers', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['speed', 'perfume', 'treatment']);
            $table->string('name', 80);
            $table->decimal('multiplier', 4, 2)->default(1.00);
            $table->unsignedBigInteger('flat_fee')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('label', 40)->nullable();
            $table->string('recipient', 120)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('full_address');
            $table->string('notes', 255)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
        });

        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('outlet_id');
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->enum('type', ['percent', 'fixed', 'free_shipping']);
            $table->unsignedBigInteger('value')->default(0);
            $table->unsignedBigInteger('min_order')->default(0);
            $table->unsignedBigInteger('max_discount')->nullable();
            $table->integer('quota')->nullable();
            $table->integer('per_user_limit')->default(1);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->char('client_uuid', 36)->nullable()->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->foreignId('address_id')->constrained('addresses');

            $table->enum('status', [
                'pending_payment', 'placed', 'assigned_pickup', 'picked_up',
                'at_outlet', 'weighed', 'awaiting_price_confirm', 'processing',
                'ready', 'assigned_delivery', 'delivering', 'completed', 'cancelled',
            ])->default('placed');

            $table->unsignedBigInteger('estimated_subtotal')->default(0);
            $table->unsignedBigInteger('final_subtotal')->default(0);
            $table->unsignedBigInteger('shipping_fee')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('estimated_total')->default(0);
            $table->unsignedBigInteger('final_total')->default(0);

            $table->decimal('estimated_weight', 6, 2)->nullable();
            $table->decimal('actual_weight', 6, 2)->nullable();

            $table->foreignId('pickup_slot_id')->nullable()->constrained('time_slots');
            $table->foreignId('delivery_slot_id')->nullable()->constrained('time_slots');
            $table->date('pickup_date')->nullable();
            $table->date('delivery_date')->nullable();

            $table->enum('payment_mode', ['prepaid_estimate', 'pay_after_weigh'])->default('pay_after_weigh');
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'refunded', 'partial'])->default('unpaid');
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers');

            $table->string('notes', 255)->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('review', 500)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('user_id');
            $table->index(['outlet_id', 'status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services');
            $table->string('service_name', 120);
            $table->enum('pricing_type', ['weight', 'unit']);
            $table->unsignedBigInteger('unit_price');
            $table->decimal('speed_multiplier', 4, 2)->default(1.00);
            $table->string('speed_name', 40)->nullable();
            $table->unsignedBigInteger('perfume_fee')->default(0);
            $table->string('perfume_name', 40)->nullable();
            $table->decimal('estimated_qty', 6, 2)->nullable();
            $table->decimal('actual_qty', 6, 2)->nullable();
            $table->unsignedBigInteger('line_total')->default(0);
            $table->timestamps();
            $table->index('order_id');
        });

        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40);
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_role', 20)->nullable();
            $table->string('note', 255)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->char('client_uuid', 36)->nullable()->unique();
            $table->timestamp('created_at')->nullable();
            $table->index('order_id');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->enum('gateway', ['midtrans', 'xendit', 'cash']);
            $table->string('external_id', 120)->nullable();
            $table->unsignedBigInteger('amount');
            $table->enum('type', ['charge', 'refund', 'difference'])->default('charge');
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'refunded'])->default('pending');
            $table->string('method', 40)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
            $table->index('order_id');
            $table->index('status');
        });

        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->string('vehicle', 40)->nullable();
            $table->boolean('is_available')->default(true);
            $table->decimal('last_lat', 10, 7)->nullable();
            $table->decimal('last_lng', 10, 7)->nullable();
            $table->timestamps();
        });

        Schema::create('courier_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('courier_id')->constrained('couriers');
            $table->enum('type', ['pickup', 'delivery']);
            $table->enum('status', ['assigned', 'on_the_way', 'arrived', 'done', 'failed'])->default('assigned');
            $table->string('proof_photo', 255)->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();
            $table->index('order_id');
            $table->index(['courier_id', 'status']);
        });

        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('vouchers');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('order_id')->constrained('orders');
            $table->unsignedBigInteger('discount');
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->enum('type', ['earn', 'redeem', 'expire', 'adjust']);
            $table->bigInteger('points');
            $table->bigInteger('balance_after');
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index('user_id');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->enum('channel', ['push', 'whatsapp', 'email']);
            $table->string('title', 120);
            $table->string('body', 255);
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['user_id', 'is_read']);
        });

        // staff outlet_id FK (added after outlets exists)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('outlet_id')->references('id')->on('outlets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
        });
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('courier_assignments');
        Schema::dropIfExists('couriers');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_status_logs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('service_modifiers');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
        Schema::dropIfExists('outlets');
    }
};
