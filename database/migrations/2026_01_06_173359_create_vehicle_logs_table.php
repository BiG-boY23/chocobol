<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_logs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->unsignedBigInteger('vehicle_registration_id')->nullable();
            $blueprint->string('rfid_tag_id')->nullable();
            $blueprint->enum('type', ['entry', 'exit']);
            $blueprint->timestamp('timestamp');
            $blueprint->timestamps();

            $blueprint->foreign('vehicle_registration_id')->references('id')->on('vehicle_registrations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_logs');
    }
};
