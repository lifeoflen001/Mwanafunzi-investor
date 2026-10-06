<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\TeamMember;
use App\Support\AdminAudit;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminTeamMemberController extends Controller
{
    public function index()
    {
        return view('admin.team.index', ['members' => TeamMember::orderBy('sort_order')->orderBy('name')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.team.form', [
            'member' => new TeamMember(['is_active' => true, 'robots' => 'index,follow']),
            'media' => Media::latest()->limit(100)->get(),
            'action' => route('admin.team.store'),
        ]);
    }

    public function store(Request $request)
    {
        $member = TeamMember::create($this->prepare($request, $this->validated($request)));
        AdminAudit::record('team_member.created', 'Created team member '.$member->name, $member);
        return to_route('admin.team.edit', $member)->with('success', 'Team member created.');
    }

    public function edit(TeamMember $teamMember)
    {
        return view('admin.team.form', [
            'member' => $teamMember,
            'media' => Media::latest()->limit(100)->get(),
            'action' => route('admin.team.update', $teamMember),
        ]);
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $teamMember->update($this->prepare($request, $this->validated($request, $teamMember)));
        AdminAudit::record('team_member.updated', 'Updated team member '.$teamMember->name, $teamMember);
        return back()->with('success', 'Team member updated.');
    }

    public function destroy(TeamMember $teamMember)
    {
        $teamMember->delete();
        AdminAudit::record('team_member.deleted', 'Removed team member '.$teamMember->name, $teamMember);
        return back()->with('success', 'Team member removed.');
    }

    private function prepare(Request $request, array $data): array
    {
        $data['slug'] = Str::slug($data['slug']);
        $data['bio'] = RichText::sanitize($data['bio'] ?? null);
        $data['expertise'] = collect(preg_split('/\r\n|\r|\n/', (string) $request->input('expertise')))->map(fn ($item) => trim($item))->filter()->values()->all();
        foreach (['is_active', 'is_featured'] as $field) $data[$field] = $request->boolean($field);
        return $data;
    }

    private function validated(Request $request, ?TeamMember $member = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['required', 'string', 'max:190', Rule::unique('team_members', 'slug')->ignore($member?->id)],
            'role' => ['nullable', 'string', 'max:190'],
            'department' => ['nullable', 'string', 'max:190'],
            'short_intro' => ['nullable', 'string', 'max:2000'],
            'bio' => ['nullable', 'string', 'max:30000'],
            'focus' => ['nullable', 'string', 'max:2000'],
            'expertise' => ['nullable', 'string', 'max:5000'],
            'portrait' => ['nullable', 'string', 'max:255'],
            'portrait_focal_point' => ['nullable', 'string', 'max:80'],
            'linkedin_url' => ['nullable', 'url', 'max:500'], 'instagram_url' => ['nullable', 'url', 'max:500'],
            'x_url' => ['nullable', 'url', 'max:500'], 'facebook_url' => ['nullable', 'url', 'max:500'],
            'youtube_url' => ['nullable', 'url', 'max:500'], 'tiktok_url' => ['nullable', 'url', 'max:500'],
            'github_url' => ['nullable', 'url', 'max:500'], 'website_url' => ['nullable', 'url', 'max:500'],
            'public_email' => ['nullable', 'email', 'max:190'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'], 'is_featured' => ['nullable', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'],
            'canonical_url' => ['nullable', 'url', 'max:500'], 'robots' => ['nullable', 'regex:/^(index|noindex),(follow|nofollow)$/'],
            'og_image' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
