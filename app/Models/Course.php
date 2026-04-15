<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<Exercise, $this> */
    public function exercises(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Exercise::class)->orderBy('order');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<UserCourseProgress, $this> */
    public function userProgress(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserCourseProgress::class);
    }
}
