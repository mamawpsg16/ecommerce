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
        Schema::create('event_participant_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id');
            $table->foreignId('participant_id');
            $table->foreignId('item_id');
            $table->string('item_name',100);
            $table->integer('quantity');
            $table->timestamps();

            $table->index('event_id');
            $table->index('participant_id');
            $table->index('item_name');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_participant_items');
    }
};
