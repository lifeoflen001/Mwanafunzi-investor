<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseWaitlist;
use App\Mail\CommerceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WaitlistController extends Controller
{
    public function store(Request $request, Course $course)
    {
        abort_unless($course->status === 'coming_soon', 404);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:190'], 'consent' => ['accepted']]);
        $entry = CourseWaitlist::firstOrCreate(['course_id' => $course->id, 'email' => strtolower($data['email'])], ['user_id' => $request->user()?->id, 'name' => $data['name'], 'consented' => true, 'status' => 'active', 'joined_at' => now()]);
        if ($entry->wasRecentlyCreated) Mail::to($entry->email)->queue(new CommerceMail('Waitlist confirmation — '.$course->title, 'You are on the waitlist', 'We will contact you when this course is ready. This message is specific to the course you selected.', route('courses.show', $course), 'View course'));
        return back()->with('success', 'You are on the course waitlist. We will contact you when the next step is ready.');
    }
}
