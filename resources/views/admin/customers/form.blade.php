@extends('layouts.admin')
@section('title', $isCreate ? 'New customer' : 'Edit customer')
@section('portal-heading', 'Customers')
@section('content')
<x-admin.page-header eyebrow="Students / Customers" title="{{ $isCreate ? 'Add a customer' : 'Edit customer account' }}" description="Manage identity and access details without exposing the customer’s password." />
<a class="admin-back-link" href="{{ $isCreate ? route('admin.customers') : route('admin.customers.show', $customer) }}">← {{ $isCreate ? 'Back to customers' : 'Back to customer' }}</a>
<div class="admin-form-card">
    <form class="admin-form" method="post" action="{{ $isCreate ? route('admin.customers.store') : route('admin.customers.update', $customer) }}" data-unsaved-warning>
        @csrf @if(!$isCreate) @method('put') @endif
        <div class="form-row">
            <label for="customer-name">Full name<input id="customer-name" name="name" value="{{ old('name', $customer->name) }}" autocomplete="name" required></label>
            <label for="customer-email">Email address<input id="customer-email" type="email" name="email" value="{{ old('email', $customer->email) }}" autocomplete="email" required></label>
        </div>
        <div class="form-row">
            <label for="customer-phone">Phone <span>(optional)</span><input id="customer-phone" name="phone" value="{{ old('phone', $customer->phone) }}" autocomplete="tel"></label>
            <label for="customer-country">Country code <span>(optional)</span><input id="customer-country" name="country" value="{{ old('country', $customer->country) }}" maxlength="2" placeholder="TZ" autocomplete="country" style="text-transform:uppercase"></label>
        </div>
        <div class="form-row">
            <label for="customer-password">Password @if(!$isCreate)<span>(leave blank to keep current)</span>@endif<div class="admin-password-field"><input id="customer-password" type="password" name="password" autocomplete="new-password" {{ $isCreate ? 'required' : '' }} minlength="10"><x-password-toggle class="admin-password-toggle" label="Show customer password" /></div></label>
            <label for="customer-password-confirmation">Confirm password<div class="admin-password-field"><input id="customer-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" {{ $isCreate ? 'required' : '' }} minlength="10"><x-password-toggle class="admin-password-toggle" label="Show password confirmation" /></div></label>
        </div>
        <label for="customer-status">Account status<select id="customer-status" name="status" required><option value="active" @selected(old('status', $customer->status ?: 'active') === 'active')>Active — can sign in</option><option value="suspended" @selected(old('status', $customer->status) === 'suspended')>Suspended — cannot sign in</option></select></label>
        <p class="field-help">Passwords are stored as hashes and are never displayed. Use at least 10 characters. Suspending keeps purchase and learning records intact while preventing sign-in.</p>
        <div class="form-actions"><button class="button button-dark" type="submit">{{ $isCreate ? 'Create customer' : 'Save customer' }} <span aria-hidden="true">↗</span></button></div>
    </form>
</div>
@endsection
