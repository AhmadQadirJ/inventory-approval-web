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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('brand')->nullable();
            $table->enum('kategori', ['Elektronik', 'Non Elektronik', 'Ruangan']);
            $table->decimal('harga', 15, 2)->nullable();
            $table->enum('branch', ['Bandung', 'Jakarta', 'Surabaya']);
            $table->year('tahun_beli')->nullable();
            $table->string('nama_vendor')->nullable();
            $table->text('vendor_link')->nullable();
            $table->integer('qty');
            $table->text('deskripsi')->nullable();
            $table->string('gambar', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
