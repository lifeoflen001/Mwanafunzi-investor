<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'icon', 'short_description', 'full_description', 'image', 'learning_outcomes', 'skill_level', 'study_time', 'expected_availability', 'sort_order', 'is_published', 'status'];

    protected function casts(): array
    {
        return ['learning_outcomes' => 'array', 'is_published' => 'boolean'];
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}
