<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Listado de Alumnos - Folio {{ $folio }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 14pt;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 11pt;
            font-weight: normal;
        }
        .header h3 {
            font-size: 12pt;
            margin-top: 5px;
        }
        .folio-box {
            background-color: #f0f0f0;
            padding: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #a01508;
            font-size: 8pt;
        }
        .grupo-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .grupo-header {
            background-color: #a01508;
            color: white;
            padding: 8px 12px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 10pt;
        }
        .info-grupo {
            margin-bottom: 15px;
            padding: 5px 10px;
            background-color: #f8f9fa;
            border-left: 3px solid #a01508;
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #343a40;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 8pt;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 6px;
            vertical-align: top;
            font-size: 8pt;
        }
        .total-grupo {
            text-align: right;
            font-weight: bold;
            margin-top: 5px;
            padding: 5px;
            background-color: #e9ecef;
        }
        .total-global {
            margin-top: 20px;
            padding: 10px;
            background-color: #a01508;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }
        .badge-contacto {
            background-color: #28a745;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7pt;
            display: inline-block;
            margin-bottom: 2px;
        }
        .contacto-item {
            margin-bottom: 3px;
        }
        .small-text {
            font-size: 7pt;
            color: #6c757d;
        }
        .direccion {
            font-size: 7.5pt;
            line-height: 1.2;
        }
        .page-break {
            page-break-before: always;
        }
        footer {
            text-align: center;
            font-size: 7pt;
            color: #6c757d;
            margin-top: 20px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<!-- ENCABEZADO -->
<div class="header">
    <h1>COLEGIO DE ESTUDIOS CIENTÍFICOS Y TECNOLÓGICOS</h1>
    <h2>DEL ESTADO DE ZACATECAS - PLANTEL RÍO GRANDE</h2>
    <h3>LISTADO DE ALUMNOS</h3>
</div>

<!-- DATOS DE LA CONSULTA -->
<div class="folio-box">
    <strong>Folio:</strong> {{ $folio }}<br>
    <strong>Conforme a los datos disponibles el día:</strong> {{ $fecha }} a las {{ $hora }}<br>
    <strong>Usuario que generó esta consulta:</strong> {{ $usuario }}
</div>

<!-- FILTROS APLICADOS -->
<div class="folio-box" style="background-color: #e9ecef;">
    <strong>Filtros aplicados:</strong><br>
    @if($filtros['semestre']) <strong>Semestre:</strong> {{ $filtros['semestre'] }} | @endif
    @if($filtros['grupo_letra']) <strong>Grupo:</strong> {{ $filtros['grupo_letra'] }} | @endif
    @if($filtros['carrera']) <strong>Carrera:</strong> {{ $filtros['carrera'] }} | @endif
    @if($filtros['opcion_prioritaria'] === 'domicilio' && $filtros['localidad'])
        <strong>Localidad:</strong> {{ $filtros['localidad'] }}
    @endif
    <br>
    <strong>Opción de datos prioritarios:</strong>
    @if($opcion_prioritaria === 'contactos')
        Primeros 2 números de contacto
    @elseif($opcion_prioritaria === 'telefonos')
        Teléfonos Celulares personales
    @else
        Domicilio
    @endif
</div>

<!-- LISTADO POR GRUPOS -->
@php
    $contadorGlobal = 0;
@endphp

@foreach($grupos as $grupoData)
@php
    $grupo = $grupoData['grupo'];
    $asesor = $grupoData['asesor'];
    $alumnosGrupo = $grupoData['alumnos'];
    $contadorGrupo = count($alumnosGrupo);
    $contadorGlobal += $contadorGrupo;
@endphp

<div class="grupo-section">
    <div class="grupo-header">
        GRUPO: {{ $grupo->semestre }} {{ $grupo->grupo }} - {{ $grupo->carrera }}
    </div>
    
    <div class="info-grupo">
        <strong>Maestro(a) Asesor(a):</strong> 
        {{ $asesor ? $asesor->nombre_completo : 'No asignado' }}
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="12%">No. Ctrl.</th>
                <th width="28%">Nombre del alumno</th>
                <th width="60%">
                    @if($opcion_prioritaria === 'contactos')
                        Contacto 1; Contacto 2
                    @elseif($opcion_prioritaria === 'telefonos')
                        Teléfono Celular
                    @else
                        Domicilio
                    @endif
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($alumnosGrupo as $alumno)
            <tr>
                <td>{{ $alumno->num_control ?? '—' }}</td>
                <td>{{ $alumno->nombre_completo }}</td>
                <td>
                    @if($opcion_prioritaria === 'contactos')
                        @php
                            $contactos = $alumno->familiares->filter(function($f) {
                                return $f->pivot->contacto_emergencia > 0;
                            })->sortBy(function($f) {
                                return $f->pivot->contacto_emergencia;
                            })->take(2);
                        @endphp
                        @if($contactos->count() > 0)
                            @foreach($contactos as $index => $contacto)
                                <div class="contacto-item">
                                    <span class="badge-contacto">Contacto {{ $index + 1 }}</span><br>
                                    <strong>Teléfono:</strong> {{ $contacto->telefono_celular ?? '—' }}<br>
                                    <strong>Parentesco:</strong> {{ $contacto->pivot->parentesco }}
                                </div>
                                @if(!$loop->last)<hr style="margin: 5px 0;">@endif
                            @endforeach
                        @else
                            <span class="small-text">No hay contactos registrados</span>
                        @endif
                        
                    @elseif($opcion_prioritaria === 'telefonos')
                        <strong>Teléfono Celular:</strong><br>
                        {{ $alumno->telefono_celular ?? '—' }}
                        
                    @else
                        @if($alumno->domicilio)
                            <div class="direccion">
                                {{ $alumno->domicilio->calle }} #{{ $alumno->domicilio->num_ext }}
                                @if($alumno->domicilio->num_int)-{{ $alumno->domicilio->num_int }}@endif<br>
                                {{ $alumno->domicilio->colonia }}, {{ $alumno->domicilio->localidad }}<br>
                                {{ $alumno->domicilio->municipio }}, C.P. {{ $alumno->domicilio->cp }}, {{ $alumno->domicilio->estado }}
                                @if($alumno->domicilio->telefono_domicilio)
                                    <br><strong>Teléfono:</strong> {{ $alumno->domicilio->telefono_domicilio }}
                                @endif
                            </div>
                        @else
                            <span class="small-text">No hay domicilio registrado</span>
                        @endif
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center;">No hay alumnos en este grupo</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="total-grupo">
        Total de alumnos en este grupo: {{ $contadorGrupo }}
    </div>
</div>
@endforeach

<!-- TOTAL GLOBAL -->
<div class="total-global">
    TOTAL GLOBAL DE ALUMNOS LISTADOS CONFORME A LOS FILTROS Y OPCIONES ELEGIDOS: {{ $total_general }}
</div>

<footer>
    Documento generado por el Sistema General de Gestión Digital de la Información Personal de los Alumnos (SGGDI)
</footer>

</body>
</html>