<?php

namespace App\Http\Controllers;

use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    public function show(Request $request)
    {
        return view('admin.profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->update($data);
        AdminAudit::record('profile.updated', 'Updated administrator profile');

        return back()->with('success', 'Profile details saved.');
    }

    public function updateAvatar(Request $request)
    {
        $data = $request->validate(['avatar' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096']]);
        $user = $request->user();
        if ($user->avatar_path) Storage::disk('public')->delete($user->avatar_path);
        $path = $data['avatar']->store('admin-avatars', 'public');
        $user->update(['avatar_path' => $path]);
        AdminAudit::record('profile.avatar_updated', 'Updated administrator avatar');

        return back()->with('success', 'Profile photo updated.');
    }

    public function removeAvatar(Request $request)
    {
        $user = $request->user();
        if ($user->avatar_path) Storage::disk('public')->delete($user->avatar_path);
        $user->update(['avatar_path' => null]);
        AdminAudit::record('profile.avatar_removed', 'Removed administrator avatar');

        return back()->with('success', 'Profile photo removed.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->forceFill(['password' => $data['password']])->save();
        AdminAudit::record('profile.password_updated', 'Updated administrator password');

        return back()->with('success', 'Password updated securely.');
    }
}
