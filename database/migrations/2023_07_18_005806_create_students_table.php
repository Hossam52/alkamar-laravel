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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stage_id');

            $table->string('code')->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('school')->nullable(true)->default('');
            $table->string('father_phone')->nullable(true)->default('');
            $table->string('mother_phone')->nullable(true)->default('');
            $table->string('student_phone')->nullable(true)->default('');
            $table->string('whatsapp')->nullable(true)->default('');
            $table->string('address')->nullable(true)->default('');
            $table->enum('gender',['male','female'])->nullable(false);
            $table->binary('qr_code')->nullable(false);
            $table->timestamps();

            $table->foreign('stage_id')->references('id')->on('stages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
