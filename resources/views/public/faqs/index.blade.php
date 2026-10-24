@extends('layouts.public')
@section('title', 'Frequently asked questions — Mwanafunzi Investor')
@section('description', 'Find clear answers about Mwanafunzi Investor courses, learning paths, digital tools and platform services.')
@section('structured_data')
    @if($groups->isNotEmpty())
        @php($faqItems = $groups->flatten(1)->map(fn ($faq) => ['@type' => 'Question', 'name' => $faq['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($faq['answer'])]])->values()->all())
        <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faqItems], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @endif
@endsection
@section('content')
<x-public-hero class="public-page-hero" eyebrow="Questions, made clear" title="Frequently asked questions." summary="A practical place to find answers about learning, tools and the work we do." :breadcrumbs="[['label' => 'Home', 'url' => route('home')]]" />
<section class="platform-section faq-directory" aria-labelledby="faq-directory-heading">
    <div class="container">
        <div class="split-heading">
            <div>
                <p class="eyebrow"><span class="eyebrow-line"></span> Answers</p>
                <h2 id="faq-directory-heading">Start with the question you have.</h2>
            </div>
            <p class="body-copy">These answers are connected to currently published platform content and are kept deliberately direct.</p>
        </div>

        @forelse($groups as $target => $faqs)
            <section class="faq-directory-group" aria-labelledby="faq-group-{{ str($target)->slug() }}">
                <div class="faq-directory-group-heading">
                    <p class="eyebrow">{{ $faqs->first()['type'] }}</p>
                    <h3 id="faq-group-{{ str($target)->slug() }}">{{ $target }}</h3>
                </div>
                <div class="module-list">
                    @foreach($faqs as $faq)
                        <details>
                            <summary>{{ $faq['question'] }} <span aria-hidden="true">+</span></summary>
                            <p>{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="empty-state">
                <span class="empty-index">FAQS</span>
                <h2>The first answers are being prepared.</h2>
                <p>Published questions and answers will appear here as the relevant course, tool or platform content is reviewed.</p>
                <a class="button button-dark" href="{{ route('contact') }}">Ask a question <span aria-hidden="true">↗</span></a>
            </div>
        @endforelse
    </div>
</section>
@endsection
