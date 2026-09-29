# Consulta Demo — Web y gestión para consulta de psicología

Aplicación Laravel + Filament para una psicóloga sanitaria ficticia:
web pública (Blade + Tailwind), panel interno de gestión (`/admin`)
y portal de pacientes (`/portal`). Demo con datos ficticios pero
comportamiento de producto real.

## Requisitos

- PHP 8.3 con extensiones `sqlite3`, `mbstring`, `gd`, `intl`, `zip`, `curl`, `xml`
- Composer
- Node 20+ y npm

## Instalación y arranque desde un clon limpio

Ejecutar en orden, desde la raíz del repositorio:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

La aplicación queda disponible en http://localhost:8000.

- Web pública: http://localhost:8000
- Panel interno (admin): http://localhost:8000/admin
- Portal del paciente: http://localhost:8000/portal

No hace falta ningún proceso adicional (ni colas, ni scheduler, ni Vite en
modo dev): la cola es `sync` y los assets se sirven compilados.

## Credenciales de demostración

| Rol     | Email              | Contraseña |
|---------|--------------------|------------|
| Admin   | admin@demo.test    | password   |
| Cliente | cliente1@demo.test | password   |
| Cliente | cliente2@demo.test | password   |

## Tests

```bash
php artisan test              # 54 tests, 366 aserciones
vendor/bin/phpstan analyse    # Larastan nivel 5, sin errores
```

Calidad verificada: 54 tests en verde, Larastan nivel 5 sin errores y
verificación externa de auditoría con Playwright 91/91 sin peticiones a
dominios externos (scripts propios de la auditoría, no incluidos en el
repo al depender de su fecha y rutas de ejecución).

## Decisiones de diseño

- **Dos paneles Filament**: `admin` (`/admin`, rol admin) y `portal`
  (`/portal`, rol cliente). El acceso se controla con
  `User::canAccessPanel()`; un usuario autenticado en el panel equivocado
  recibe 403.
- **Lógica en servicios** (`app/Services`): `Horario` (horario laboral y
  huecos libres), `GestorCitas` (solicitar/confirmar/rechazar/reprogramar
  con todas las validaciones), `GestorMensajes` (hilos, respuestas y envío
  a varios) y `GestorClientes` (alta de clientes y conversión de leads).
  Los recursos Filament y los tests usan estos servicios, así las reglas
  no dependen de la interfaz.
- **Horario laboral configurable** en *Horario laboral* (`/admin`), con el
  valor por defecto lunes a viernes 9:00–14:00 y 16:00–20:00. Los huecos
  se calculan en rejilla de 15 minutos dentro de los tramos.
- **Citas**: dos citas confirmadas (o completadas) nunca se solapan; las
  solicitudes pendientes no bloquean huecos hasta confirmarse. El rechazo
  exige motivo. Los avisos al cliente llegan por email (al log) y como
  notificación en el portal.
- **Adjuntos del historial** en el disco `privado` (`storage/app/privado`),
  servidos solo por la ruta `/adjuntos/{id}` con autorización: la admin ve
  todo; cada cliente solo los adjuntos de sus entradas marcadas como
  visibles.
- **Comunicaciones**: el *envío a varios* crea un hilo individual por
  cliente; ningún cliente ve a los demás destinatarios.
- **Imágenes**: todas originales y dentro del repositorio
  (`public/images/*.svg`, ver `CREDITS.md`). Sin fuentes ni scripts
  externos: el tema usa pilas de fuentes del sistema y Filament sirve sus
  assets en local.
- **Correos**: `MAIL_MAILER=log`; todos los avisos (contraseñas, mensajes,
  citas) quedan registrados en `storage/logs/laravel.log`.
