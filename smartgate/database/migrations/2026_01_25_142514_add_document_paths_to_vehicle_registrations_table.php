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
        Schema::table('vehicle_registrations', function (Blueprint $table) {
            $table->string('cr_path')->nullable();
            $table->string('or_path')->nullable();
            $table->string('com_path')->nullable();
            $table->string('student_id_path')->nullable();
            $table->string('license_path')->nullable();
            $table->string('employee_id_path')->nullable();
            $table->string('payment_receipt_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'cr_path', 'or_path', 'com_path', 'student_id_path', 
                'license_path', 'employee_id_path', 'payment_receipt_path'
            ]);
        });
    }
};
