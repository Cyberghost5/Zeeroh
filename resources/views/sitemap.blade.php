<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Static pages --}}
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('events.index') }}</loc>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- Event pages --}}
    @foreach($events as $event)
    <url>
        <loc>{{ route('events.show', $event->slug) }}</loc>
        <lastmod>{{ $event->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- Organizer pages --}}
    @foreach($organizers as $organizer)
    <url>
        <loc>{{ route('organizers.show', $organizer->slug) }}</loc>
        <lastmod>{{ $organizer->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

</urlset>
