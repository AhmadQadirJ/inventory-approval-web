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
        Schema::create('inventory_units', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel inventory (barang induk)
            $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
            
            $table->string('serial_number')->unique();
            $table->string('condition')->default('Good'); // Kondisi unit
            $table->string('gambar')->nullable(); // Path gambar unit fisik
            
            // Kolom yang mungkin diperlukan (contoh: lokasi spesifik)
            $table->string('location')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_units');
    }
};
