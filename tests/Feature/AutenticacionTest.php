<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_paginas_de_login_y_recuperacion_cargan(): void
    {
        $this->get('/admin/login')->assertOk();
        $this->get('/portal/login')->assertOk();
        $this->get('/admin/password-reset/request')->assertOk();
        $this->get('/portal/password-reset/request')->assertOk();
    }

    public function test_admin_entra_en_admin_y_no_en_portal(): void
    {
        Livewire::test(Login::class, ['panel' => 'admin'])
            ->set('data.email', 'admin@demo.test')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticated();

        $admin = User::where('email', 'admin@demo.test')->firstOrFail();
        $this->assertTrue($admin->canAccessPanel(\Filament\Facades\Filament::getPanel('admin')));
        $this->assertFalse($admin->canAccessPanel(\Filament\Facades\Filament::getPanel('portal')));
    }

    public function test_cliente_entra_en_portal_y_no_en_admin(): void
    {
        \Filament\Facades\Filament::setCurrentPanel('portal');

        Livewire::test(Login::class)
            ->set('data.email', 'cliente1@demo.test')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticated();

        $cliente = User::where('email', 'cliente1@demo.test')->firstOrFail();
        $this->assertTrue($cliente->canAccessPanel(\Filament\Facades\Filament::getPanel('portal')));
        $this->assertFalse($cliente->canAccessPanel(\Filament\Facades\Filament::getPanel('admin')));
    }

    public function test_recuperacion_envia_email_al_log(): void
    {
        \Illuminate\Support\Facades\Notification::fake();
        \Filament\Facades\Filament::setCurrentPanel('portal');

        Livewire::test(\Filament\Auth\Pages\PasswordReset\RequestPasswordReset::class)
            ->set('data.email', 'cliente1@demo.test')
            ->call('request')
            ->assertHasNoErrors();

        $cliente = User::where('email', 'cliente1@demo.test')->firstOrFail();
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $cliente,
            \Filament\Auth\Notifications\ResetPassword::class
        );
    }
}
