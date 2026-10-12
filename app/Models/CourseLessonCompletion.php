<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLessonCompletion extends Model
{
    protected $fillable = ['user_id', 'enrollment_id', 'course_lesson_id', 'completed_at'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }
    public function lesson() { return $this->belongsTo(CourseLesson::class, 'course_lesson_id'); }
}
