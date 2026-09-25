@extends('layouts.public')
@section('title', 'Refund Policy — Mwanafunzi Investor')
@section('description', 'How Mwanafunzi Investor handles product, course, duplicate-payment and refund questions.')
@php
$sections = [
    ['id' => 'before-purchase', 'title' => 'Before purchase', 'paragraphs' => ['A product or course should publish its availability, description, price and access conditions before checkout or enrolment is enabled. If the relevant offer is marked coming soon or waitlist, a purchase is not being represented as available.']],
    ['id' => 'digital-products', 'title' => 'Digital product purchases', 'paragraphs' => ['Digital products may provide access to a download or entitlement after a successful payment. Keep your order reference and contact the desk if the access shown in your account does not match the order record.']],
    ['id' => 'course-purchases', 'title' => 'Course purchases and enrolment', 'paragraphs' => ['Course enrolment and access are linked to the course and order records created by the platform. Course-specific access conditions shown before checkout apply to that course.']],
    ['id' => 'duplicate-or-failed-payments', 'title' => 'Duplicate or failed transactions', 'paragraphs' => ['If you see a duplicate payment, an incorrect status or a payment that appears to have failed after funds were taken, send the order reference and payment-provider reference to the desk. We will check the platform record and provider response.']],
    ['id' => 'eligibility', 'title' => 'Refund questions and eligibility', 'paragraphs' => ['This policy does not invent a fixed refund window. Eligibility depends on the product or course terms shown at purchase, the state of delivery or access, the order record and the circumstances described to support.']],
    ['id' => 'access-after-refund', 'title' => 'Access after a confirmed refund', 'paragraphs' => ['Where a refund is confirmed, associated entitlements or course access may be revoked so that the account record matches the completed transaction.']],
    ['id' => 'processing', 'title' => 'Processing and provider timelines', 'paragraphs' => ['The platform can record a refund request and its status, but the time for funds to appear can depend on the payment provider and the user’s bank. Provider delays do not change the order record held by the site.']],
    ['id' => 'support', 'title' => 'Contact support', 'paragraphs' => ['Include your name, account email, order number, payment reference and a short description of the issue. Do not send full card numbers or passwords.']],
];
$related = ['terms' => 'Terms of use', 'privacy-policy' => 'Privacy policy', 'risk-disclosure' => 'Risk disclosure'];
@endphp
@section('content') @include('public.pages.legal-content', ['heading' => 'Refund policy', 'eyebrow' => 'Policies / Refunds', 'intro' => 'Products and courses will publish clear purchase and refund terms before enrolment or checkout is enabled. Until then, availability is marked as coming soon or waitlist.', 'sections' => $sections, 'related' => $related, 'supportHeading' => 'Clear purchase and refund expectations.', 'supportCopy' => 'For a payment or access question, contact the Mwanafunzi Investor desk with the relevant order details.']) @endsection
