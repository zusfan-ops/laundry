<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question', 200);
            $table->text('answer');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->string('opening_hours', 120)->nullable()->after('address');
            $table->string('maps_url', 255)->nullable()->after('lng');
        });
    }

    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn(['opening_hours', 'maps_url']);
        });
        Schema::dropIfExists('faqs');
    }
};
