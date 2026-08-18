<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ficha de Datos Individuales - Folio {{ $folio }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #333;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        .header h1 {
            font-size: 11pt;
            margin-bottom: 2px;
        }
        .header h2 {
            font-size: 9pt;
            font-weight: normal;
        }
        .folio-box {
            background-color: #f0f0f0;
            padding: 5px;
            margin-bottom: 10px;
            border-left: 3px solid #a01508;
            font-size: 7pt;
        }
        .section {
            margin-bottom: 10px;
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        .section-title {
            background-color: #a01508;
            color: white;
            padding: 3px 8px;
            margin-bottom: 5px;
            font-size: 9pt;
            font-weight: bold;
        }
        .subsection-title {
            background-color: #e9ecef;
            padding: 2px 6px;
            margin: 8px 0 4px 0;
            font-size: 8pt;
            font-weight: bold;
            border-left: 2px solid #a01508;
        }
        .data-row {
            margin-bottom: 2px;
            padding-left: 10px;
            font-size: 7.5pt;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            min-width: 150px;
        }
        .value {
            display: inline-block;
        }
        .family-card {
            border: 1px solid #ddd;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        .family-header {
            background-color: #f8f9fa;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 8pt;
            border-bottom: 1px solid #ddd;
        }
        .family-body {
            padding: 6px;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7pt;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7pt;
        }
        .foto-container {
            text-align: left;
            margin-bottom: 10px;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .foto {
            max-width: 120px;
            max-height: 120px;
            width: auto;
            height: auto;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .foto-label {
            font-size: 6pt;
            color: #6c757d;
            margin-top: 3px;
        }
        .clearfix {
            clear: both;
        }
        footer {
            text-align: center;
            font-size: 6pt;
            color: #6c757d;
            margin-top: 10px;
            border-top: 1px solid #dee2e6;
            padding-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px;
            vertical-align: top;
        }
        body {
            page-break-after: avoid;
            page-break-before: avoid;
        }
    </style>
</head>
<body>

<!-- ENCABEZADO -->
 <div class="section">
    <div class="header">
        <h1>COLEGIO DE ESTUDIOS CIENTÍFICOS Y TECNOLÓGICOS</h1>
        <h2>DEL ESTADO DE ZACATECAS - PLANTEL RÍO GRANDE</h2>
        <h3>FICHA DE DATOS INDIVIDUALES</h3>
    </div>
</div>

<!-- DATOS DE LA CONSULTA -->
<div class="section">
    <div class="folio-box">
        <strong>Folio:</strong> {{ $folio }}<br>
        <strong>Conforme a los datos disponibles el día:</strong> {{ $fecha }} a las {{ $hora }}<br>
        <strong>Usuario que generó esta consulta:</strong> {{ $usuario }}
    </div>
</div>

<!-- DATOS GENERALES DEL ALUMNO -->
<div class="section">
    <div class="section-title">DATOS GENERALES DEL ALUMNO</div>
    
    <!-- Foto centrada arriba -->
    @if(isset($fotoBase64) && $fotoBase64)
    <div class="foto-container">
        <img src="{{ $fotoBase64 }}" class="foto" alt="Foto del alumno">
        <div class="foto-label">Foto del alumno</div>
    </div>
    @endif
    
    <div class="data-row"><span class="label">Número de Control:</span> <span class="value">{{ $num_control }}</span></div>
    <div class="data-row">
        <span class="label">Estatus:</span> 
        <span class="value">{!! $estatus == 'Activo' ? '<span class="badge-success">Activo</span>' : '<span class="badge-danger">Inactivo</span>' !!}</span>
    </div>
    <div class="data-row"><span class="label">Fecha de Nacimiento:</span> <span class="value">{{ $fecha_nacimiento }}</span></div>
    <div class="data-row"><span class="label">Edad:</span> <span class="value">{{ $edad }} años</span></div>
    <div class="clearfix"></div>
</div>

<!-- 1.-DATOS PERSONALES DEL ALUMNO -->
<div class="section">
    <div class="section-title">1.-DATOS PERSONALES DEL ALUMNO</div>
    
    <div class="subsection-title">1.1-NOMBRE COMPLETO</div>
    <div class="data-row"><span class="label">Nombre del alumno(a):</span> <span class="value">{{ $nombre_completo }}</span></div>
    
    <div class="subsection-title">1.2-LUGAR DE NACIMIENTO</div>
    <div class="data-row">
        <span class="label">Localidad:</span> 
        <span class="value">
            @if($lugar_nacimiento)
                {{ $lugar_nacimiento->localidad }}, {{ $lugar_nacimiento->municipio }}, {{ $lugar_nacimiento->estado }}, {{ $lugar_nacimiento->pais }}
            @else
                —
            @endif
        </span>
    </div>
    
    <div class="subsection-title">1.3-DOMICILIO ACTUAL</div>
    <div class="data-row">
        <span class="label">Dirección:</span> 
        <span class="value">
            @if($domicilio)
                {{ $domicilio->calle }} #{{ $domicilio->num_ext }}@if($domicilio->num_int)-{{ $domicilio->num_int }}@endif, 
                {{ $domicilio->colonia }}, {{ $domicilio->localidad }}, {{ $domicilio->municipio }}, 
                C.P. {{ $domicilio->cp }}, {{ $domicilio->estado }}
            @else
                —
            @endif
        </span>
    </div>
    <div class="data-row"><span class="label">Teléfono del domicilio:</span> <span class="value">{{ $domicilio->telefono_domicilio ?? '—' }}</span></div>
    
    <div class="subsection-title">1.4-SECUNDARIA DE PROCEDENCIA</div>
    <div class="data-row"><span class="label">Nombre del Plantel:</span> <span class="value">{{ $secundaria->nombre ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Tipo de Secundaria:</span> <span class="value">{{ $secundaria->tipo ?? '—' }}</span></div>
    <div class="data-row">
        <span class="label">Dirección:</span> 
        <span class="value">
            @if($secundaria)
                {{ $secundaria->localidad }}, {{ $secundaria->municipio }}, {{ $secundaria->estado }}, {{ $secundaria->pais }}
            @else
                —
            @endif
        </span>
    </div>
    
    <div class="subsection-title">1.5-DATOS PARTICULARES DEL ALUMNO</div>
    <div class="data-row"><span class="label">Teléfono Celular:</span> <span class="value">{{ $telefono_celular ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Email Personal:</span> <span class="value">{{ $email_personal ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Email Institucional:</span> <span class="value">{{ $email_institucional ?? '—' }}</span></div>
    <div class="data-row"><span class="label">CURP:</span> <span class="value">{{ $curp ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Número de Seguro Social:</span> <span class="value">{{ $nss ?? '—' }}</span></div>
    
    <div class="subsection-title">1.6-GRUPO</div>
    <div class="data-row">
        <span class="label">Grupo y Carrera:</span> 
        <span class="value">
            @if($grupo)
                {{ $grupo->semestre }} {{ $grupo->grupo }} - {{ $grupo->carrera }}
            @else
                Sin grupo asignado
            @endif
        </span>
    </div>
    
    <div class="subsection-title">1.7-BECAS</div>
    <div class="data-row">
        <span class="label">Becas activas:</span> 
        <span class="value">
            @if(count($becas) > 0)
                {{ implode('; ', $becas) }}
            @else
                No tiene
            @endif
        </span>
    </div>
    
    <div class="subsection-title">1.8-TRABAJO DEL ALUMNO</div>
    <div class="data-row"><span class="label">Lugar de trabajo:</span> <span class="value">{{ $trabajo->lugar_trabajo ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Horario laboral:</span> <span class="value">{{ $trabajo->horario_laboral ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Domicilio:</span> <span class="value">{{ $trabajo->domicilio_trabajo ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Teléfono del trabajo:</span> <span class="value">{{ $trabajo->telefono_trabajo ?? '—' }}</span></div>
</div>

<!-- 2.-DATOS FAMILIARES -->
<div class="section">
    <div class="section-title">2.-DATOS FAMILIARES</div>
    
    <div class="subsection-title">2.1-FAMILIARES Y RELACIONADOS</div>
    
    @forelse($familiares as $familiar)
    <div class="family-card">
        <div class="family-header">
            Familiar {{ $loop->iteration }}
        </div>
        <div class="family-body">
            <div class="subsection-title" style="margin-top: 0;">2.1.1-Datos Personales</div>
            <div class="data-row"><span class="label">Parentesco:</span> <span class="value">{{ $familiar->pivot->parentesco }}</span></div>
            <div class="data-row"><span class="label">¿Vive?:</span> <span class="value">{{ $familiar->vive ? 'Sí' : 'No' }}</span></div>
            <div class="data-row"><span class="label">Nombre completo:</span> <span class="value">{{ $familiar->nombre_completo }}</span></div>
            <div class="data-row"><span class="label">Fecha de Nacimiento:</span> <span class="value">{{ $familiar->fecha_nacimiento ? $familiar->fecha_nacimiento->format('d/m/Y') : '—' }}</span></div>
            <div class="data-row"><span class="label">Teléfono Celular:</span> <span class="value">{{ $familiar->telefono_celular ?? '—' }}</span></div>
            <div class="data-row"><span class="label">Escolaridad:</span> <span class="value">{{ $familiar->escolaridad ?? '—' }}</span></div>
            
            <div class="subsection-title">2.1.2-Domicilio</div>
            @if($familiar->comparte_domicilio ?? false)
                <div class="data-row"><span class="label">Dirección:</span> <span class="value">Mismo domicilio del alumno</span></div>
            @elseif($familiar->domicilio)
                <div class="data-row">
                    <span class="label">Dirección:</span> 
                    <span class="value">
                        {{ $familiar->domicilio->calle }} #{{ $familiar->domicilio->num_ext }}@if($familiar->domicilio->num_int)-{{ $familiar->domicilio->num_int }}@endif, 
                        {{ $familiar->domicilio->colonia }}, {{ $familiar->domicilio->localidad }}, 
                        {{ $familiar->domicilio->municipio }}, C.P. {{ $familiar->domicilio->cp }}, {{ $familiar->domicilio->estado }}
                    </span>
                </div>
                <div class="data-row"><span class="label">Teléfono:</span> <span class="value">{{ $familiar->domicilio->telefono_domicilio ?? '—' }}</span></div>
            @else
                <div class="data-row"><span class="value">—</span></div>
            @endif
            
            <div class="subsection-title">2.1.3-Trabajo y Ocupación</div>
            <div class="data-row"><span class="label">Ocupación:</span> <span class="value">{{ $familiar->ocupacion ?? '—' }}</span></div>
            <div class="data-row"><span class="label">Lugar de trabajo:</span> <span class="value">{{ $familiar->lugar_trabajo ?? '—' }}</span></div>
            <div class="data-row"><span class="label">Horario laboral:</span> <span class="value">{{ $familiar->horario_laboral ?? '—' }}</span></div>
            <div class="data-row"><span class="label">Domicilio del trabajo:</span> <span class="value">{{ $familiar->domicilio_trabajo ?? '—' }}</span></div>
            <div class="data-row"><span class="label">Teléfono del trabajo:</span> <span class="value">{{ $familiar->telefono_trabajo ?? '—' }}</span></div>
            
            <div class="subsection-title">2.1.4-Asignaciones</div>
            <div class="data-row"><span class="label">¿Autorizado como Tutor?:</span> <span class="value">{{ $familiar->pivot->tutor ? 'Sí' : 'No' }}</span></div>
            <div class="data-row">
                <span class="label">Prioridad como Contacto:</span> 
                <span class="value">
                    @if($familiar->pivot->contacto_emergencia > 0)
                        {{ $familiar->pivot->contacto_emergencia }}
                    @else
                        No autorizado
                    @endif
                </span>
            </div>
        </div>
    </div>
    @empty
    <div class="data-row"><span class="value">No hay familiares registrados</span></div>
    @endforelse
    
    <div class="subsection-title">2.2-INFORMACIÓN FAMILIAR</div>
    <div class="data-row"><span class="label">Estado Civil de los Padres:</span> <span class="value">{{ $datos_familiares->estado_civil_padres ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Ingreso Familiar:</span> <span class="value">${{ number_format($datos_familiares->ingreso_familiar_aprox ?? 0, 2) }}</span></div>
    <div class="data-row"><span class="label">Gasto Familiar:</span> <span class="value">${{ number_format($datos_familiares->gasto_familiar_aprox ?? 0, 2) }}</span></div>
    <div class="data-row"><span class="label">Casa propia:</span> <span class="value">{{ ($datos_familiares->casa_propia ?? false) ? 'Sí' : 'No' }}</span></div>
    <div class="data-row"><span class="label">Automóvil propio (familia):</span> <span class="value">{{ ($datos_familiares->auto_propio_familia ?? false) ? 'Sí' : 'No' }}</span></div>
    <div class="data-row">
        <span class="label">Servicios de la casa:</span> 
        <span class="value">
            @php
                $serviciosLista = [1 => 'Energía eléctrica', 2 => 'Agua corriente', 3 => 'Drenaje', 4 => 'Alumbrado público'];
                $serviciosTexto = [];
                foreach ($servicios_array as $s) { $serviciosTexto[] = $serviciosLista[$s] ?? ''; }
            @endphp
            {{ implode(', ', array_filter($serviciosTexto)) ?: '—' }}
        </span>
    </div>
</div>

<!-- 3.-INFORMACIÓN SOCIOECONÓMICA PERSONAL -->
<div class="section">
    <div class="section-title">3.-INFORMACIÓN SOCIOECONÓMICA PERSONAL</div>
    <div class="data-row"><span class="label">Automóvil propio (alumno):</span> <span class="value">{{ ($info_socioeco->auto_propio_alumno ?? false) ? 'Sí' : 'No' }}</span></div>
    <div class="data-row"><span class="label">Transporte a la escuela:</span> <span class="value">{{ !empty($transportes) ? implode(', ', $transportes) : '—' }}</span></div>
    <div class="data-row"><span class="label">Tiempo de traslado:</span> <span class="value">{{ $info_socioeco->traslado_horas ?? 0 }}h {{ $info_socioeco->traslado_minutos ?? 0 }}min</span></div>
    <div class="data-row"><span class="label">Estado Civil:</span> <span class="value">{{ $info_socioeco->estado_civil_alumno ?? '—' }}</span></div>
    <div class="data-row">
        <span class="label">Hijos:</span> 
        <span class="value">{{ ($info_socioeco->num_hijos ?? 0) > 0 ? $info_socioeco->num_hijos : 'No tiene' }}</span>
    </div>
    @if(($info_socioeco->num_hijos ?? 0) > 0)
    <div class="data-row"><span class="label">Edades de los hijos:</span> <span class="value">{{ $info_socioeco->edades_hijos ?? '—' }}</span></div>
    @endif
    <div class="data-row"><span class="label">Apoyo económico familiar:</span> <span class="value">${{ number_format($info_socioeco->monto_apoyo ?? 0, 2) }}</span></div>
    <div class="data-row"><span class="label">Gasto diario (comida/transporte):</span> <span class="value">${{ number_format($info_socioeco->gasto_comida_transporte ?? 0, 2) }}</span></div>
    <div class="data-row"><span class="label">Comidas diarias:</span> <span class="value">{{ $info_socioeco->comidas_diarias ?? 3 }}</span></div>
</div>

<!-- 4.-DATOS ACADÉMICOS -->
<div class="section">
    <div class="section-title">4.-DATOS ACADÉMICOS</div>
    
    <div class="subsection-title">4.1-HERRAMIENTAS</div>
    <div class="data-row">
        <span class="label">Dispositivos para tareas:</span> 
        <span class="value">
            @php
                $dispositivosLista = [1 => 'Celular', 2 => 'Computadora', 3 => 'Internet', 4 => 'Tablet'];
                $dispositivosTexto = [];
                foreach ($dispositivos_array as $d) { $dispositivosTexto[] = $dispositivosLista[$d] ?? ''; }
            @endphp
            {{ implode(', ', array_filter($dispositivosTexto)) ?: '—' }}
        </span>
    </div>
    <div class="data-row"><span class="label">Otros dispositivos:</span> <span class="value">{{ $datos_academicos->otro_dispositivo ?? '—' }}</span></div>
    
    <div class="subsection-title">4.2-PROBLEMAS DE APRENDIZAJE</div>
    <div class="data-row">
        <span class="label">Características diagnosticadas:</span> 
        <span class="value">
            @php
                $problemasLista = [1 => 'Dislexia', 2 => 'Visuales', 3 => 'Auditivos', 4 => 'No entender instrucciones', 5 => 'Lentitud', 6 => 'Problemas de lenguaje', 7 => 'Problemas de comunicación', 8 => 'Discriminación auditiva', 9 => 'Sustituir expresión verbal', 10 => 'Distracción fácil', 11 => 'TDAH', 12 => 'Discapacidad intelectual'];
                $problemasTexto = [];
                foreach ($caracteristicas_array as $p) { $problemasTexto[] = $problemasLista[$p] ?? ''; }
            @endphp
            {{ implode(', ', array_filter($problemasTexto)) ?: 'Ninguno' }}
        </span>
    </div>
    <div class="data-row"><span class="label">Otros problemas:</span> <span class="value">{{ $problema_aprendizaje->otro_problema ?? '—' }}</span></div>
</div>

<!-- 5.-DATOS GENERALES DE SALUD -->
<div class="section">
    <div class="section-title">5.-DATOS GENERALES DE SALUD</div>
    
    <div class="subsection-title">5.1-DATOS BÁSICOS</div>
    <div class="data-row"><span class="label">Estatura/Peso/Sangre:</span> <span class="value">{{ $datos_salud->estatura ?? '—' }} cm / {{ $datos_salud->peso ?? '—' }} kg / {{ $datos_salud->tipo_sangre ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Cuadro básico de vacunas:</span> <span class="value">{{ ($datos_salud->cuadro_basico_vacunas ?? false) ? 'Sí' : 'No' }}</span></div>
    <div class="data-row"><span class="label">Graduación de anteojos:</span> <span class="value">{{ ($datos_salud->graduacion_anteojos ?? false) ? $datos_salud->graduacion_anteojos . ' dioptrías' : 'No usa' }}</span></div>
    
    <div class="subsection-title">5.2-PADECIMIENTOS FÍSICOS</div>
    <div class="data-row"><span class="label">Cirugía:</span> <span class="value">{{ $datos_salud->cirugia ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Alergia:</span> <span class="value">{{ $datos_salud->alergia ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Limitante físico:</span> <span class="value">{{ $datos_salud->limitante_fisico ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Problema auditivo:</span> <span class="value">{{ $datos_salud->problema_auditivo ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Adicción:</span> <span class="value">{{ $datos_salud->adiccion ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Padecimiento emocional:</span> <span class="value">{{ $datos_salud->padecimiento_emocional ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Enfermedad actual:</span> <span class="value">{{ $datos_salud->enfermedad_actual ?? '—' }}</span></div>
    
    <div class="subsection-title">5.3-SALUD MENTAL</div>
    <div class="data-row">
        <span class="label">Síntomas detectados:</span> 
        <span class="value">
            @php
                $sintomasLista = [1 => 'Ansiedad', 2 => 'Estrés', 3 => 'Depresión', 4 => 'Culpa', 5 => 'Tristeza'];
                $sintomasTexto = [];
                foreach ($sintomas_array as $s) { $sintomasTexto[] = $sintomasLista[$s] ?? ''; }
            @endphp
            {{ implode(', ', array_filter($sintomasTexto)) ?: '—' }}
        </span>
    </div>
    <div class="data-row"><span class="label">Otros síntomas:</span> <span class="value">{{ $datos_salud->otro_sintoma ?? '—' }}</span></div>
    
    <div class="subsection-title">5.4-DATOS ESPECÍFICOS</div>
    <div class="data-row"><span class="label">Medicamento controlado:</span> <span class="value">{{ $datos_salud->medicamento_controlado ?? 'Ninguno' }}</span></div>
    <div class="data-row"><span class="label">Alergia a medicamento:</span> <span class="value">{{ $datos_salud->alergia_medicamento ?? 'Ninguno' }}</span></div>
    <div class="data-row"><span class="label">Hospitalización:</span> <span class="value">{{ $datos_salud->motivo_hospitalizacion ?? 'Ninguna' }}</span></div>
    <div class="data-row"><span class="label">Diabetes:</span> <span class="value">{{ ($datos_salud->diabetes ?? false) ? 'Sí' : 'No' }}</span></div>
    <div class="data-row"><span class="label">Hipertensión:</span> <span class="value">{{ ($datos_salud->hipertension ?? false) ? 'Sí' : 'No' }}</span></div>
    <div class="data-row"><span class="label">Dolores de cabeza:</span> <span class="value">{{ ($datos_salud->dolores_cabeza ?? false) ? 'Sí' : 'No' }}</span></div>
    <div class="data-row"><span class="label">Dolores de estómago:</span> <span class="value">{{ ($datos_salud->dolores_estomago ?? false) ? 'Sí' : 'No' }}</span></div>
    
    <div class="data-row">
        <span class="label">Frecuencia al médico:</span> 
        <span class="value">
            @php
                $frecMedico = $datos_salud->frecuencia_medico ?? -1;
            @endphp
            @if($frecMedico == 0)
                Nunca
            @elseif($frecMedico == -1)
                Sólo cuando se enferma
            @else
                Cada {{ floor($frecMedico / 12) }} año(s) y {{ $frecMedico % 12 }} mes(es)
            @endif
        </span>
    </div>
    <div class="data-row">
        <span class="label">Frecuencia al dentista:</span> 
        <span class="value">
            @php
                $frecDentista = $datos_salud->frecuencia_dentista ?? -1;
            @endphp
            @if($frecDentista == 0)
                Nunca
            @elseif($frecDentista == -1)
                Sólo cuando se enferma
            @else
                Cada {{ floor($frecDentista / 12) }} año(s) y {{ $frecDentista % 12 }} mes(es)
            @endif
        </span>
    </div>
</div>

<!-- 6.-ACTIVIDADES RECREATIVAS -->
<div class="section">
    <div class="section-title">6.-ACTIVIDADES RECREATIVAS</div>
    <div class="data-row"><span class="label">Pasatiempo favorito:</span> <span class="value">{{ $actividades->pasatiempo_favorito ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Horas por semana:</span> <span class="value">{{ $actividades->horas_pasatiempo_dedicadas ?? '—' }}</span></div>
    <div class="data-row">
        <span class="label">Deportes que practica:</span> 
        <span class="value">{{ $actividades && !empty($actividades->deporte_practicado) ? implode(', ', $actividades->deporte_practicado) : '—' }}</span>
    </div>
    <div class="data-row"><span class="label">Horas de deporte por semana:</span> <span class="value">{{ $actividades->horas_deporte_dedicadas ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Horas de TV al día:</span> <span class="value">{{ $actividades->horas_dia_tv ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Horas de computadora al día:</span> <span class="value">{{ $actividades->horas_dia_compu ?? '—' }}</span></div>
    <div class="data-row"><span class="label">Uso de computadora:</span> <span class="value">{{ $actividades->uso_frecuente_compu ?? '—' }}</span></div>
    <div class="data-row">
        <span class="label">Temas de chat:</span> 
        <span class="value">{{ $actividades->temas_quien_chat ?? 'No le gusta chatear' }}</span>
    </div>
</div>

<!-- FIRMA -->
<div class="signature" style="margin-top: 20px; text-align: center; border-top: 1px solid #333; padding-top: 15px;">
    <p style="margin-bottom: 25px;">FIRMA DE VISTO BUENO DE MADRE, PADRE O TUTOR(A)</p>
    <p style="margin-top: 15px;">_______________________________</p>
    <p style="margin-top: 5px; font-size: 7pt;">Nombre y firma</p>
</div>

<footer>
    Documento generado por el Sistema General de Gestión Digital de la Información Personal de los Alumnos (SGGDI)
</footer>

</body>
</html>