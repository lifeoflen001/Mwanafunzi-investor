@extends('layouts.account')

@section('title', 'Studio enquiries')

@section('content')
<section class="student-page-heading"><div><p class="student-eyebrow">Creative Studio / Enquiries</p><h1>Your studio enquiries.</h1><p>Track the briefs you have sent to Mwanafunzi Investor Creative Studio.</p></div><a class="student-button student-button-dark" href="{{ route('creative-studio.book') }}">Start another enquiry <span aria-hidden="true">↗</span></a></section>
<x-portal-card class="student-panel"><div class="student-table-scroll"><div class="student-table-head"><span>Reference</span><span>Service</span><span>Status</span><span>Submitted</span><span></span></div>@forelse($inquiries as $inquiry)<a class="student-table-row" href="{{ route('account.studio.inquiries.show', $inquiry) }}"><strong>{{ $inquiry->reference }}</strong><span>{{ $inquiry->service?->title ?: 'Service to be confirmed' }}</span><span><x-portal-status :status="$inquiry->statusLabel()" /></span><span>{{ $inquiry->created_at?->format('M j, Y') }}</span><b aria-hidden="true">↗</b></a>@empty<div class="student-wide-empty"><x-portal-empty icon="◫" title="No studio enquiries yet." description="When you send a Creative Studio brief, its status will appear here." :href="route('creative-studio.book')" action="Start an enquiry" /></div>@endforelse</div></x-portal-card>
{{ $inquiries->links() }}
@endsection
