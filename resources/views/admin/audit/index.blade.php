@extends('layouts.admin')
@section('title', 'Activity log')
@section('portal-heading', 'Activity log')
@section('content')
<div class="admin-heading"><div><p class="eyebrow">System / Audit</p><h1>Activity log</h1><p>Recent changes to important CMS records and site configuration.</p></div></div>
<div class="admin-table"><div class="admin-table-head"><span>Change</span><span>Editor</span><span>Record</span><span>When</span></div>@forelse($logs as $log)<div class="admin-table-row"><span><strong>{{ $log->summary }}</strong><small>{{ $log->action }}</small></span><span>{{ $log->user?->name ?: 'System' }}</span><span>{{ $log->auditable_type ? class_basename($log->auditable_type).' #'.$log->auditable_id : '—' }}</span><span>{{ $log->created_at?->format('M j, Y H:i') }}</span></div>@empty<div class="empty-state compact"><p>No activity recorded yet.</p></div>@endforelse</div>{{ $logs->links() }}
@endsection
