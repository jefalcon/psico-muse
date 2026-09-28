<?php

namespace Tests\Feature;

use App\Models\Articulo;
use App\Models\Interaccion;
use App\Models\Lead;
use App\Models\Post;
use App\Models\Servicio;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_inicio_muestra_presentacion_y_enlaces(): void
    {
        $response = $this->get('/')->assertOk();

        foreach (['/biografia', '/servicios', '/blog', '/articulos', '/contacto', '/aviso-legal', '/privacidad'] as $url) {
            $response->assertSee($url, false);
        }

        $response->assertSee('Últimas publicaciones');
    }

    public function test_biografia_contiene_datos_profesionales(): void
    {
        $this->get('/biografia')->assertOk()
            ->assertSee('M-28417', false)
            ->assertSee('retrato.svg', false);
    }

    public function test_servicios_muestra_solo_activos(): void
    {
        $response = $this->get('/servicios')->assertOk()
            ->assertSee('Terapia individual presencial')
            ->assertDontSee('Taller de gestión del estrés');
    }

    public function test_blog_lista_y_detalle(): void
    {
        $this->get('/blog')->assertOk()->assertSee('La ansiedad no es tu enemiga');

        $post = Post::where('slug', 'ansiedad-no-es-tu-enemiga')->firstOrFail();
        $this->get('/blog/'.$post->slug)->assertOk()->assertSee($post->titulo);
    }

    public function test_blog_borrador_y_futuro_devuelven_404(): void
    {
        $this->get('/blog/borrador-perfeccionismo')->assertNotFound();

        Post::create([
            'titulo' => 'Futuro', 'slug' => 'futuro', 'cuerpo' => 'x',
            'estado' => Post::ESTADO_PUBLICADO, 'publicado_en' => now()->addWeek(),
        ]);
        $this->get('/blog/futuro')->assertNotFound();
    }

    public function test_articulos_con_filtro_y_aviso(): void
    {
        $this->get('/articulos')->assertOk()->assertSee('Terapia cognitivo-conductual');

        $response = $this->get('/articulos?categoria=trastorno')->assertOk();
        $response->assertSee('Trastorno de ansiedad generalizada');
        $response->assertDontSee('Terapia cognitivo-conductual');

        $articulo = Articulo::where('slug', 'emdr-reprocesar-recuerdos')->firstOrFail();
        $this->get('/articulos/'.$articulo->slug)->assertOk()
            ->assertSee('divulgativa y no sustituye una consulta');
    }

    public function test_articulo_borrador_devuelve_404(): void
    {
        Articulo::create([
            'titulo' => 'Borrador', 'slug' => 'borrador-art', 'categoria' => Articulo::CATEGORIA_TRASTORNO,
            'cuerpo' => 'x', 'estado' => Articulo::ESTADO_BORRADOR,
        ]);
        $this->get('/articulos/borrador-art')->assertNotFound();
    }

    private function datosContacto(): array
    {
        return [
            'nombre' => 'Nuevo Contacto',
            'email' => 'nuevo@example.test',
            'telefono' => '600000000',
            'mensaje' => 'Quiero información sobre la terapia.',
            'consentimiento' => '1',
        ];
    }

    public function test_contacto_crea_lead(): void
    {
        $this->get('/contacto')->assertOk()->assertSee('consentimiento', false);

        $this->post('/contacto', $this->datosContacto())
            ->assertRedirect('/contacto')
            ->assertSessionHas('enviado', true);

        $this->assertDatabaseHas('leads', [
            'email' => 'nuevo@example.test',
            'estado' => Lead::ESTADO_NUEVO,
            'origen' => Lead::ORIGEN_WEB,
        ]);
    }

    public function test_contacto_con_email_existente_anade_interaccion(): void
    {
        $email = 'repite@example.test';
        $this->post('/contacto', array_merge($this->datosContacto(), ['email' => $email]));
        $this->post('/contacto', array_merge($this->datosContacto(), ['email' => $email, 'mensaje' => 'Segundo mensaje']));

        $lead = Lead::where('email', $email)->firstOrFail();
        $this->assertSame(1, Lead::where('email', $email)->count());
        $this->assertSame(1, Interaccion::where('lead_id', $lead->id)->count());
    }

    public function test_contacto_valida_en_servidor(): void
    {
        $this->post('/contacto', [])->assertSessionHasErrors(['nombre', 'email', 'mensaje', 'consentimiento']);
        $this->post('/contacto', array_merge($this->datosContacto(), ['email' => 'no-es-email']))
            ->assertSessionHasErrors(['email']);
    }

    public function test_contacto_limita_5_envios_por_minuto(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/contacto', array_merge($this->datosContacto(), ['email' => "t{$i}@example.test"]))
                ->assertRedirect('/contacto');
        }

        $this->post('/contacto', array_merge($this->datosContacto(), ['email' => 't5@example.test']))
            ->assertStatus(429);
    }

    public function test_paginas_legales_y_seo(): void
    {
        $this->get('/aviso-legal')->assertOk()->assertSee('Aviso legal');
        $this->get('/privacidad')->assertOk()->assertSee('Política de privacidad');

        $post = Post::publicados()->firstOrFail();
        $articulo = Articulo::publicados()->firstOrFail();

        foreach (['/', '/biografia', '/servicios', '/blog', '/blog/'.$post->slug,
            '/articulos', '/articulos/'.$articulo->slug, '/contacto', '/aviso-legal', '/privacidad'] as $url) {
            $response = $this->get($url)->assertOk();
            $response->assertSee('<title>', false);
            $response->assertSee('name="description"', false);
        }
    }

    public function test_sitemap_incluye_publicados_y_excluye_borradores(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();
        $response->assertSee('/blog/ansiedad-no-es-tu-enemiga', false);
        $response->assertSee('/articulos/emdr-reprocesar-recuerdos', false);
        $response->assertDontSee('borrador-perfeccionismo', false);
    }

    public function test_paginas_sin_recursos_externos_ni_relleno(): void
    {
        $post = Post::publicados()->firstOrFail();
        $articulo = Articulo::publicados()->firstOrFail();

        foreach (['/', '/biografia', '/servicios', '/blog', '/blog/'.$post->slug,
            '/articulos', '/articulos/'.$articulo->slug, '/contacto', '/aviso-legal', '/privacidad'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertDoesNotMatchRegularExpression('#https?://(?!localhost|127\.0\.0\.1)#', (string) $html, "Recurso externo en {$url}");
            $this->assertStringNotContainsStringIgnoringCase('lorem ipsum', (string) $html);
        }
    }
}
