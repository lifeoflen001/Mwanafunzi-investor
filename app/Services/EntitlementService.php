<?php

namespace App\Services;

use App\Enums\EntitlementStatus;
use App\Models\Entitlement;
use App\Models\OrderItem;
use App\Models\User;

class EntitlementService
{
    public function grantForItem(OrderItem $item, ?User $user = null): ?Entitlement
    {
        $product = $item->purchasable_type === 'App\\Models\\Product' ? $item->purchasable : null;
        $course = $item->purchasable_type === 'App\\Models\\Course' ? $item->purchasable : null;
        if (! $product && ! $course) return null;
        $query = Entitlement::query()->where('order_item_id', $item->id)->where('type', $product ? 'product' : 'course');
        $entitlement = $query->first() ?: new Entitlement;
        $entitlement->fill([
            'user_id' => $user?->id ?: $item->order->user_id,
            'order_id' => $item->order_id,
            'order_item_id' => $item->id,
            'product_id' => $product?->id,
            'course_id' => $course?->id,
            'customer_email' => $item->order->customer_email,
            'type' => $product ? 'product' : 'course',
            'status' => EntitlementStatus::Active,
            'product_version_id' => $product?->versions()->first()?->id,
            'starts_at' => now(),
            'metadata' => ['purchase_version' => $product?->version, 'title' => $item->title],
        ]);
        $entitlement->save();
        return $entitlement;
    }

    public function activeForProduct(User $user, int $productId): ?Entitlement
    {
        return $user->entitlements()->where('product_id', $productId)->where('status', EntitlementStatus::Active->value)->where(function ($query) { $query->whereNull('ends_at')->orWhere('ends_at', '>', now()); })->latest()->first();
    }

    public function activeForCourse(User $user, int $courseId): ?Entitlement
    {
        return $user->entitlements()->where('course_id', $courseId)->where('status', EntitlementStatus::Active->value)->where(function ($query) { $query->whereNull('ends_at')->orWhere('ends_at', '>', now()); })->latest()->first();
    }

    public function revokeForOrder($order, string $status = EntitlementStatus::Refunded->value): void
    {
        $order->entitlements()->update(['status' => $status, 'revoked_at' => now()]);
    }
}
