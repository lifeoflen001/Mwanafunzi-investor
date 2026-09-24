<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseWaitlist extends Model
{
    protected $fillable = ['course_id', 'user_id', 'name', 'email', 'consented', 'status', 'joined_at'];
    protected function casts(): array { return ['consented' => 'boolean', 'joined_at' => 'datetime']; }
    public function course() { return $this->belongsTo(Course::class); }
    public function user() { return $this->belongsTo(User::class); }
}
