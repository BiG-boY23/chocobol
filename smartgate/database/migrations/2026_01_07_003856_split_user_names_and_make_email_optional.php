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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('id')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('middle_name')->after('last_name')->nullable();
            $table->string('email')->nullable()->change();
            // We'll drop 'name' later or keep it for now and migrate data if needed, 
            // but for a fresh start/custom project, we can just transition.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'middle_name']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
