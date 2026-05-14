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
        Schema::create('seasonal_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities');
            $table->tinyInteger('month')->unsigned();
            $table->string('event_name');
            $table->integer('predicted_increase_percent')->default(0);
            $table->text('recommendation');
            $table->json('based_on_years');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['city_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasonal_alerts');
    }
};
