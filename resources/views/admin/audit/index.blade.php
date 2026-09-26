@extends('layouts.admin')
@section('title', 'Activity log')
@section('portal-heading', 'Activity log')
@section('content')
<x-admin.page-header eyebrow="System / Audit" title="Activity log" description="Recent changes to important CMS records and site configuration." />
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Change</span><span>Editor</span><span>Record</span><span>When</span></div>@forelse($logs as $log)<div class="admin-table-row"><span><strong>{{ $log->summary }}</strong><small>{{ $log->action }}</small></span><span>{{ $log->user?->name ?: 'System' }}</span><span>{{ $log->auditable_type ? class_basename($log->auditable_type).' #'.$log->auditable_id : '—' }}</span><span>{{ $log->created_at?->format('M j, Y H:i') }}</span></div>@empty<x-admin.empty-state title="No activity recorded yet." />@endforelse</div></x-admin.card>{{ $logs->links() }}
@endsection
