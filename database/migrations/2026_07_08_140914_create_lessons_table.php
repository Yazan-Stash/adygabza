<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['course_id', 'order']);
        });

        Schema::table('exercises', function (Blueprint $table) {
            $table->foreignId('lesson_id')->nullable()->constrained()->cascadeOnDelete();
            $table->index(['lesson_id', 'order']);
        });

        DB::table('courses')->orderBy('id')->each(function (object $course): void {
            $lessonId = DB::table('lessons')->insertGetId([
                'course_id' => $course->id,
                'title' => 'Lesson 1',
                'description' => null,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('exercises')
                ->where('course_id', $course->id)
                ->update(['lesson_id' => $lessonId]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lesson_id');
        });

        Schema::dropIfExists('lessons');
    }
};
