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
        Schema::table('lend_submissions', function (Blueprint $table) {
            $table->time('start_time')->after('end_date');
            $table->time('end_time')->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lend_submissions', function (Blueprint $table) {
            //
        });
    }
};
