<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['exam_id']);
            $table->dropForeign(['student_id']);

            // Add the foreign key with cascade delete
            $table->foreign('exam_id')
                ->references('id')
                ->on('exams')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
        });
        Schema::table('attendances', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['lec_id']);
            $table->dropForeign(['student_id']);

            // Add the foreign key with cascade delete
            $table->foreign('lec_id')
                ->references('id')
                ->on('lectures')
                ->onDelete('cascade');
            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            //
        });
    }
};