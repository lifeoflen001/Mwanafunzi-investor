@extends('layouts.public')
@section('title', $page?->seo_title ?: 'Disclaimer — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'The educational scope and limits of Mwanafunzi Investor content, tools and external services.')
@php
$sections = [
    ['id' => 'educational-only', 'title' => 'Educational content only', 'paragraphs' => ['Mwanafunzi Investor publishes educational material about markets, systems, probability, risk and disciplined decision-making. It is intended to support learning, not to replace individual advice.']],
    ['id' => 'not-advice', 'title' => 'Not financial advice or a recommendation', 'paragraphs' => ['Nothing on this website should be understood as a recommendation to buy or sell an asset, open an account or use a particular provider.']],
    ['id' => 'market-uncertainty', 'title' => 'Market uncertainty', 'paragraphs' => ['Markets are uncertain. Information can become outdated, examples can be incomplete and an approach that suits one person may not suit another. No outcome is guaranteed.']],
    ['id' => 'tools', 'title' => 'Tools and calculators', 'paragraphs' => ['Tools and calculators are educational aids. Check the inputs, formulas, assumptions and outputs yourself, and do not treat a result as a promise or instruction.']],
    ['id' => 'third-party-services', 'title' => 'Third-party data and services', 'paragraphs' => ['Links, charts, payment providers, brokers and other third-party services may have separate terms, data and risks. Mwanafunzi Investor is not responsible for changes or failures outside its control.']],
    ['id' => 'your-decision', 'title' => 'Your decision responsibility', 'paragraphs' => ['You remain responsible for your decisions, account security, risk, records and any professional advice you choose to obtain. Consider your own circumstances before acting.']],
    ['id' => 'changes', 'title' => 'Changes', 'paragraphs' => ['This disclaimer may be updated as the platform and its content develop. The current version is the one published on this page.']],
];
$related = ['risk-disclosure' => 'Risk disclosure', 'terms' => 'Terms of use', 'privacy-policy' => 'Privacy policy'];
@endphp
@section('content') @include('public.pages.legal-content', ['heading' => 'Educational disclaimer', 'eyebrow' => 'Policies / Disclaimer', 'intro' => 'Nothing on this website should be understood as a recommendation to buy or sell an asset. Consider your own circumstances and seek independent professional advice where appropriate.', 'sections' => $sections, 'related' => $related, 'supportHeading' => 'Questions about the scope of this content?', 'supportCopy' => 'Contact the Mwanafunzi Investor desk if you need clarification about an educational page or tool.']) @endsection
