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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('plate')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('purpose')->nullable();
            $table->string('destination')->nullable();
            $table->timestamp('time_in');
            $table->timestamp('time_out')->nullable();
            $table->enum('status', ['inside', 'left'])->default('inside');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
