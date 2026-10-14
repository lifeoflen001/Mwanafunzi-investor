<?php

return [
    'site_url' => env('SEO_SITE_URL', 'https://mwanafunziinvestor.com'),
    'site_name' => env('SEO_SITE_NAME', 'Mwanafunzi Investor'),
    'locale' => env('SEO_LOCALE', 'en_TZ'),
    'default_title' => 'Mwanafunzi Investor — Become a Student of Money',
    'default_description' => 'Financial education, digital software and creative systems for clearer decisions, disciplined work and useful progress.',
    // Editorial targets only: these are hypotheses for page ownership, not ranking or volume claims.
    'keyword_map' => [
        'home' => ['primary' => 'financial education Tanzania', 'secondary' => ['trading education Tanzania', 'forex education for beginners', 'elimu ya fedha Tanzania'], 'intent' => 'Brand discovery and financial education'],
        'learn' => ['primary' => 'trading education for beginners', 'secondary' => ['technical analysis education', 'trading discipline', 'portfolio diversification education', 'jifunze forex'], 'intent' => 'Informational learning path'],
        'courses' => ['primary' => 'forex trading courses Tanzania', 'secondary' => ['forex trading for beginners', 'risk management course', 'kozi ya forex'], 'intent' => 'Course discovery'],
        'tools' => ['primary' => 'trading journal and calculator tools', 'secondary' => ['trading journal', 'forex risk calculator', 'trading calculator'], 'intent' => 'Tool discovery'],
        'blog' => ['primary' => 'trading education blog', 'secondary' => ['trading psychology and discipline', 'risk management for forex traders', 'portfolio diversification education', 'market education Tanzania'], 'intent' => 'Informational research'],
        'about' => ['primary' => 'Mwanafunzi Investor Tanzania', 'secondary' => ['financial education platform Tanzania', 'student of money'], 'intent' => 'Brand trust'],
        'contact' => ['primary' => 'contact Mwanafunzi Investor', 'secondary' => ['forex education Tanzania contact', 'software services Tanzania'], 'intent' => 'Contact and enquiry'],
        'development' => ['primary' => 'business software Tanzania', 'secondary' => ['custom dashboards Tanzania', 'web applications Tanzania', 'digital systems'], 'intent' => 'Service discovery'],
        'studio' => ['primary' => 'creative studio Tanzania', 'secondary' => ['brand design Tanzania', 'creative services Tanzania'], 'intent' => 'Service discovery'],
        'services' => ['primary' => 'digital services Tanzania', 'secondary' => ['software development services', 'website design Tanzania', 'creative studio services'], 'intent' => 'Service comparison'],
        'projects' => ['primary' => 'software and design case studies Tanzania', 'secondary' => ['web development portfolio Tanzania', 'digital product case studies'], 'intent' => 'Proof and evaluation'],
    ],
];
