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
        Schema::create('lend_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('employee_id');
            $table->string('department');
            $table->string('item_name');
            $table->integer('quantity');
            $table->string('purpose_title');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description');
            $table->string('status')
              ->default('Pending')
              ->check(fn ($table) => in_array($table->status, ['Pending', 'Processed - GA', 'Processed - Manager', 'Processed - Finance', 'Processed - COO', 'Accepted', 'Rejected - General', 'Rejected - Manager', 'Rejected - Finance', 'Rejected - COO'])); // pending, approved, rejected, completed
            $table->timestamps();
            

        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lend_submissions');
    }
};
