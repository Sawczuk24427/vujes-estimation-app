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
        Schema::table('estimations', function (Blueprint $table) {
            $table->enum('type', ['fixed', 'hourly'])->default('fixed')->after('title');
            $table->integer('hours')->nullable()->after('type');
            $table->decimal('hourly-rate', 8, 2)->nullable()->after('hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimations', function (Blueprint $table) {
            $table->dropColumn(['type', 'hours', 'hourly_rate']);
        });
    }
};
