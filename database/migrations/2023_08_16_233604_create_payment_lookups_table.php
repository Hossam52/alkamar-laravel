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
        Schema::create('payment_lookups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stage_id');
            $table->unsignedBigInteger('created_by');
            $table->string('title');
            $table->boolean('status')->default(true);
            $table->integer('price')->default(0);
            $table->integer('month');
            $table->integer('year');

            $table->foreign('stage_id')->references('id')->on('stages')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_lookups');
    }
};
