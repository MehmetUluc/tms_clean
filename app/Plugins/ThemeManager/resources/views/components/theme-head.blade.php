{{-- SEO meta etiketleri --}}
<title>{{ $themeSettings['site_name'] ?? config('app.name') }}</title>
<meta name="description" content="{{ $seoSettings['seo_meta_description'] ?? '' }}">
<meta name="keywords" content="{{ $seoSettings['seo_meta_keywords'] ?? '' }}">

{{-- Sosyal medya meta etiketleri --}}
<meta property="og:title" content="{{ $themeSettings['site_name'] ?? config('app.name') }}">
<meta property="og:description" content="{{ $seoSettings['seo_meta_description'] ?? '' }}">
<meta property="og:image" content="{{ asset($seoSettings['seo_og_image'] ?? '') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">

{{-- Twitter Card meta etiketleri --}}
<meta name="twitter:card" content="{{ $seoSettings['seo_twitter_card'] ?? 'summary_large_image' }}">
<meta name="twitter:title" content="{{ $themeSettings['site_name'] ?? config('app.name') }}">
<meta name="twitter:description" content="{{ $seoSettings['seo_meta_description'] ?? '' }}">
<meta name="twitter:image" content="{{ asset($seoSettings['seo_og_image'] ?? '') }}">

{{-- Tema CSS değişkenleri --}}
<link rel="stylesheet" href="{{ route('theme.css') }}">

{{-- Favicon --}}
<link rel="icon" href="{{ asset($themeSettings['favicon'] ?? '/favicon.ico') }}">

{{-- Font yükleme --}}
@if(isset($themeSettings['typography_heading_font']) || isset($themeSettings['typography_body_font']))
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
@endif

@if(isset($themeSettings['typography_heading_font']))
    @php
        $headingFont = preg_replace('/,.*$/', '', $themeSettings['typography_heading_font']);
        $headingFontFormatted = str_replace(' ', '+', $headingFont);
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ $headingFontFormatted }}:wght@400;500;600;700&display=swap" rel="stylesheet">
@endif

@if(isset($themeSettings['typography_body_font']) && $themeSettings['typography_body_font'] !== ($themeSettings['typography_heading_font'] ?? null))
    @php
        $bodyFont = preg_replace('/,.*$/', '', $themeSettings['typography_body_font']);
        $bodyFontFormatted = str_replace(' ', '+', $bodyFont);
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ $bodyFontFormatted }}:wght@400;500;600&display=swap" rel="stylesheet">
@endif