@extends('layouts.admin')
@section('title', 'Commerce enrollments')
@section('content')
<x-admin.page-header eyebrow="Commerce / Courses" title="Enrollments" description="Monitor course access granted to students." />
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Student</span><span>Course</span><span>Order</span><span>Status</span></div>@forelse($enrollments as $enrollment)<div class="admin-table-row"><span>{{ $enrollment->user->email }}</span><span>{{ $enrollment->course->title }}</span><span>{{ $enrollment->order?->order_number ?: 'Free enrollment' }}</span><x-admin.status-badge :status="$enrollment->status->value" /></div>@empty<x-admin.empty-state title="No enrollments yet." />@endforelse</div></x-admin.card>
{{ $enrollments->links() }}
@endsection
