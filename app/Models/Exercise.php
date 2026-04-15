<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['course_id', 'type', 'prompt', 'answer', 'options', 'explanation', 'order', 'metadata'])]
class Exercise extends Model
{
    use HasFactory;
    protected function casts(): array
    {
        return [
            'answer' => 'array',
            'options' => 'array',
            'metadata' => 'array',
            'order' => 'integer',
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Course, $this> */
    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
