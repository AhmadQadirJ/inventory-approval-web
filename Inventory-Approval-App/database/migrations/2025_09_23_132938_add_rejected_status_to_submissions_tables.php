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
            'Processed - Finance',
            'Processed - COO/CHRD',
            'Processed - CHRD',
            'Rejected - GA',
            'Rejected - Finance',
            'Rejected - CHRD',
            'Rejected - COO',
            'Accepted'
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
            'Pending', 'Processed - GA', 'Processed - Finance',
            'Processed - COO/CHRD', 'Rejected - GA', 'Rejected - Finance',
            'Rejected - COO','Rejected CHRD', 'Accepted'
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