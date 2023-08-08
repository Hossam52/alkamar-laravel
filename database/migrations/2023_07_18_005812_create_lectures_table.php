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
        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('stage_id');
            
            $table->string('title')->nullable(false);            
            $table->dateTime('lecture_date')->nullable(false);            
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('stage_id')->references('id')->on('stages');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lectures');
    }
};
