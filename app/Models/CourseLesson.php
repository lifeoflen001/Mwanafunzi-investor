<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $fillable = ['course_module_id', 'title', 'slug', 'content', 'sort_order', 'is_published'];
    protected function casts(): array { return ['is_published' => 'boolean']; }
    public function module() { return $this->belongsTo(CourseModule::class, 'course_module_id'); }
}
