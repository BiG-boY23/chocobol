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
            $table->string('university_id')->nullable()->change();
            $table->string('college_dept')->nullable()->change();
            $table->string('model_year')->nullable()->change();
            $table->string('color', 100)->nullable()->change();
            $table->string('engine_number', 100)->nullable()->change();
            $table->date('validity_from')->nullable()->change();
            $table->date('validity_to')->nullable()->change();
            $table->string('rfid_tag_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_registrations', function (Blueprint $table) {
            $table->string('university_id')->nullable(false)->change();
            $table->string('college_dept')->nullable(false)->change();
            $table->string('model_year')->nullable(false)->change();
            $table->string('color', 100)->nullable(false)->change();
            $table->string('engine_number', 100)->nullable(false)->change();
            $table->date('validity_from')->nullable(false)->change();
            $table->date('validity_to')->nullable(false)->change();
            $table->string('rfid_tag_id')->nullable(false)->change();
        });
    }
};
