<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['title', 'description', 'language_from', 'language_to', 'is_published'])]
class Course extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    /** @return HasMany<Lesson, $this> */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /** @return HasManyThrough<Exercise, Lesson, $this> */
    public function exercises(): HasManyThrough
    {
        return $this->hasManyThrough(Exercise::class, Lesson::class)
            ->orderBy('lessons.order')
            ->orderBy('exercises.order');
    }

    /** @return HasMany<UserCourseProgress, $this> */
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserCourseProgress::class);
    }
}
