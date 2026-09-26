@extends('layouts.admin')
@section('title', 'Commerce payments')
@section('content')
<x-admin.page-header eyebrow="Commerce / Payments" title="Payments" description="Inspect provider references, verification state, and captured amounts." />
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Provider / reference</span><span>Order</span><span>Amount</span><span>Status</span></div>@forelse($payments as $payment)<div class="admin-table-row"><div><strong>{{ $payment->provider }}</strong><small>{{ $payment->internal_reference }}<br>{{ $payment->provider_transaction_id }}</small></div><span>{{ $payment->order->order_number }}</span><strong>{{ app(\App\Services\MoneyFormatter::class)->format($payment->amount, $payment->currency) }}</strong><span><x-admin.status-badge :status="$payment->status->value" /><small>{{ $payment->verified_at?->format('M j, Y H:i') }}</small></span></div>@empty<x-admin.empty-state title="No payments yet." />@endforelse</div></x-admin.card>
{{ $payments->links() }}
@endsection
