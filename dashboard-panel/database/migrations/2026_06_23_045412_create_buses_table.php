<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('bus_number')->unique(); // رقم اللوحة
            $table->string('driver_name');          // اسم السائق
            $table->string('driver_phone');         // رقم السائق لإشعارات الطوارئ
            $table->enum('status', ['inactive', 'active', 'delayed'])->default('inactive');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('buses');
    }
};