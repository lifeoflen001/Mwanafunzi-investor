<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Entitlement;
use App\Models\Order;
use App\Models\User;

class EnrollmentService
{
    public function enrollFree(User $user, Course $course): Enrollment
    {
        return Enrollment::firstOrCreate(['user_id' => $user->id, 'course_id' => $course->id], ['status' => EnrollmentStatus::Active, 'enrolled_at' => now()]);
    }

    public function activateFromEntitlement(Entitlement $entitlement): ?Enrollment
    {
        if (! $entitlement->course_id || ! $entitlement->user_id) return null;
        return Enrollment::updateOrCreate(['user_id' => $entitlement->user_id, 'course_id' => $entitlement->course_id], ['order_id' => $entitlement->order_id, 'entitlement_id' => $entitlement->id, 'status' => EnrollmentStatus::Active, 'enrolled_at' => now()]);
    }

    public function revokeForOrder(Order $order): void
    {
        $order->load('items');
        $courseIds = $order->items->where('purchasable_type', Course::class)->pluck('purchasable_id');
        if ($courseIds->isNotEmpty()) Enrollment::where('order_id', $order->id)->whereIn('course_id', $courseIds)->update(['status' => EnrollmentStatus::Refunded, 'cancelled_at' => now()]);
    }
}
