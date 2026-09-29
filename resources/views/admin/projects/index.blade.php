@extends('layouts.admin')
@section('title', 'Projects')
@section('portal-heading', 'Projects')
@section('content')
<x-admin.page-header eyebrow="Portfolio / Case studies" title="Projects" description="Curate selected work, structured case studies, media galleries, technologies and factual outcomes." action-url="{{ route('admin.projects.create') }}" action-label="Add project" />
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Project</span><span>Status</span><span>Services</span><span>Gallery</span><span>Updated</span><span></span></div>@forelse($projects as $project)<div class="admin-table-row"><span><strong>{{ $project->title }}</strong><small>{{ $project->slug }} @if($project->is_featured) · Featured @endif</small></span><span><x-admin.status-badge :status="$project->status" /></span><span>{{ $project->services_count }}</span><span>{{ $project->gallery_count }}</span><span>{{ $project->updated_at?->format('M j, Y') }}</span><x-admin.action-menu><a href="{{ route('admin.projects.edit', $project) }}">Edit case study</a><form method="post" action="{{ route('admin.projects.destroy', $project) }}" data-confirm="Remove this project?">@csrf @method('delete')<button type="submit">Remove</button></form></x-admin.action-menu></div>@empty<x-admin.empty-state title="No projects registered." description="Only add work that is real and ready to be documented." />@endforelse</div></x-admin.card>
{{ $projects->links() }}
@endsection
