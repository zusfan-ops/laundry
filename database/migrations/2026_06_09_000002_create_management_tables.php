<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('subtitle', 180)->nullable();
            $table->string('cta_label', 40)->nullable();
            $table->string('cta_url', 255)->nullable();
            $table->string('art', 40)->default('bubbles');     // SVG art key
            $table->string('color_from', 20)->default('#0EA5A4');
            $table->string('color_to', 20)->default('#0B7E7D');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('period', 7);                       // YYYY-MM
            $table->unsignedBigInteger('base_amount')->default(0);
            $table->unsignedBigInteger('bonus')->default(0);
            $table->unsignedBigInteger('deduction')->default(0);
            $table->unsignedBigInteger('net_amount')->default(0);
            $table->enum('status', ['draft', 'paid'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'period']);
            $table->index('period');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('position', 60)->nullable()->after('role');
            $table->unsignedBigInteger('base_salary')->default(0)->after('loyalty_balance');
            $table->date('hired_at')->nullable()->after('base_salary');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['position', 'base_salary', 'hired_at']);
        });
        Schema::dropIfExists('salaries');
        Schema::dropIfExists('promo_banners');
    }
};
