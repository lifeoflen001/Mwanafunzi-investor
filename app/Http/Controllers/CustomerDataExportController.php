<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerDataExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user()->load([
            'orders.items',
            'enrollments.course',
            'studioInquiries.service',
            'articleReactions.article',
            'comments.article',
        ]);

        $payload = [
            'exported_at' => now()->toIso8601String(),
            'account' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                'created_at' => $user->created_at?->toIso8601String(),
            ],
            'orders' => $user->orders->map(fn ($order) => [
                'order_number' => $order->order_number,
                'status' => $order->payment_status?->value ?? (string) $order->payment_status,
                'total' => $order->total,
                'currency' => $order->currency,
                'created_at' => $order->created_at?->toIso8601String(),
                'items' => $order->items->pluck('title')->values(),
            ])->values(),
            'course_enrollments' => $user->enrollments->map(fn ($enrollment) => [
                'course' => $enrollment->course?->title,
                'status' => $enrollment->status?->value ?? (string) $enrollment->status,
                'enrolled_at' => $enrollment->enrolled_at?->toIso8601String(),
            ])->values(),
            'studio_inquiries' => $user->studioInquiries->map(fn ($inquiry) => [
                'reference' => $inquiry->reference,
                'service' => $inquiry->service?->title,
                'status' => $inquiry->status,
                'created_at' => $inquiry->created_at?->toIso8601String(),
            ])->values(),
            'saved_articles' => $user->articleReactions->where('type', 'like')->map(fn ($reaction) => [
                'title' => $reaction->article?->title,
                'slug' => $reaction->article?->slug,
                'saved_at' => $reaction->created_at?->toIso8601String(),
            ])->values(),
            'comments' => $user->comments->map(fn ($comment) => [
                'article' => $comment->article?->title,
                'body' => $comment->body,
                'status' => $comment->status,
                'created_at' => $comment->created_at?->toIso8601String(),
            ])->values(),
        ];

        return response()->streamDownload(function () use ($payload): void {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }, 'mwanafunzi-investor-data-export-'.now()->format('Y-m-d').'.json', [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Cache-Control' => 'no-store, private',
        ]);
    }
}
