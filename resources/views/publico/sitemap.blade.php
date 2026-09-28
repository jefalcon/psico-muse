@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $url)
    <url><loc>{{ $url }}</loc></url>
@endforeach
@foreach ($posts as $post)
    <url><loc>{{ route('blog.detalle', $post) }}</loc><lastmod>{{ $post->publicado_en?->toDateString() }}</lastmod></url>
@endforeach
@foreach ($articulos as $articulo)
    <url><loc>{{ route('articulos.detalle', $articulo) }}</loc><lastmod>{{ $articulo->publicado_en?->toDateString() }}</lastmod></url>
@endforeach
</urlset>
