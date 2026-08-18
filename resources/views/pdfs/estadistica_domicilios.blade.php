<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadística de Domicilios y Traslados - Folio {{ $folio }}</title>
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
        .folio-box {
            background-color: #f0f0f0;
            padding: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #a01508;
            font-size: 8pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #a01508;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 9pt;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 6px;
            vertical-align: middle;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .grafica-container {
            margin: 20px 0;
            page-break-inside: avoid;
        }
        .grafica-titulo {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 10pt;
            background-color: #f8f9fa;
            padding: 5px;
            border-left: 3px solid #a01508;
        }
        .barra-apilada {
            width: 100%;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
            display: flex;
        }
        .segmento {
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 8pt;
            font-weight: bold;
        }
        .segmento-verde { background-color: #28a745; }
        .segmento-rojo { background-color: #dc3545; }
        .segmento-azul { background-color: #17a2b8; }
        .segmento-naranja { background-color: #fd7e14; }
        .segmento-morado { background-color: #6f42c1; }
        .segmento-cyan { background-color: #20c997; }
        .leyenda {
            display: flex;
            gap: 20px;
            margin-top: 5px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .leyenda-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 8pt;
        }
        .cuadro-color {
            width: 15px;
            height: 15px;
            border-radius: 3px;
        }
        .barra-item {
            margin-bottom: 8px;
        }
        .barra-label {
            display: inline-block;
            width: 35%;
            font-size: 8pt;
            vertical-align: middle;
        }
        .barra-contenedor {
            display: inline-block;
            width: 50%;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            vertical-align: middle;
        }
        .barra-porcentaje {
            display: inline-block;
            width: 12%;
            text-align: right;
            font-size: 8pt;
            margin-left: 5px;
            vertical-align: middle;
        }
        .barra {
            height: 18px;
            width: 0%;
            border-radius: 5px;
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
    <h3>INFORME DE ESTADÍSTICAS SOBRE DOMICILIOS Y TRASLADOS</h3>
</div>

<!-- DATOS DE LA CONSULTA -->
<div class="folio-box">
    <strong>Folio:</strong> {{ $folio }}<br>
    <strong>Conforme a los datos disponibles el día:</strong> {{ $fecha }} a las {{ $hora }}<br>
    <strong>Usuario que generó esta consulta:</strong> {{ $usuario }}
</div>

<!-- TABLA DE LOCALIDADES -->
<h4 style="margin-bottom: 10px;">Procedencia de los alumnos por localidad</h4>

<table>
    <thead>
        <tr>
            <th>Localidad - Municipio</th>
            <th style="width: 15%; text-align: center;">Cantidad de alumnos</th>
            <th style="width: 20%; text-align: center;">Tiempo mínimo de traslado</th>
            <th style="width: 20%; text-align: center;">Tiempo máximo de traslado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($localidadesData as $data)
        <tr>
            <td>{{ $data['localidad'] }} - {{ $data['municipio'] }}</td>
            <td style="text-align: center;">{{ $data['cantidad'] }}</td>
            <td style="text-align: center;">
                @if($data['tiempo_min'] !== null)
                    {{ floor($data['tiempo_min'] / 60) }}h {{ $data['tiempo_min'] % 60 }}min
                @else
                    —
                @endif
            </td>
            <td style="text-align: center;">
                @if($data['tiempo_max'] !== null)
                    {{ floor($data['tiempo_max'] / 60) }}h {{ $data['tiempo_max'] % 60 }}min
                @else
                    —
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align: center;">No hay datos de domicilios registrados</td>
        </tr>
        @endforelse
        <tr class="total-row">
            <td style="text-align: right;"><strong>TOTAL DE ALUMNOS ACTIVOS</strong></td>
            <td style="text-align: center;"><strong>{{ $totalAlumnos }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>

<!-- GRÁFICA 1: Procedencia (Municipio Río Grande) -->
<div class="grafica-container">
    <div class="grafica-titulo">
        Procedencia de los alumnos (Municipio Río Grande vs Otros)
    </div>
    
    <div class="barra-apilada">
        <div class="segmento segmento-verde" style="width: {{ $porcentajeRGLocalidad }}%;">
            &nbsp;{{ $porcentajeRGLocalidad }}%
        </div>
        <div class="segmento segmento-azul" style="width: {{ $porcentajeRGOtraLocalidad }}%;">
            &nbsp;{{ $porcentajeRGOtraLocalidad }}%
        </div>
        <div class="segmento segmento-rojo" style="width: {{ $porcentajeOtroMunicipio }}%;">
            &nbsp;{{ $porcentajeOtroMunicipio }}%
        </div>
    </div>
    
    <div class="leyenda">
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #28a745;"></div>
            <span>Río Grande (localidad) - {{ $enRGGrandeLocalidad }} alumnos ({{ $porcentajeRGLocalidad }}%)</span>
        </div>
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #17a2b8;"></div>
            <span>Río Grande (municipio, otra localidad) - {{ $enRGMunicipioOtraLocalidad }} alumnos ({{ $porcentajeRGOtraLocalidad }}%)</span>
        </div>
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #dc3545;"></div>
            <span>Otro municipio - {{ $enOtroMunicipio }} alumnos ({{ $porcentajeOtroMunicipio }}%)</span>
        </div>
    </div>
</div>

<!-- GRÁFICA 2: Tiempos de traslado -->
<div class="grafica-container">
    <div class="grafica-titulo">
        Tiempo de traslado de ida y vuelta al plantel
    </div>
    
    <div class="barra-apilada">
        <div class="segmento segmento-naranja" style="width: {{ $porcentajeTrasladoLargo }}%;">
            &nbsp;{{ $porcentajeTrasladoLargo }}%
        </div>
        <div class="segmento segmento-cyan" style="width: {{ $porcentajeTrasladoCorto }}%;">
            &nbsp;{{ $porcentajeTrasladoCorto }}%
        </div>
    </div>
    
    <div class="leyenda">
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #fd7e14;"></div>
            <span>1 hora o más - {{ $trasladoLargo }} alumnos ({{ $porcentajeTrasladoLargo }}%)</span>
        </div>
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #20c997;"></div>
            <span>Menos de 1 hora - {{ $trasladoCorto }} alumnos ({{ $porcentajeTrasladoCorto }}%)</span>
        </div>
    </div>
</div>

<!-- GRÁFICA 3: Tipos de transporte (cantidad) -->
<div class="grafica-container">
    <div class="grafica-titulo">
        Cantidad de tipos de transporte utilizados por alumno
    </div>
    
    <div class="barra-apilada">
        <div class="segmento segmento-azul" style="width: {{ $porcentajeUnTransporte }}%;">
            &nbsp;{{ $porcentajeUnTransporte }}%
        </div>
        <div class="segmento segmento-morado" style="width: {{ $porcentajeMultiplesTransportes }}%;">
            &nbsp;{{ $porcentajeMultiplesTransportes }}%
        </div>
    </div>
    
    <div class="leyenda">
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #17a2b8;"></div>
            <span>Solo un tipo de transporte - {{ $unSoloTransporte }} alumnos ({{ $porcentajeUnTransporte }}%)</span>
        </div>
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #6f42c1;"></div>
            <span>Más de un tipo de transporte - {{ $multiplesTransportes }} alumnos ({{ $porcentajeMultiplesTransportes }}%)</span>
        </div>
    </div>
</div>

<!-- GRÁFICA 4: Desglose por tipo de transporte -->
<div class="grafica-container">
    <div class="grafica-titulo">
        Tipos de transporte utilizados (desglose)
    </div>
    
    @php
        $totalRegistros = array_sum($transportesContados);
    @endphp
    
    @foreach($transportesContados as $transporte => $cantidad)
        @php
            $porcentaje = $totalRegistros > 0 ? round(($cantidad / $totalRegistros) * 100, 1) : 0;
        @endphp
        <div class="barra-item">
            <span class="barra-label">{{ $transporte }}</span>
            <div class="barra-contenedor">
                <div class="barra" style="width: {{ $porcentaje }}%; background-color: #28a745;"></div>
            </div>
            <span class="barra-porcentaje">{{ $porcentaje }}% ({{ $cantidad }})</span>
        </div>
    @endforeach
</div>

<footer>
    Documento generado por el Sistema General de Gestión Digital de la Información Personal de los Alumnos (SGGDI)
</footer>

</body>
</html>