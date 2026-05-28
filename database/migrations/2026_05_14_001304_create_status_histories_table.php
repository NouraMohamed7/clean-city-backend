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
        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->enum('from_status', ['pending', 'assigned', 'in_progress', 'resolved', 'rejected']);
            $table->enum('to_status', ['pending', 'assigned', 'in_progress', 'resolved', 'rejected']);
            $table->foreignId('changed_by')
    ->nullable()
    ->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['report_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
       public function down(): void
    {
        Schema::dropIfExists('status_histories');
    }

};
