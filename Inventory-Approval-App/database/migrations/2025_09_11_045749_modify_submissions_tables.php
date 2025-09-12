<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Daftar status baru
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
            'Accepted'
        ];

        // Modifikasi tabel peminjaman
        Schema::table('lend_submissions', function (Blueprint $table) use ($statuses) {
            $table->string('proposal_id')->unique()->nullable()->after('id');
            $table->enum('status', $statuses)->default('Pending')->change();
        });

        // Modifikasi tabel pengadaan
        Schema::table('procure_submissions', function (Blueprint $table) use ($statuses) {
            $table->string('proposal_id')->unique()->nullable()->after('id');
            $table->enum('status', $statuses)->default('Pending')->change();
        });
    }

    public function down(): void
    {
        // Logika untuk membatalkan migrasi (rollback)
        Schema::table('lend_submissions', function (Blueprint $table) {
            $table->dropColumn('proposal_id');
            $table->string('status')->default('pending')->change();
        });

        Schema::table('procure_submissions', function (Blueprint $table) {
            $table->dropColumn('proposal_id');
            $table->string('status')->default('pending')->change();
        });
    }
};