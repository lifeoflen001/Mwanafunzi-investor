<?php

namespace App\Http\Controllers;

use App\Enums\EnrollmentStatus;
use App\Models\CourseLesson;
use App\Models\CourseLessonCompletion;
use App\Models\Enrollment;
use App\Models\Order;
use App\Services\DownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerAccountController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $enrollments = $user->enrollments()
            ->with(['course.modules.lessons', 'lessonCompletions'])
            ->whereIn('status', [EnrollmentStatus::Active, EnrollmentStatus::Completed])
            ->latest()
            ->get();
        $orders = $user->orders()->withCount('items')->latest()->limit(5)->get();
        $downloads = $user->entitlements()->where('type', 'product')->where('status', 'active')->with('product.assets')->latest()->get();
        return view('account.dashboard', [
            'orders' => $orders,
            'orderCount' => $user->orders()->count(),
            'downloads' => $downloads,
            'downloadCount' => $downloads->count(),
            'enrollments' => $enrollments,
            'courseCount' => $enrollments->count(),
            'hasActivity' => $enrollments->isNotEmpty() || $orders->isNotEmpty() || $downloads->isNotEmpty(),
        ]);
    }
    public function orders(Request $request) { return view('account.orders', ['orders' => $request->user()->orders()->withCount('items')->latest()->paginate(15)]); }
    public function order(Order $order) { $this->owner($order); return view('account.order', compact('order')); }
    public function downloads(Request $request) { return view('account.downloads', ['entitlements' => $request->user()->entitlements()->where('type', 'product')->where('status', 'active')->with(['product.assets', 'version'])->latest()->get(), 'downloads' => app(DownloadService::class)]); }
    public function courses(Request $request)
    {
        return view('account.courses', [
            'enrollments' => $request->user()->enrollments()
                ->with(['course.modules.lessons', 'lessonCompletions'])
                ->whereIn('status', [EnrollmentStatus::Active, EnrollmentStatus::Completed])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function course(Request $request, Enrollment $enrollment)
    {
        $this->authorizeEnrollment($request, $enrollment);
        $enrollment->load(['course.modules.lessons', 'lessonCompletions']);

        return view('account.course', compact('enrollment'));
    }

    public function lesson(Request $request, Enrollment $enrollment, CourseLesson $lesson)
    {
        $this->authorizeEnrollment($request, $enrollment);
        $lesson->load('module.course');
        abort_unless($lesson->is_published && $lesson->module?->course_id === $enrollment->course_id, 404);

        $enrollment->load(['course.modules.lessons', 'lessonCompletions']);
        $lessons = $enrollment->course->modules
            ->flatMap(fn ($module) => $module->lessons->where('is_published', true))
            ->values();
        $lessonIndex = $lessons->search(fn ($item) => $item->is($lesson));

        return view('account.lesson', [
            'enrollment' => $enrollment,
            'lesson' => $lesson,
            'previousLesson' => $lessonIndex !== false && $lessonIndex > 0 ? $lessons->get($lessonIndex - 1) : null,
            'nextLesson' => $lessonIndex !== false ? $lessons->get($lessonIndex + 1) : null,
            'isComplete' => $enrollment->lessonCompletions->contains('course_lesson_id', $lesson->id),
        ]);
    }

    public function completeLesson(Request $request, Enrollment $enrollment, CourseLesson $lesson)
    {
        $this->authorizeEnrollment($request, $enrollment);
        $lesson->load('module');
        abort_unless($lesson->is_published && $lesson->module?->course_id === $enrollment->course_id, 404);

        CourseLessonCompletion::firstOrCreate(
            ['user_id' => $request->user()->id, 'enrollment_id' => $enrollment->id, 'course_lesson_id' => $lesson->id],
            ['completed_at' => now()],
        );

        $enrollment->load(['course.modules.lessons', 'lessonCompletions']);
        $totalLessons = $enrollment->totalPublishedLessons();
        if ($totalLessons > 0 && $enrollment->completedLessonCount() >= $totalLessons && $enrollment->status !== EnrollmentStatus::Completed) {
            $enrollment->update(['status' => EnrollmentStatus::Completed, 'completed_at' => now()]);
        }

        return back()->with('success', 'Lesson marked complete.');
    }
    public function profile(Request $request) { return view('account.profile', ['user' => $request->user()]); }
    public function updateProfile(Request $request) { $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'phone' => ['nullable', 'string', 'max:40'], 'country' => ['nullable', 'string', 'size:2']]); $request->user()->update([...$data, 'country' => strtoupper($data['country'] ?? '')]); return back()->with('success', 'Profile updated.'); }
    public function updateAvatar(Request $request)
    {
        $data = $request->validate(['avatar' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096']]);
        $user = $request->user();
        if ($user->avatar_path) Storage::disk('public')->delete($user->avatar_path);
        $user->update(['avatar_path' => $data['avatar']->store('customer-avatars', 'public')]);
        return back()->with('success', 'Profile photo updated.');
    }
    public function removeAvatar(Request $request)
    {
        $user = $request->user();
        if ($user->avatar_path) Storage::disk('public')->delete($user->avatar_path);
        $user->update(['avatar_path' => null]);
        return back()->with('success', 'Profile photo removed.');
    }
    public function security() { return view('account.security'); }
    public function updatePassword(Request $request) { $data = $request->validate(['current_password' => ['required', 'current_password'], 'password' => ['required', 'confirmed', 'min:10']]); $request->user()->update(['password' => $data['password']]); return back()->with('success', 'Password updated.'); }
    public function receipt(Order $order) { $this->owner($order); return view('commerce.receipt', compact('order')); }
    private function authorizeEnrollment(Request $request, Enrollment $enrollment): void
    {
        abort_unless($enrollment->user_id === $request->user()->id && in_array($enrollment->status, [EnrollmentStatus::Active, EnrollmentStatus::Completed], true), 403);
    }
    private function owner(Order $order): void { abort_unless($order->user_id === auth()->id(), 403); }
}
