<?php

namespace App\Models;

use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['course_id', 'title', 'description', 'order'])]
class Lesson extends Model
{
    /** @use HasFactory<LessonFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    /** @return BelongsTo<Course, $this> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return HasMany<Exercise, $this> */
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class)->orderBy('order');
    }
}
