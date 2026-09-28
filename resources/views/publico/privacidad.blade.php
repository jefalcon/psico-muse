@extends('publico.base')

@section('titulo', 'Política de privacidad · Consulta Demo')
@section('descripcion', 'Cómo tratamos tus datos personales en Consulta Demo: finalidades, derechos y contacto.')

@section('contenido')
<article class="max-w-3xl mx-auto px-4 py-10 text-[15px] leading-relaxed text-stone-700 space-y-4">
    <h1 class="font-serif text-3xl text-teal-950">Política de privacidad</h1>
    <p><strong>Responsable del tratamiento:</strong> Elena Márquez Ruiz (datos ficticios de demostración), email hola@consulta-demo.test.</p>
    <h2 class="font-serif text-xl text-teal-950 pt-2">Qué datos recogemos y para qué</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li><strong>Formulario de contacto:</strong> nombre, email, teléfono y mensaje, para responder a tu solicitud. La base jurídica es tu consentimiento.</li>
        <li><strong>Pacientes:</strong> datos identificativos y de salud necesarios para la prestación del servicio sanitario, amparados en la relación contractual y en la normativa sanitaria (Ley 41/2002).</li>
    </ul>
    <h2 class="font-serif text-xl text-teal-950 pt-2">Conservación</h2>
    <p>Los mensajes de contacto se conservan un máximo de un año. La documentación clínica se conserva durante los plazos exigidos por la normativa sanitaria (mínimo cinco años desde el alta).</p>
    <h2 class="font-serif text-xl text-teal-950 pt-2">Tus derechos</h2>
    <p>Puedes ejercer tus derechos de acceso, rectificación, supresión, oposición, limitación y portabilidad escribiendo a hola@consulta-demo.test. También tienes derecho a reclamar ante la Agencia Española de Protección de Datos (www.aepd.es).</p>
    <h2 class="font-serif text-xl text-teal-950 pt-2">Cesiones y seguridad</h2>
    <p>No cedemos tus datos a terceros salvo obligación legal. Aplicamos medidas de seguridad proporcionales a la sensibilidad de los datos de salud, incluido el control de acceso a la historia clínica.</p>
</article>
@endsection
