<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Daftar status LENGKAP yang baru
        $statuses = [
            'Pending',
            'Processed - GA',
            'Processed - Manager',
            'Processed - Finance',
            'Processed - COO',
            'Rejected - GA',
            'Rejected - Manager',
            'Rejected - Finance',
            'Rejected - COO',
            'Accepted',
            'Rejected'
        ];

        // Gunakan Schema Builder Laravel untuk mengubah kolom
        Schema::table('lend_submissions', function (Blueprint $table) use ($statuses) {
            $table->enum('status', $statuses)->default('Pending')->change();
        });

        Schema::table('procure_submissions', function (Blueprint $table) use ($statuses) {
            $table->enum('status', $statuses)->default('Pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opsi rollback (praktik yang baik)
        $statuses = [
            'Pending', 'Processed - GA', 'Processed - Manager', 'Processed - Finance',
            'Processed - COO', 'Rejected - GA', 'Rejected - Manager', 'Rejected - Finance',
            'Rejected - COO', 'Accepted'
        ];

        // Gunakan Schema Builder Laravel untuk mengembalikan kolom
        Schema::table('lend_submissions', function (Blueprint $table) use ($statuses) {
            $table->enum('status', $statuses)->default('Pending')->change();
        });

        Schema::table('procure_submissions', function (Blueprint $table) use ($statuses) {
            $table->enum('status', $statuses)->default('Pending')->change();
        });
    }
};