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
        // 1. Tambahkan kolom ke tabel lend_submissions (Peminjaman)
        Schema::table('lend_submissions', function (Blueprint $table) {
            $table->string('approved_by', 10)->nullable(); // Menyimpan 'COO' atau 'CHRD'
            $table->string('final_approver_nip', 20)->nullable();
            $table->string('final_approver_name', 100)->nullable();
            $table->string('final_approver_ttd_path')->nullable();
        });
        
        // 2. Tambahkan kolom yang sama ke tabel procure_submissions (Pengadaan)
        Schema::table('procure_submissions', function (Blueprint $table) {
            $table->string('approved_by', 10)->nullable();
            $table->string('final_approver_nip', 20)->nullable();
            $table->string('final_approver_name', 100)->nullable();
            $table->string('final_approver_ttd_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Hapus kolom dari tabel lend_submissions
        Schema::table('lend_submissions', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'final_approver_nip', 'final_approver_name', 'final_approver_ttd_path']);
        });

        // 2. Hapus kolom dari tabel procure_submissions
        Schema::table('procure_submissions', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'final_approver_nip', 'final_approver_name', 'final_approver_ttd_path']);
        });
    }
};