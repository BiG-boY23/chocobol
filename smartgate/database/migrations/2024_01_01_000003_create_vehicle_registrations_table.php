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
        Schema::create('vehicle_registrations', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['student', 'faculty', 'staff']);
            $table->string('full_name');
            $table->string('university_id');
            $table->string('college_dept');
            $table->string('contact_number', 20);
            $table->string('email_address');
            $table->string('course')->nullable();
            $table->string('year_level')->nullable();
            $table->string('rank')->nullable();
            $table->string('office')->nullable();
            $table->string('business_stall_name')->nullable();
            $table->string('vendor_address')->nullable();
            $table->enum('vehicle_type', ['car', 'suv', 'van', 'motorcycle']);
            $table->string('registered_owner');
            $table->string('make_brand');
            $table->string('model_year');
            $table->string('color', 100);
            $table->string('plate_number', 20);
            $table->string('engine_number', 100);
            $table->json('sticker_classification')->nullable();
            $table->json('requirements')->nullable();
            $table->date('validity_from');
            $table->date('validity_to');
            $table->string('rfid_tag_id')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('office_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_registrations');
    }
};
