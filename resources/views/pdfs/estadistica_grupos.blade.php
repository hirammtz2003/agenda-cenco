<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadística de Grupos y Carreras - Folio {{ $folio }}</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #a01508;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-size: 9pt;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 6px;
            vertical-align: middle;
            text-align: center;
        }
        td.text-left {
            text-align: left;
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
        }
        .barra-item {
            margin-bottom: 8px;
        }
        .barra-label {
            display: inline-block;
            width: 45%;
            font-size: 8pt;
        }
        .barra-contenedor {
            display: inline-block;
            width: 40%;
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
        }
        .barra {
            height: 18px;
            background-color: #a01508;
            width: 0%;
            border-radius: 5px;
        }
        .chart-row {
            margin-bottom: 5px;
        }
        .chart-label {
            font-weight: bold;
            width: 35%;
            display: inline-block;
            font-size: 8pt;
        }
        .chart-bar {
            width: 50%;
            display: inline-block;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            vertical-align: middle;
        }
        .chart-percent {
            width: 12%;
            display: inline-block;
            text-align: right;
            font-size: 8pt;
        }
        .bar-segment {
            height: 20px;
            display: inline-block;
            float: left;
        }
        footer {
            text-align: center;
            font-size: 7pt;
            color: #6c757d;
            margin-top: 20px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

<!-- ENCABEZADO -->
<div class="header">
    <h1>COLEGIO DE ESTUDIOS CIENTÍFICOS Y TECNOLÓGICOS</h1>
    <h2>DEL ESTADO DE ZACATECAS - PLANTEL RÍO GRANDE</h2>
    <h3>INFORME DE ESTADÍSTICAS SOBRE GRUPOS Y CARRERAS</h3>
</div>

<!-- DATOS DE LA CONSULTA -->
<div class="folio-box">
    <strong>Folio:</strong> {{ $folio }}<br>
    <strong>Conforme a los datos disponibles el día:</strong> {{ $fecha }} a las {{ $hora }}<br>
    <strong>Usuario que generó esta consulta:</strong> {{ $usuario }}
</div>

<!-- TABLA PRINCIPAL -->
<h4 style="margin-bottom: 10px;">Distribución de alumnos por carrera, semestre y grupo</h4>

<table>
    <thead>
        <tr>
            <th>Carrera</th>
            <th>Semestre</th>
            <th>Grupo A</th>
            <th>Grupo B</th>
            <th>Total por semestre</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalGeneral = 0;
            // Precalcular cuántas filas tendrá cada carrera
            $filasPorCarrera = [];
            foreach ($carreras as $carrera) {
                $contador = 0;
                foreach ($semestres as $semestre) {
                    $cantidadA = isset($datos[$carrera][$semestre]['A']) ? $datos[$carrera][$semestre]['A'] : 0;
                    $cantidadB = isset($datos[$carrera][$semestre]['B']) ? $datos[$carrera][$semestre]['B'] : 0;
                    if ($cantidadA + $cantidadB > 0) {
                        $contador++;
                    }
                }
                $filasPorCarrera[$carrera] = $contador > 0 ? $contador : 0;
            }
        @endphp
        
        @foreach($carreras as $carrera)
            @php
                $primeraFila = true;
                $carreraTotal = 0;
                $semestresMostrados = 0;
            @endphp
            
            @foreach($semestres as $semestre)
                @php
                    $cantidadA = isset($datos[$carrera][$semestre]['A']) ? $datos[$carrera][$semestre]['A'] : 0;
                    $cantidadB = isset($datos[$carrera][$semestre]['B']) ? $datos[$carrera][$semestre]['B'] : 0;
                    $totalSemestre = $cantidadA + $cantidadB;
                    $carreraTotal += $totalSemestre;
                    $totalGeneral += $totalSemestre;
                @endphp
                
                @if($totalSemestre > 0)
                    @php
                        $semestresMostrados++;
                    @endphp
                    <tr>
                        @if($primeraFila)
                            <td rowspan="{{ $filasPorCarrera[$carrera] + 1 }}" style="vertical-align: middle; background-color: #f8f9fa; font-weight: bold;">
                                {{ $carrera }}
                            </td>
                            @php $primeraFila = false; @endphp
                        @endif                        
                        <td>{{ $semestre }}</td>
                        <td>{{ $cantidadA > 0 ? $cantidadA : '—' }}</td>
                        <td>{{ $cantidadB > 0 ? $cantidadB : '—' }}</td>
                        <td><strong>{{ $totalSemestre }}</strong></td>
                    </tr>
                @endif
            @endforeach
            
            @if($semestresMostrados > 0)
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="4" style="text-align: right;">Total: {{ $carreraTotal }}</td>
                </tr>
            @endif
        @endforeach
        
        <tr style="background-color: #a01508; color: white; font-weight: bold;">
            <td colspan="4" style="text-align: right;">TOTAL GENERAL DE ALUMNOS:</td>
            <td>{{ $totalGeneral }}</td>
        </tr>
    </tbody>
</table>

<!-- Cálculo de rowspans para la tabla -->
@php
    $carrerasDatos = [];
    foreach ($carreras as $carrera) {
        $semestresConDatos = 0;
        foreach ($semestres as $semestre) {
            $cantidadA = isset($datos[$carrera][$semestre]['A']) ? $datos[$carrera][$semestre]['A'] : 0;
            $cantidadB = isset($datos[$carrera][$semestre]['B']) ? $datos[$carrera][$semestre]['B'] : 0;
            if ($cantidadA + $cantidadB > 0) {
                $semestresConDatos++;
            }
        }
        $carrerasDatos[$carrera]['rowspan'] = $semestresConDatos > 0 ? $semestresConDatos + 1 : 0;
    }
@endphp

<!-- GRÁFICAS -->
<div class="grafica-container">
    <div class="grafica-titulo">
        <i class="fas fa-chart-bar"></i> Distribución de alumnos por carrera
    </div>
    
    @foreach($carreraTotales as $carrera => $total)
        @php
            $porcentaje = $totalGeneral > 0 ? round(($total / $totalGeneral) * 100, 1) : 0;
            $color = $colores[$carrera] ?? '#6c757d';
        @endphp
        <div class="barra-item">
            <span class="barra-label">{{ $carrera }}</span>
            <div class="barra-contenedor">
                <div class="barra" style="width: {{ $porcentaje }}%; background-color: {{ $color }};"></div>
            </div>
            <span class="barra-porcentaje">{{ $porcentaje }}% ({{ $total }})</span>
        </div>
    @endforeach
</div>

<div class="grafica-container">
    <div class="grafica-titulo">
        <i class="fas fa-chart-bar"></i> Distribución de alumnos por semestre (según corresponda por la época del año)
    </div>
    
    @php
        $grado1 = ($semestreTotales['1°'] ?? 0) + ($semestreTotales['2°'] ?? 0);
        $grado2 = ($semestreTotales['3°'] ?? 0) + ($semestreTotales['4°'] ?? 0);
        $grado3 = ($semestreTotales['5°'] ?? 0) + ($semestreTotales['6°'] ?? 0);
        $grados = [
            '1°/2° Semestre' => $grado1,
            '3°/4° Semestre' => $grado2,
            '5°/6° Semestre' => $grado3
        ];
        $coloresGrado = ['#28a745', '#fd7e14', '#a01508'];
        $indiceGrado = 0;
    @endphp
    
    @foreach($grados as $nombre => $total)
        @php
            $porcentaje = $totalGeneral > 0 ? round(($total / $totalGeneral) * 100, 1) : 0;
            $color = $coloresGrado[$indiceGrado] ?? '#6c757d';
            $indiceGrado++;
        @endphp
        <div class="barra-item">
            <span class="barra-label">{{ $nombre }}</span>
            <div class="barra-contenedor">
                <div class="barra" style="width: {{ $porcentaje }}%; background-color: {{ $color }};"></div>
            </div>
            <span class="barra-porcentaje">{{ $porcentaje }}% ({{ $total }})</span>
        </div>
    @endforeach
</div>

<footer>
    Documento generado por el Sistema General de Gestión Digital de la Información Personal de los Alumnos (SGGDI)
</footer>

</body>
</html>