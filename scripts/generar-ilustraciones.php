<?php
// Generador de las ilustraciones abstractas de public/images. Uso: php scripts/generar-ilustraciones.php
$out = dirname(__DIR__).'/public/images';
@mkdir($out, 0777, true);

$temas = [
    'blog-1' => ['fondo' => '#e8f1ec', 'a' => '#2f7d6d', 'b' => '#9cc3b5', 'c' => '#e0a458', 'motivo' => 'ondas'],
    'blog-2' => ['fondo' => '#f3ece1', 'a' => '#b0723a', 'b' => '#d9b98a', 'c' => '#2f7d6d', 'motivo' => 'circulos'],
    'blog-3' => ['fondo' => '#e9eef4', 'a' => '#3d6b8e', 'b' => '#a9c3d6', 'c' => '#7fb069', 'motivo' => 'arcos'],
    'art-1' => ['fondo' => '#edf3ea', 'a' => '#5b8c5a', 'b' => '#bccfae', 'c' => '#3d6b8e', 'motivo' => 'hojas'],
    'art-2' => ['fondo' => '#f4ebe4', 'a' => '#a8563c', 'b' => '#d8a583', 'c' => '#2f7d6d', 'motivo' => 'ondas'],
    'art-3' => ['fondo' => '#e7f0f0', 'a' => '#2f7d6d', 'b' => '#a9c3d6', 'c' => '#e0a458', 'motivo' => 'circulos'],
    'art-4' => ['fondo' => '#f1eee6', 'a' => '#7a6c4f', 'b' => '#c9bfa4', 'c' => '#a8563c', 'motivo' => 'arcos'],
    'hero' => ['fondo' => '#e8f1ec', 'a' => '#2f7d6d', 'b' => '#9cc3b5', 'c' => '#e0a458', 'motivo' => 'arcos'],
];

function motivo(string $tipo, string $a, string $b, string $c): string
{
    return match ($tipo) {
        'ondas' => <<<SVG
            <path d="M-20,380 C150,300 250,460 420,380 C590,300 690,460 860,380 L860,620 L-20,620 Z" fill="{$a}" opacity="0.85"/>
            <path d="M-20,430 C150,360 250,500 420,430 C590,360 690,500 860,430 L860,620 L-20,620 Z" fill="{$b}" opacity="0.7"/>
            <circle cx="640" cy="150" r="90" fill="{$c}" opacity="0.8"/>
            <circle cx="640" cy="150" r="120" fill="none" stroke="{$c}" stroke-width="3" opacity="0.5"/>
            SVG,
        'circulos' => <<<SVG
            <circle cx="200" cy="220" r="150" fill="{$a}" opacity="0.85"/>
            <circle cx="200" cy="220" r="105" fill="{$b}" opacity="0.9"/>
            <circle cx="200" cy="220" r="60" fill="{$c}" opacity="0.9"/>
            <circle cx="580" cy="400" r="180" fill="{$b}" opacity="0.5"/>
            <circle cx="700" cy="140" r="45" fill="{$c}" opacity="0.7"/>
            SVG,
        'arcos' => <<<SVG
            <rect x="120" y="80" width="240" height="440" rx="120" fill="{$a}" opacity="0.85"/>
            <rect x="400" y="140" width="240" height="380" rx="120" fill="{$b}" opacity="0.8"/>
            <circle cx="240" cy="330" r="55" fill="{$c}"/>
            <circle cx="660" cy="200" r="28" fill="{$c}" opacity="0.7"/>
            SVG,
        'hojas' => <<<SVG
            <ellipse cx="400" cy="520" rx="330" ry="60" fill="{$b}" opacity="0.6"/>
            <path d="M400,520 C380,350 300,250 180,210 C300,200 390,300 400,520 Z" fill="{$a}"/>
            <path d="M400,520 C420,350 500,250 620,210 C500,200 410,300 400,520 Z" fill="{$a}" opacity="0.75"/>
            <path d="M400,520 C395,400 370,320 320,270 C370,265 398,350 400,520 Z" fill="{$c}" opacity="0.85"/>
            <circle cx="400" cy="150" r="40" fill="{$c}" opacity="0.8"/>
            SVG,
    };
}

foreach ($temas as $nombre => $t) {
    $figuras = motivo($t['motivo'], $t['a'], $t['b'], $t['c']);
    $svg = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="800" height="600" role="img" aria-label="Ilustración decorativa">
          <rect width="800" height="600" fill="{$t['fondo']}"/>
          {$figuras}
        </svg>
        SVG;
    file_put_contents("{$out}/{$nombre}.svg", $svg);
    echo "{$nombre}.svg\n";
}
