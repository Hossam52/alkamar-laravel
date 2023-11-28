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
        Schema::create('available_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module')->unique()->nullable(false);
            $table->string('title')->unique()->nullable(false)->default('');
            $table->boolean('view')->nullable(false)->default(true);
            $table->boolean('create')->nullable(false)->default(true);
            $table->boolean('update')->nullable(false)->default(false);
            $table->boolean('delete')->nullable(false)->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_permissions');
    }
};
