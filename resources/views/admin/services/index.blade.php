@extends('layouts.admin')
@section('title', 'Services')
@section('portal-heading', 'Services')
@section('content')
<x-admin.page-header eyebrow="Portfolio / Services" title="Services" description="Manage the service catalogue, pricing language, delivery guidance and related case studies." action-url="{{ route('admin.services.create') }}" action-label="Add service" />
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Service</span><span>Pricing</span><span>Projects</span><span>Visibility</span><span>Updated</span><span></span></div>@forelse($services as $service)<div class="admin-table-row"><span><strong>{{ $service->title }}</strong><small>{{ $service->slug }}</small></span><span>{{ $service->pricingText() }}</span><span>{{ $service->projects_count }}</span><span><x-admin.status-badge :status="$service->is_active ? 'active' : 'hidden'" /> @if($service->is_featured)<small>Featured</small>@endif</span><span>{{ $service->updated_at?->format('M j, Y') }}</span><x-admin.action-menu><a href="{{ route('admin.services.edit', $service) }}">Edit service</a><form method="post" action="{{ route('admin.services.destroy', $service) }}" data-confirm="Remove this service?">@csrf @method('delete')<button type="submit">Remove</button></form></x-admin.action-menu></div>@empty<x-admin.empty-state title="No services registered." description="Add a service when you have real offering and pricing information to publish." />@endforelse</div></x-admin.card>
{{ $services->links() }}
@endsection
