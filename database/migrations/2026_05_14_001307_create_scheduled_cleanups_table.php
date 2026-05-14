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
        Schema::create('scheduled_cleanups', function (Blueprint $table) {
            $table->id();
             $table->foreignId('company_id')->constrained('companies');
            $table->string('area_name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('weekly');
            $table->enum('status', ['active', 'paused', 'completed'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_cleanups');
    }
};
