<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lend_submissions', function (Blueprint $table) {
            // Hapus kolom lama
            if (Schema::hasColumn('lend_submissions', 'item_name')) {
             $table->dropColumn('item_name');
            }
            // Tambahkan kolom baru sebagai foreign key ke tabel inventories
            $table->foreignId('inventory_id')->nullable()->after('department')->constrained()->onDelete('cascade');
            
        });
    }

    public function down(): void
    {
        Schema::table('lend_submissions', function (Blueprint $table) {
            // Logika untuk rollback (mengembalikan seperti semula)
            $table->dropForeign(['inventory_id']);
            $table->dropColumn('inventory_id');
            $table->string('item_name')->after('department');
        });
    }
};