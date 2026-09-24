<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseFaq extends Model
{
    protected $fillable = ['course_id', 'question', 'answer', 'sort_order'];
    public function course() { return $this->belongsTo(Course::class); }
}
