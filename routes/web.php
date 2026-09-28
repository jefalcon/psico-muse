<?php

use App\Http\Controllers\AdjuntoController;
use App\Http\Controllers\Publico\ArticuloController;
use App\Http\Controllers\Publico\ContactoController;
use App\Http\Controllers\Publico\InicioController;
use App\Http\Controllers\Publico\LegalController;
use App\Http\Controllers\Publico\PostController;
use App\Http\Controllers\Publico\ServicioController;
use App\Http\Controllers\Publico\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', InicioController::class)->name('inicio');
Route::get('/biografia', [InicioController::class, 'biografia'])->name('biografia');
Route::get('/servicios', ServicioController::class)->name('servicios');
Route::get('/blog', [PostController::class, 'indice'])->name('blog.indice');
Route::get('/blog/{post:slug}', [PostController::class, 'detalle'])->name('blog.detalle');
Route::get('/articulos', [ArticuloController::class, 'indice'])->name('articulos.indice');
Route::get('/articulos/{articulo:slug}', [ArticuloController::class, 'detalle'])->name('articulos.detalle');
Route::get('/contacto', [ContactoController::class, 'formulario'])->name('contacto.formulario');
Route::post('/contacto', [ContactoController::class, 'enviar'])
    ->middleware('throttle:5,1')
    ->name('contacto.enviar');
Route::get('/aviso-legal', [LegalController::class, 'aviso'])->name('legal.aviso');
Route::get('/privacidad', [LegalController::class, 'privacidad'])->name('legal.privacidad');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/entrar', fn () => redirect('/portal/login'))->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/adjuntos/{adjunto}', [AdjuntoController::class, 'descargar'])->name('adjuntos.descargar');
});
