<?php

namespace Database\Seeders;

use App\Models\Adjunto;
use App\Models\Articulo;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\EntradaHistorial;
use App\Models\Hilo;
use App\Models\Interaccion;
use App\Models\Lead;
use App\Models\Mensaje;
use App\Models\Post;
use App\Models\Servicio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->crearUsuarios();
        $this->crearServicios();
        $this->crearBlog();
        $this->crearArticulos();
        $this->crearLeads();
        $this->crearHistorial();
        $this->crearHilos();
        $this->crearCitas();
    }

    private function crearUsuarios(): void
    {
        User::create(['name' => 'Elena Márquez', 'email' => 'admin@demo.test', 'password' => Hash::make('password'), 'role' => 'admin']);

        $c1 = User::create(['name' => 'Lucía Fernández', 'email' => 'cliente1@demo.test', 'password' => Hash::make('password'), 'role' => 'cliente']);
        Cliente::create(['user_id' => $c1->id, 'telefono' => '612345678', 'fecha_nacimiento' => '1990-04-12', 'notas_internas' => 'Derivada desde el formulario web. Ansiedad generalizada. Buena adherencia.']);

        $c2 = User::create(['name' => 'Marcos Ruiz', 'email' => 'cliente2@demo.test', 'password' => Hash::make('password'), 'role' => 'cliente']);
        Cliente::create(['user_id' => $c2->id, 'telefono' => '623456789', 'fecha_nacimiento' => '1985-11-03', 'notas_internas' => 'Duelo reciente. Prefiere sesiones por la tarde.']);
    }

    private function crearServicios(): void
    {
        Servicio::create([
            'nombre' => 'Primera entrevista informativa',
            'descripcion' => 'Treinta minutos gratuitos para conocernos, entender tu motivo de consulta y valorar si puedo ayudarte. Sin compromiso y con total confidencialidad.',
            'duracion_minutos' => 30, 'precio' => null, 'activo' => true, 'orden' => 1,
        ]);
        Servicio::create([
            'nombre' => 'Terapia individual presencial',
            'descripcion' => 'Sesiones de 50 minutos en la consulta de Chamberí (Madrid). Trabajamos ansiedad, depresión, duelo, autoestima, estrés laboral y dificultades en las relaciones. Incluye plan de objetivos y revisión periódica.',
            'duracion_minutos' => 50, 'precio' => 60, 'activo' => true, 'orden' => 2,
        ]);
        Servicio::create([
            'nombre' => 'Terapia individual online',
            'descripcion' => 'El mismo trabajo terapéutico por videollamada segura, estés donde estés. Ideal si vives fuera de Madrid o tienes dificultades de desplazamiento. Solo necesitas un lugar tranquilo y buena conexión.',
            'duracion_minutos' => 50, 'precio' => 55, 'activo' => true, 'orden' => 3,
        ]);
        Servicio::create([
            'nombre' => 'Programa de 8 sesiones para la ansiedad',
            'descripcion' => 'Programa estructurado de ocho sesiones para aprender a manejar la ansiedad: psicoeducación, respiración y relajación, exposición gradual y reestructuración de pensamientos. Incluye materiales de apoyo entre sesiones.',
            'duracion_minutos' => 50, 'precio' => 400, 'activo' => true, 'orden' => 4,
        ]);
        Servicio::create([
            'nombre' => 'Taller de gestión del estrés (grupo)',
            'descripcion' => 'Taller grupal de cuatro semanas, actualmente pausado. Escríbeme si te interesa y te aviso cuando se abra un nuevo grupo.',
            'duracion_minutos' => 90, 'precio' => 120, 'activo' => false, 'orden' => 5,
        ]);
    }

    private function crearBlog(): void
    {
        Post::create([
            'titulo' => 'La ansiedad no es tu enemiga: qué intenta decirte',
            'slug' => 'ansiedad-no-es-tu-enemiga',
            'extracto' => 'Sentir ansiedad es incómodo, pero también es información. Aprende a escucharla en lugar de luchar contra ella.',
            'cuerpo' => <<<'HTML'
                <p>Muchas personas llegan a consulta con la misma petición: «quiero dejar de sentir ansiedad». Es comprensible: el nudo en el estómago, el corazón acelerado y la mente que no para son experiencias muy desagradables. Pero la ansiedad, en sí misma, no es el problema: es una señal.</p>
                <h2>La ansiedad como mensajera</h2>
                <p>La ansiedad aparece cuando tu cerebro detecta algo importante en juego: un examen, una conversación difícil, un cambio vital. Es el mismo sistema que nos ha protegido durante miles de años. El problema no es sentirla, sino lo que hacemos con ella: evitar, posponer o exigirnos no sentir nada.</p>
                <h2>Tres ideas para empezar</h2>
                <ul>
                <li><strong>Nómbrala sin juzgarla:</strong> «estoy sintiendo ansiedad porque esto me importa» cambia mucho respecto a «no debería sentirme así».</li>
                <li><strong>Respira con el diafragma:</strong> inhala 4 segundos, exhala 6. Repetir cinco veces calma el sistema nervioso.</li>
                <li><strong>Actúa a pesar de ella:</strong> la ansiedad baja cuando compruebas que puedes afrontarlo, no antes.</li>
                </ul>
                <p>Si la ansiedad limita tu vida desde hace meses, pedir ayuda profesional es un paso de valentía, no de debilidad.</p>
                HTML,
            'imagen' => 'images/blog-1.svg',
            'estado' => Post::ESTADO_PUBLICADO,
            'publicado_en' => now()->subDays(6),
        ]);
        Post::create([
            'titulo' => 'Descansar también es productivo: el mito del siempre ocupado',
            'slug' => 'descansar-tambien-es-productivo',
            'extracto' => 'Vivimos midiendo nuestro valor por lo que producimos. El descanso no es un premio: es una necesidad.',
            'cuerpo' => <<<'HTML'
                <p>«No sé parar», «me siento culpable si no hago nada». Lo escucho cada semana en consulta. Hemos interiorizado que nuestro valor depende de nuestra productividad, y el descanso se ha convertido en algo que hay que ganarse.</p>
                <h2>Descanso no es hacer scroll</h2>
                <p>Mirar el móvil en el sofá no descansa: mantiene el cerebro en alerta. Descansar de verdad implica actividades que bajen la activación: pasear sin prisa, cocinar con calma, dormir lo suficiente, estar con quien quieres sin mirar la hora.</p>
                <h2>Un experimento de una semana</h2>
                <p>Te propongo algo sencillo: reserva cada día 30 minutos sin pantallas y sin objetivo. Al principio la culpa aparecerá; obsérvala como un pensamiento más, no como una orden. Al cabo de una semana, pregúntate cómo están tu energía y tu ánimo.</p>
                <p>Cuidarte no es egoísmo: es lo que te permite cuidar de lo demás.</p>
                HTML,
            'imagen' => 'images/blog-2.svg',
            'estado' => Post::ESTADO_PUBLICADO,
            'publicado_en' => now()->subDays(13),
        ]);
        Post::create([
            'titulo' => 'Poner límites sin sentirte mala persona',
            'slug' => 'poner-limites-sin-culpa',
            'extracto' => 'Decir «no» también es cuidar la relación. Claves para poner límites con firmeza y amabilidad.',
            'cuerpo' => <<<'HTML'
                <p>Poner un límite y sentir culpa después es una de las experiencias más comunes que trabajo en terapia. Nos han enseñado que decir «no» es ser egoísta, cuando en realidad es lo contrario: las relaciones se sostienen mejor cuando ambas partes pueden expresar sus necesidades.</p>
                <h2>La fórmula del límite amable</h2>
                <p>Un buen límite tiene tres partes: validar, decir y proponer. Por ejemplo: «Entiendo que lo necesites (validar), pero este fin de semana no puedo quedarme con los niños (decir). ¿Buscamos otra fecha? (proponer)».</p>
                <h2>La culpa no es una brújula</h2>
                <p>Sentir culpa al principio es normal si nunca lo hiciste: es un hábito aprendido, no una señal de que estés haciendo daño. Con la práctica, la culpa se atenúa y aparece algo mejor: respeto por ti misma.</p>
                <p>Empieza con límites pequeños y observa qué pasa. Casi nunca ocurre la catástrofe que imaginabas.</p>
                HTML,
            'imagen' => 'images/blog-3.svg',
            'estado' => Post::ESTADO_PUBLICADO,
            'publicado_en' => now()->subDays(20),
        ]);
        Post::create([
            'titulo' => 'Borrador: ideas sobre el perfeccionismo',
            'slug' => 'borrador-perfeccionismo',
            'extracto' => 'Notas en construcción.',
            'cuerpo' => '<p>Texto en construcción, aún sin revisar.</p>',
            'imagen' => 'images/blog-1.svg',
            'estado' => Post::ESTADO_BORRADOR,
            'publicado_en' => null,
        ]);
    }

    private function crearArticulos(): void
    {
        Articulo::create([
            'titulo' => 'Trastorno de ansiedad generalizada: síntomas y señales',
            'slug' => 'trastorno-ansiedad-generalizada',
            'categoria' => Articulo::CATEGORIA_TRASTORNO,
            'extracto' => 'Preocupación excesiva y difícil de controlar durante meses: así se manifiesta la ansiedad generalizada.',
            'cuerpo' => <<<'HTML'
                <p>El trastorno de ansiedad generalizada (TAG) se caracteriza por una preocupación excesiva y persistente por asuntos cotidianos —el trabajo, la salud, la familia— que resulta difícil de controlar y dura al menos seis meses.</p>
                <h2>Síntomas frecuentes</h2>
                <ul>
                <li>Inquietud o sensación de estar «al límite».</li>
                <li>Fatiga fácil y dificultades de concentración.</li>
                <li>Irritabilidad y tensión muscular.</li>
                <li>Problemas para conciliar o mantener el sueño.</li>
                </ul>
                <h2>Cuándo pedir ayuda</h2>
                <p>Si la preocupación ocupa gran parte del día, interfiere con tu trabajo o tus relaciones y no remite con el descanso, conviene consultar con un profesional. El TAG responde muy bien a la terapia cognitivo-conductual y, cuando se necesita, al tratamiento farmacológico pautado por el médico.</p>
                HTML,
            'imagen' => 'images/art-1.svg',
            'estado' => Articulo::ESTADO_PUBLICADO,
            'publicado_en' => now()->subDays(9),
        ]);
        Articulo::create([
            'titulo' => 'Duelo: las fases no son una escalera',
            'slug' => 'duelo-fases-no-escalera',
            'categoria' => Articulo::CATEGORIA_TRASTORNO,
            'extracto' => 'El duelo no avanza por etapas ordenadas. Entenderlo así alivia mucha culpa.',
            'cuerpo' => <<<'HTML'
                <p>Tras una pérdida, muchas personas se preguntan si «lo están haciendo bien»: si lloran demasiado o demasiado poco, si ya «deberían» estar mejor. Parte de esa presión viene de la idea de que el duelo tiene fases ordenadas que hay que superar.</p>
                <h2>Un proceso en oleadas</h2>
                <p>La investigación actual describe el duelo como un movimiento entre dos polos: momentos de conexión con la pérdida (tristeza, añoranza) y momentos orientados a reconstruir la vida (trabajo, nuevas rutinas). Oscilar entre ambos no es retroceder: es adaptarse.</p>
                <h2>Señales para pedir apoyo</h2>
                <p>Es recomendable buscar ayuda profesional si el dolor te impide funcionar durante meses, si aparecen ideas de hacerte daño o si sientes que tu vida se detuvo por completo con la pérdida. Acompañar el duelo es una de las tareas más humanas de la terapia.</p>
                HTML,
            'imagen' => 'images/art-2.svg',
            'estado' => Articulo::ESTADO_PUBLICADO,
            'publicado_en' => now()->subDays(16),
        ]);
        Articulo::create([
            'titulo' => 'Terapia cognitivo-conductual: en qué consiste',
            'slug' => 'terapia-cognitivo-conductual',
            'categoria' => Articulo::CATEGORIA_TRATAMIENTO,
            'extracto' => 'El tratamiento psicológico con más evidencia: cómo funciona y para qué sirve.',
            'cuerpo' => <<<'HTML'
                <p>La terapia cognitivo-conductual (TCC) es el enfoque psicológico con mayor respaldo científico para la ansiedad, la depresión y muchos otros problemas. Su idea central es sencilla: lo que pensamos, lo que sentimos y lo que hacemos se influyen entre sí, y cambiar una de esas piezas mueve las demás.</p>
                <h2>Cómo se trabaja</h2>
                <ul>
                <li><strong>Evaluación y objetivos:</strong> las primeras sesiones sirven para entender tu caso y pactar metas concretas.</li>
                <li><strong>Técnicas cognitivas:</strong> identificar y cuestionar pensamientos distorsionados («todo me sale mal»).</li>
                <li><strong>Técnicas conductuales:</strong> exposición gradual a lo temido, activación de actividades gratificantes, entrenamiento en habilidades.</li>
                <li><strong>Práctica entre sesiones:</strong> pequeños ejercicios para consolidar lo aprendido.</li>
                </ul>
                <h2>Duración</h2>
                <p>Suele ser una terapia de duración media: entre 8 y 20 sesiones según el caso. Desde el principio sabrás qué estamos haciendo y por qué.</p>
                HTML,
            'imagen' => 'images/art-3.svg',
            'estado' => Articulo::ESTADO_PUBLICADO,
            'publicado_en' => now()->subDays(11),
        ]);
        Articulo::create([
            'titulo' => 'EMDR: reprocesar recuerdos que siguen doliendo',
            'slug' => 'emdr-reprocesar-recuerdos',
            'categoria' => Articulo::CATEGORIA_TRATAMIENTO,
            'extracto' => 'Qué es la terapia EMDR y por qué ayuda con el trauma y los recuerdos difíciles.',
            'cuerpo' => <<<'HTML'
                <p>EMDR son las siglas de «desensibilización y reprocesamiento por movimientos oculares». Es una terapia recomendada por la Organización Mundial de la Salud para el trastorno de estrés postraumático, y también resulta útil con recuerdos difíciles que, sin llegar a ser traumáticos, siguen generando malestar.</p>
                <h2>En qué consiste una sesión</h2>
                <p>Tras una fase de preparación y estabilización, se trabaja con el recuerdo a la vez que se realiza estimulación bilateral (habitualmente movimientos oculares guiados). El objetivo no es olvidar lo ocurrido, sino que el recuerdo deje de doler como si estuviera pasando ahora y se integre como algo pasado.</p>
                <h2>Mitos frecuentes</h2>
                <ul>
                <li>No es hipnosis: estás consciente y tienes el control en todo momento.</li>
                <li>No borra recuerdos: cambia la carga emocional que llevan asociada.</li>
                <li>No es magia rápida: requiere evaluación cuidadosa y un terapeuta acreditado.</li>
                </ul>
                HTML,
            'imagen' => 'images/art-4.svg',
            'estado' => Articulo::ESTADO_PUBLICADO,
            'publicado_en' => now()->subDays(3),
        ]);
    }

    private function crearLeads(): void
    {
        $servicio = Servicio::where('nombre', 'like', '%presencial%')->first();

        Lead::create([
            'nombre' => 'Carmen Soto', 'email' => 'carmen.soto@example.test', 'telefono' => '634567890',
            'servicio_id' => $servicio?->id,
            'mensaje' => 'Hola, me gustaría información sobre la terapia para la ansiedad. ¿Tienes disponibilidad por las mañanas?',
            'consentimiento' => true, 'estado' => Lead::ESTADO_NUEVO, 'origen' => Lead::ORIGEN_WEB,
        ]);

        $lead2 = Lead::create([
            'nombre' => 'Javier Molina', 'email' => 'javier.molina@example.test', 'telefono' => null,
            'servicio_id' => null,
            'mensaje' => 'Quiero pedir una primera entrevista para hablar de un duelo reciente.',
            'consentimiento' => true, 'estado' => Lead::ESTADO_CONTACTADO, 'origen' => Lead::ORIGEN_WEB,
        ]);
        Interaccion::create(['lead_id' => $lead2->id, 'fecha' => now()->subDays(2), 'canal' => Interaccion::CANAL_TELEFONO, 'nota' => 'Llamada de 10 minutos. Interesado, pide pensarlo unos días. Volver a llamar el lunes.']);
        Interaccion::create(['lead_id' => $lead2->id, 'fecha' => now()->subDay(), 'canal' => Interaccion::CANAL_EMAIL, 'nota' => 'Email con información de horarios y precios.']);

        Lead::create([
            'nombre' => 'Ana Beltrán', 'email' => 'ana.beltran@example.test', 'telefono' => '645678901',
            'servicio_id' => null,
            'mensaje' => 'Me recomendó una amiga. Busco terapia online por las tardes.',
            'consentimiento' => true, 'estado' => Lead::ESTADO_CUALIFICADO, 'origen' => Lead::ORIGEN_MANUAL,
        ]);
    }

    private function crearHistorial(): void
    {
        $c1 = Cliente::whereHas('user', fn ($q) => $q->where('email', 'cliente1@demo.test'))->firstOrFail();
        $c2 = Cliente::whereHas('user', fn ($q) => $q->where('email', 'cliente2@demo.test'))->firstOrFail();

        $e1 = EntradaHistorial::create([
            'cliente_id' => $c1->id, 'fecha' => today()->subDays(21),
            'motivo' => 'Ansiedad generalizada: preocupación constante, insomnio y tensión muscular desde hace un año.',
            'notas_sesion' => 'Primera sesión: historia del problema, evaluación y objetivos. Buena alianza desde el inicio.',
            'plan_terapeutico' => 'Psicoeducación sobre ansiedad, respiración diafragmática y registro de preocupaciones.',
            'visible_cliente' => true,
        ]);
        EntradaHistorial::create([
            'cliente_id' => $c1->id, 'fecha' => today()->subDays(14),
            'motivo' => 'Revisión: mejora del sueño, persisten rumiaciones laborales.',
            'notas_sesion' => 'Se introduce reestructuración cognitiva con ejemplos de su trabajo. Tarea: autorregistro.',
            'plan_terapeutico' => 'Continuar con TCC; valorar exposición a reuniones en dos sesiones.',
            'visible_cliente' => false,
        ]);

        // Adjunto de ejemplo en disco privado.
        Storage::disk('privado')->put('historial/ejemplo-respiracion.pdf', $this->pdfEjemplo());
        Adjunto::create([
            'entrada_historial_id' => $e1->id,
            'ruta' => 'historial/ejemplo-respiracion.pdf',
            'nombre_original' => 'Guía de respiración diafragmática.pdf',
            'mime' => 'application/pdf',
            'tamano' => Storage::disk('privado')->size('historial/ejemplo-respiracion.pdf'),
        ]);

        EntradaHistorial::create([
            'cliente_id' => $c2->id, 'fecha' => today()->subDays(10),
            'motivo' => 'Duelo por fallecimiento de su padre hace cuatro meses. Tristeza intensa y aislamiento.',
            'notas_sesion' => 'Validación del proceso. Se normaliza la oscilación entre conexión y reconstrucción.',
            'plan_terapeutico' => 'Acompañamiento del duelo, activación gradual de actividades significativas.',
            'visible_cliente' => true,
        ]);
        EntradaHistorial::create([
            'cliente_id' => $c2->id, 'fecha' => today()->subDays(3),
            'motivo' => 'Revisión: ha retomado el deporte; culpa al disfrutar.',
            'notas_sesion' => 'Trabajo con la culpa: diferenciar lealtad de sufrimiento. Hipótesis: duelo normativo.',
            'plan_terapeutico' => 'Prolongar activación; seguimiento quincenal.',
            'visible_cliente' => false,
        ]);
    }

    private function crearHilos(): void
    {
        $admin = User::where('email', 'admin@demo.test')->firstOrFail();
        $c1 = Cliente::whereHas('user', fn ($q) => $q->where('email', 'cliente1@demo.test'))->firstOrFail();
        $u1 = $c1->user;
        $c2 = Cliente::whereHas('user', fn ($q) => $q->where('email', 'cliente2@demo.test'))->firstOrFail();
        $u2 = $c2->user;

        $h1 = Hilo::create(['cliente_id' => $c1->id, 'asunto' => 'Duda sobre el autorregistro', 'creado_por' => $u1->id]);
        Mensaje::create(['hilo_id' => $h1->id, 'remitente_id' => $u1->id, 'cuerpo' => 'Hola Elena, ¿el autorregistro lo hago cada día o solo cuando note ansiedad?', 'leido_en' => now()->subDays(2)]);
        Mensaje::create(['hilo_id' => $h1->id, 'remitente_id' => $admin->id, 'cuerpo' => 'Hola Lucía: hazlo a diario durante esta semana, aunque sea breve. Así tendremos una buena foto de la semana.', 'leido_en' => null]);

        $h2 = Hilo::create(['cliente_id' => $c2->id, 'asunto' => 'Cambio de hora', 'creado_por' => $u2->id]);
        Mensaje::create(['hilo_id' => $h2->id, 'remitente_id' => $u2->id, 'cuerpo' => 'Hola, ¿podríamos pasar la próxima sesión a las 17:00?', 'leido_en' => null]);
    }

    private function crearCitas(): void
    {
        $c1 = Cliente::whereHas('user', fn ($q) => $q->where('email', 'cliente1@demo.test'))->firstOrFail();
        $c2 = Cliente::whereHas('user', fn ($q) => $q->where('email', 'cliente2@demo.test'))->firstOrFail();
        $presencial = Servicio::where('nombre', 'like', '%presencial%')->firstOrFail();
        $online = Servicio::where('nombre', 'like', '%online%')->firstOrFail();

        $prox = fn (int $dias, string $hora): Carbon => $this->diaLaborable(today()->addDays($dias), $hora);
        $pas = fn (int $dias, string $hora): Carbon => $this->diaLaborable(today()->subDays($dias), $hora);

        // Solicitada pendiente (futura).
        $i = $prox(3, '10:00');
        Cita::create(['cliente_id' => $c1->id, 'servicio_id' => $presencial->id, 'inicio' => $i, 'fin' => $i->copy()->addMinutes(50), 'estado' => Cita::ESTADO_SOLICITADA, 'comentario_cliente' => 'Preferiría por la mañana, gracias.']);

        // Confirmada hoy (si hoy es laborable; si no, el próximo laborable).
        $hoy = today();
        if ($hoy->isWeekend()) {
            $hoy = $hoy->copy()->next(Carbon::MONDAY);
        }
        $i = $hoy->copy()->setTime(12, 0);
        if ($i->isPast()) {
            $i = $hoy->copy()->addWeek()->setTime(12, 0);
        }
        Cita::create(['cliente_id' => $c2->id, 'servicio_id' => $online->id, 'inicio' => $i, 'fin' => $i->copy()->addMinutes(50), 'estado' => Cita::ESTADO_CONFIRMADA]);

        // Confirmada futura.
        $i = $prox(6, '17:00');
        Cita::create(['cliente_id' => $c1->id, 'servicio_id' => $presencial->id, 'inicio' => $i, 'fin' => $i->copy()->addMinutes(50), 'estado' => Cita::ESTADO_CONFIRMADA]);

        // Completada pasada.
        $i = $pas(14, '10:00');
        Cita::create(['cliente_id' => $c1->id, 'servicio_id' => $presencial->id, 'inicio' => $i, 'fin' => $i->copy()->addMinutes(50), 'estado' => Cita::ESTADO_COMPLETADA]);

        // Rechazada pasada.
        $i = $pas(7, '16:00');
        Cita::create(['cliente_id' => $c2->id, 'servicio_id' => $online->id, 'inicio' => $i, 'fin' => $i->copy()->addMinutes(50), 'estado' => Cita::ESTADO_RECHAZADA, 'motivo_rechazo' => 'Ese día la consulta permanece cerrada por festivo local.']);

        // Cancelada pasada.
        $i = $pas(4, '11:00');
        Cita::create(['cliente_id' => $c2->id, 'servicio_id' => $presencial->id, 'inicio' => $i, 'fin' => $i->copy()->addMinutes(50), 'estado' => Cita::ESTADO_CANCELADA, 'comentario_cliente' => 'Cancelada por el cliente por un imprevisto laboral.']);
    }

    private function diaLaborable(Carbon $dia, string $hora): Carbon
    {
        $d = $dia->copy();
        while ($d->isWeekend()) {
            $d->addDay();
        }
        [$h, $m] = explode(':', $hora);

        return $d->setTime((int) $h, (int) $m);
    }

    private function pdfEjemplo(): string
    {
        $text = 'Guia de respiracion diafragmatica - Consulta Demo. 1) Sientate comodamente. 2) Inhala 4 segundos por la nariz. 3) Exhala 6 segundos por la boca. 4) Repite 5 veces, dos veces al dia.';
        $stream = "BT /F1 12 Tf 50 750 Td ({$text}) Tj ET";
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        $objs = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            5 => '<< /Length '.strlen($stream).' >> stream'."\n{$stream}\nendstream",
        ];
        foreach ($objs as $n => $body) {
            $offsets[$n] = strlen($pdf);
            $pdf .= "{$n} 0 obj\n{$body}\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref'."\n0 6\n0000000000 65535 f \n";
        for ($n = 1; $n <= 5; $n++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$n]);
        }
        $pdf .= 'trailer << /Size 6 /Root 1 0 R >>'."\nstartxref\n{$xref}\n%%EOF\n";

        return $pdf;
    }
}
