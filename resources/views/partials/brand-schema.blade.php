@php
    $schemaSiteUrl = \App\Support\Seo::siteUrl('/');
    $schemaOrganizationId = $schemaSiteUrl.'#organization';
    $schemaOrganization = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        '@id' => $schemaOrganizationId,
        'name' => $brandName,
        'url' => $schemaSiteUrl,
        'logo' => $brandLogoDarkUrl ?? \App\Support\BrandAssets::logo('dark'),
        'email' => $email,
        'telephone' => $phone,
        'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'TZ'],
    ];
    $schemaProfiles = \App\Support\PublicSiteData::socialLinks()->pluck('url')
        ->filter(fn ($url) => filter_var($url, FILTER_VALIDATE_URL) && parse_url($url, PHP_URL_SCHEME) === 'https')
        ->unique()->values()->all();
    if ($schemaProfiles) $schemaOrganization['sameAs'] = $schemaProfiles;
    $schemaWebsite = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => $schemaSiteUrl.'#website',
        'name' => $brandName,
        'url' => $schemaSiteUrl,
        'inLanguage' => 'en-TZ',
        'publisher' => ['@id' => $schemaOrganizationId],
    ];
    $schemaEncoding = JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
@endphp
<script type="application/ld+json">{!! json_encode($schemaOrganization, $schemaEncoding) !!}</script>
@if($includeWebsiteSchema ?? false)
<script type="application/ld+json">{!! json_encode($schemaWebsite, $schemaEncoding) !!}</script>
@endif
