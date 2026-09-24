<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = ['user_id', 'course_id', 'order_id', 'entitlement_id', 'status', 'enrolled_at', 'completed_at', 'cancelled_at', 'metadata'];
    protected function casts(): array { return ['status' => EnrollmentStatus::class, 'enrolled_at' => 'datetime', 'completed_at' => 'datetime', 'cancelled_at' => 'datetime', 'metadata' => 'array']; }
    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function order() { return $this->belongsTo(Order::class); }
    public function entitlement() { return $this->belongsTo(Entitlement::class); }
}
