<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\EntitlementStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

class CustomerDashboardService
{
    /**
     * These statuses still require client awareness or studio follow-up.
     * Delivered, completed, declined and other terminal records remain in history.
     */
    private const OPEN_SERVICE_STATUSES = [
        'new',
        'reviewing',
        'quoted',
        'accepted',
        'scheduled',
        'in_production',
        'editing',
        'awaiting_approval',
    ];

    public function __construct(private readonly MoneyFormatter $money)
    {
    }

    public function build(User $user): array
    {
        $enrollments = $user->enrollments()
            ->with(['course.modules.lessons', 'lessonCompletions'])
            ->whereIn('status', [EnrollmentStatus::Active->value, EnrollmentStatus::Completed->value])
            ->latest('updated_at')
            ->latest('enrolled_at')
            ->get();

        $tools = $user->entitlements()
            ->where('type', 'product')
            ->where('status', EntitlementStatus::Active->value)
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->with(['product.assets', 'version'])
            ->latest()
            ->get();

        $restrictedEntitlements = $user->entitlements()
            ->whereIn('status', [EntitlementStatus::Expired->value, EntitlementStatus::Revoked->value, EntitlementStatus::Suspended->value])
            ->with(['product', 'course'])
            ->latest()
            ->limit(3)
            ->get();

        $orders = $user->orders()
            ->with('items')
            ->latest()
            ->limit(5)
            ->get();
        $ordersCount = $user->orders()->count();
        $activeServices = $user->studioInquiries()
            ->studio()
            ->whereIn('status', self::OPEN_SERVICE_STATUSES)
            ->with('service')
            ->latest()
            ->get();
        $notifications = $user->notifications()->latest()->limit(5)->get();
        $unreadNotifications = $user->unreadNotifications()->count();

        $catalogCourses = Course::published()
            ->select(['id', 'title', 'slug', 'featured_image', 'level', 'status', 'short_description', 'price', 'currency', 'sort_order'])
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        $catalogTools = Product::available()
            ->select(['id', 'name', 'slug', 'product_type', 'thumbnail', 'short_description', 'availability', 'price', 'sale_price', 'currency', 'sort_order'])
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        $completedLessons = $enrollments->sum(fn (Enrollment $enrollment) => $enrollment->completedLessonCount());
        $latestEnrollment = $enrollments->first();
        $nextLesson = $latestEnrollment ? $this->nextLesson($latestEnrollment) : null;
        $hasAccountActivity = $enrollments->isNotEmpty()
            || $tools->isNotEmpty()
            || $orders->isNotEmpty()
            || $activeServices->isNotEmpty()
            || $restrictedEntitlements->isNotEmpty();
        $profileComplete = filled($user->name) && filled($user->phone) && filled($user->country);

        return [
            'isNewCustomer' => ! $hasAccountActivity,
            'user' => $user,
            'enrollments' => $enrollments,
            'featuredEnrollment' => $latestEnrollment,
            'featuredNextLesson' => $nextLesson,
            'tools' => $tools,
            'orders' => $orders,
            'activeServices' => $activeServices,
            'notifications' => $notifications,
            'unreadNotifications' => $unreadNotifications,
            'catalogCourses' => $catalogCourses,
            'catalogTools' => $catalogTools,
            'alerts' => $this->alerts($user, $orders, $restrictedEntitlements),
            'checklist' => $this->checklist($user, $enrollments, $tools, $completedLessons, $profileComplete),
            'profileComplete' => $profileComplete,
            'completedLessons' => $completedLessons,
            'stats' => [
                'enrolled' => $enrollments->count(),
                'orders' => $ordersCount,
                'active_services' => $activeServices->count(),
                'in_progress' => $enrollments->where('status', EnrollmentStatus::Active)->count(),
                'completed' => $enrollments->where('status', EnrollmentStatus::Completed)->count(),
                'lessons' => $completedLessons,
            ],
        ];
    }

    public function formatPrice(mixed $amount, ?string $currency = 'TZS'): ?string
    {
        return $amount !== null && (string) $amount !== ''
            ? $this->money->format($amount, $currency ?: 'TZS')
            : null;
    }

    private function nextLesson(Enrollment $enrollment)
    {
        $completedIds = $enrollment->lessonCompletions->pluck('course_lesson_id');

        return $enrollment->course->modules
            ->flatMap(fn ($module) => $module->lessons->where('is_published', true))
            ->first(fn ($lesson) => ! $completedIds->contains($lesson->id));
    }

    private function checklist(User $user, Collection $enrollments, Collection $tools, int $completedLessons, bool $profileComplete): array
    {
        return [
            [
                'label' => 'Verify your email address',
                'description' => $user->hasVerifiedEmail() ? 'Your email is verified.' : 'Required for protected purchases and learning access.',
                'complete' => $user->hasVerifiedEmail(),
                'href' => $user->hasVerifiedEmail() ? route('account.security') : route('verification.notice'),
                'action' => $user->hasVerifiedEmail() ? 'Review security' : 'Verify email',
            ],
            [
                'label' => 'Complete your personal profile',
                'description' => $profileComplete ? 'Your contact details are ready.' : 'Add your phone and country so support can reach you.',
                'complete' => $profileComplete,
                'href' => route('account.profile'),
                'action' => $profileComplete ? 'Review profile' : 'Complete profile',
            ],
            [
                'label' => 'Access a course',
                'description' => $enrollments->isNotEmpty() ? 'Your course access is connected.' : 'Explore the published Financial Academy courses.',
                'complete' => $enrollments->isNotEmpty(),
                'href' => $enrollments->isNotEmpty() ? route('account.courses') : route('courses'),
                'action' => $enrollments->isNotEmpty() ? 'Open learning' : 'Explore courses',
            ],
            [
                'label' => 'Discover a digital tool',
                'description' => $tools->isNotEmpty() ? 'Your accessible tools are ready.' : 'See the tools available for deliberate practice.',
                'complete' => $tools->isNotEmpty(),
                'href' => $tools->isNotEmpty() ? route('account.downloads') : route('tools'),
                'action' => $tools->isNotEmpty() ? 'Open tools' : 'Explore tools',
            ],
            [
                'label' => 'Begin learning',
                'description' => $completedLessons > 0 ? $completedLessons.' lesson'.($completedLessons === 1 ? '' : 's').' completed.' : 'Your first completed lesson will appear here.',
                'complete' => $completedLessons > 0,
                'href' => $enrollments->isNotEmpty() ? route('account.courses') : route('courses'),
                'action' => $enrollments->isNotEmpty() ? 'Start learning' : 'Find a course',
            ],
        ];
    }

    private function alerts(User $user, Collection $orders, Collection $restrictedEntitlements): array
    {
        $alerts = [];

        if (! $user->hasVerifiedEmail()) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Email verification required',
                'body' => 'Verify your email to complete purchases and access protected learning.',
                'href' => route('verification.notice'),
                'action' => 'Verify email',
            ];
        }

        $pendingOrder = $orders->first(fn ($order) => in_array($order->payment_status?->value ?? $order->payment_status, ['pending', 'processing'], true));
        if ($pendingOrder) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Payment still processing',
                'body' => 'Order '.$pendingOrder->order_number.' has not been confirmed yet. Access will follow a verified payment.',
                'href' => route('account.orders.show', $pendingOrder),
                'action' => 'View order',
            ];
        }

        $restrictedAccess = $restrictedEntitlements->first();
        if ($restrictedAccess) {
            $title = $restrictedAccess->product?->name ?: $restrictedAccess->course?->title ?: 'An account entitlement';
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Access needs attention',
                'body' => $title.' is marked '.str($restrictedAccess->status->value)->replace('_', ' ').'. Contact the desk if this does not look right.',
                'href' => route('contact'),
                'action' => 'Contact support',
            ];
        }

        return $alerts;
    }
}
