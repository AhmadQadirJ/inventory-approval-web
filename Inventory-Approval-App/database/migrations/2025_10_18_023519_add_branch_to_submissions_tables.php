<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('lend_submissions', function (Blueprint $table) {
            $table->string('branch')->after('department');
        });
        Schema::table('procure_submissions', function (Blueprint $table) {
            $table->string('branch')->after('department');
        });
    }
    public function down(): void {
        Schema::table('lend_submissions', function (Blueprint $table) {
            $table->dropColumn('branch');
        });
        Schema::table('procure_submissions', function (Blueprint $table) {
            $table->dropColumn('branch');
        });
    }
};