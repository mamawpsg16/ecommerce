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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->integer('age');
            $table->date('birth_date');
            $table->string('email',100);
            $table->text('address');
            $table->string('personal_phone_number',15)->nullable();
            $table->string('industry');
            $table->string('company');
            $table->string('position');
            $table->string('company_phone_number',15)->nullable();
            $table->string('company_address');
            $table->timestamps();

            $table->index('name');
            $table->index('company');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
