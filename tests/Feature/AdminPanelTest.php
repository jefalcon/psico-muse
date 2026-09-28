<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function cliente(): User
    {
        $user = User::factory()->create(['role' => 'cliente']);
        Cliente::create(['user_id' => $user->id]);

        return $user;
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_cliente_recibe_403_en_admin(): void
    {
        $this->actingAs($this->cliente());

        $this->get('/admin')->assertForbidden();
        $this->get('/admin/servicios')->assertForbidden();
    }

    public function test_admin_ve_el_escritorio(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin')->assertOk();
    }

    public function test_admin_ve_los_listados(): void
    {
        $this->actingAs($this->admin());

        foreach ([
            '/admin/servicios',
            '/admin/posts',
            '/admin/articulos',
            '/admin/clientes',
            '/admin/leads',
            '/admin/historial',
            '/admin/hilos',
            '/admin/citas',
            '/admin/agenda',
            '/admin/horario-laboral',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_admin_ve_los_formularios_de_creacion(): void
    {
        $this->actingAs($this->admin());

        foreach ([
            '/admin/servicios/create',
            '/admin/posts/create',
            '/admin/articulos/create',
            '/admin/clientes/create',
            '/admin/leads/create',
            '/admin/historial/create',
            '/admin/hilos/create',
            '/admin/citas/create',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
