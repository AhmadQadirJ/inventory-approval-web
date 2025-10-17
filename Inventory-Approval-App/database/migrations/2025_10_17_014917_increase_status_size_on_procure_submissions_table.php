<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procure_submissions', function (Blueprint $table) {
            // PERBAIKI: Ubah ukuran kolom 'status' menjadi 50 (atau 100)
            $table->string('status', 50)->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('procure_submissions', function (Blueprint $table) {
            // Opsional: Kembali ke ukuran default jika diperlukan rollback
            $table->string('status', 20)->change();
        });
    }
};
