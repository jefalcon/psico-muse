<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->unsignedInteger('duracion_minutos');
            $table->decimal('precio', 8, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('extracto')->nullable();
            $table->longText('cuerpo');
            $table->string('imagen')->nullable();
            $table->string('estado')->default('borrador');
            $table->timestamp('publicado_en')->nullable();
            $table->timestamps();
        });

        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->string('categoria');
            $table->text('extracto')->nullable();
            $table->longText('cuerpo');
            $table->string('imagen')->nullable();
            $table->string('estado')->default('borrador');
            $table->timestamp('publicado_en')->nullable();
            $table->timestamps();
        });

        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('telefono')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->text('notas_internas')->nullable();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email');
            $table->string('telefono')->nullable();
            $table->foreignId('servicio_id')->nullable()->nullOnDelete()->constrained('servicios');
            $table->text('mensaje');
            $table->boolean('consentimiento')->default(false);
            $table->string('estado')->default('nuevo');
            $table->string('origen')->default('web');
            $table->foreignId('cliente_id')->nullable()->nullOnDelete()->constrained('clientes');
            $table->timestamps();
        });

        Schema::create('interacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->timestamp('fecha');
            $table->string('canal');
            $table->text('nota');
            $table->timestamps();
        });

        Schema::create('entradas_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->date('fecha');
            $table->text('motivo');
            $table->longText('notas_sesion')->nullable();
            $table->longText('plan_terapeutico')->nullable();
            $table->boolean('visible_cliente')->default(false);
            $table->timestamps();
        });

        Schema::create('adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrada_historial_id')->constrained('entradas_historial')->cascadeOnDelete();
            $table->string('ruta');
            $table->string('nombre_original')->nullable();
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('tamano')->default(0);
            $table->timestamps();
        });

        Schema::create('hilos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('asunto');
            $table->foreignId('creado_por')->nullable()->nullOnDelete()->constrained('users');
            $table->timestamps();
        });

        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hilo_id')->constrained('hilos')->cascadeOnDelete();
            $table->foreignId('remitente_id')->constrained('users')->cascadeOnDelete();
            $table->text('cuerpo');
            $table->timestamp('leido_en')->nullable();
            $table->timestamps();
        });

        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios');
            $table->timestamp('inicio');
            $table->timestamp('fin');
            $table->string('estado')->default('solicitada');
            $table->text('comentario_cliente')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->timestamps();
        });

        Schema::create('ajustes', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajustes');
        Schema::dropIfExists('citas');
        Schema::dropIfExists('mensajes');
        Schema::dropIfExists('hilos');
        Schema::dropIfExists('adjuntos');
        Schema::dropIfExists('entradas_historial');
        Schema::dropIfExists('interacciones');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('articulos');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('servicios');
    }
};
