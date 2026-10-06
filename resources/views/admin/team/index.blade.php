@extends('layouts.admin')
@section('title', 'Team')
@section('portal-heading', 'Team')
@section('content')
<x-admin.page-header eyebrow="Platform / Team" title="Team members" description="Manage the people shown on the About page and their public profiles." action-url="{{ route('admin.team.create') }}" action-label="Add team member" />
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Member</span><span>Role</span><span>Visibility</span><span>Order</span><span>Updated</span><span></span></div>@forelse($members as $member)<div class="admin-table-row"><span><strong>{{ $member->name }}</strong><small>{{ $member->slug }} @if($member->is_featured) · Featured @endif</small></span><span>{{ $member->role ?: 'Not set' }}</span><span><x-admin.status-badge :status="$member->is_active ? 'active' : 'hidden'" /></span><span>{{ $member->sort_order }}</span><span>{{ $member->updated_at?->format('M j, Y') }}</span><x-admin.action-menu><a href="{{ route('admin.team.edit', $member) }}">Edit profile</a><form method="post" action="{{ route('admin.team.destroy', $member) }}" data-confirm="Remove this team member?">@csrf @method('delete')<button type="submit">Remove</button></form></x-admin.action-menu></div>@empty<x-admin.empty-state title="No team members registered." description="Add a member when their public profile is ready." />@endforelse</div></x-admin.card>
{{ $members->links() }}
@endsection
