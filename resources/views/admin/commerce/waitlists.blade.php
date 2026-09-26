@extends('layouts.admin')
@section('title', 'Course waitlists')
@section('content')
<x-admin.page-header eyebrow="Commerce / Courses" title="Waitlists" description="Export real course interest records and follow up with prospective students." action-url="{{ route('admin.commerce.waitlists.export') }}" action-label="Export CSV ↗" action-class="button button-secondary" />
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Course</span><span>Name</span><span>Email</span><span>Joined</span></div>@forelse($waitlists as $entry)<div class="admin-table-row"><span>{{ $entry->course->title }}</span><span>{{ $entry->name }}</span><span>{{ $entry->email }}</span><span>{{ $entry->joined_at->format('M j, Y H:i') }}</span></div>@empty<x-admin.empty-state title="No waitlist entries yet." />@endforelse</div></x-admin.card>
{{ $waitlists->links() }}
@endsection
