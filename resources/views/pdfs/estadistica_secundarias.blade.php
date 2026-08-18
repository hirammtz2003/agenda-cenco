<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadística de Secundarias de Procedencia - Folio {{ $folio }}</title>
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
        .barra-item {
            margin-bottom: 6px;
            clear: both;
        }
        .barra-label {
            display: inline-block;
            width: 40%;
            font-size: 7.5pt;
            vertical-align: middle;
        }
        .barra-contenedor {
            display: inline-block;
            width: 45%;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            vertical-align: middle;
        }
        .barra-porcentaje {
            display: inline-block;
            width: 12%;
            text-align: right;
            font-size: 7.5pt;
            margin-left: 5px;
            vertical-align: middle;
        }
        .barra {
            height: 16px;
            width: 0%;
            border-radius: 5px;
        }
        .grupo-graficas {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }
        .grafica-columna {
            flex: 1;
            min-width: 250px;
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
    <h3>INFORME DE ESTADÍSTICAS SOBRE SECUNDARIAS DE PROCEDENCIA</h3>
</div>

<!-- DATOS DE LA CONSULTA -->
<div class="folio-box">
    <strong>Folio:</strong> {{ $folio }}<br>
    <strong>Conforme a los datos disponibles el día:</strong> {{ $fecha }} a las {{ $hora }}<br>
    <strong>Usuario que generó esta consulta:</strong> {{ $usuario }}
</div>

<!-- TABLA GENERAL -->
<h4 style="margin-bottom: 10px;">Procedencia de los alumnos activos del plantel</h4>

<table>
    <thead>
        <tr>
            <th>Nombre de la secundaria</th>
            <th style="width: 20%; text-align: center;">Total de alumnos</th>
            <th style="width: 20%; text-align: center;">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        @foreach($secundariasData as $nombre => $datos)
        <tr>
            <td>{{ $nombre }}</td>
            <td style="text-align: center;">{{ $datos['total'] }}</td>
            <td style="text-align: center;">
                @php
                    $porcentaje = $totalGlobal > 0 ? round(($datos['total'] / $totalGlobal) * 100, 1) : 0;
                @endphp
                {{ $porcentaje }}%
            </td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td style="text-align: right;"><strong>TOTAL GENERAL</strong></td>
            <td style="text-align: center;"><strong>{{ $totalGlobal }}</strong></td>
            <td style="text-align: center;"><strong>100%</strong></td>
        </tr>
    </tbody>
</table>

<!-- GRÁFICAS POR GRADO -->
<h4 style="margin-bottom: 15px;">Distribución de procedencia por grado académico</h4>

<div class="grupo-graficas">
    @php
        $gradosKeys = ['grado1', 'grado2', 'grado3'];
        $gradosNombres = ['1° y 2° Semestre', '3° y 4° Semestre', '5° y 6° Semestre'];
    @endphp
    
    @foreach($gradosKeys as $gradoIndex => $gradoKey)
    <div class="grafica-columna">
        <div class="grafica-titulo" style="text-align: center;">
            {{ $gradosNombres[$gradoIndex] }}
            <span style="font-size: 8pt;">(Total: {{ $totalesGrado[$gradoKey] }} alumnos)</span>
        </div>
        
        @php
            $totalGrado = $totalesGrado[$gradoKey];
        @endphp
        
        @if($totalGrado > 0)
            @foreach($secundariasData as $nombre => $datos)
                @php
                    $cantidad = $datos[$gradoKey];
                    $porcentaje = $totalGrado > 0 ? round(($cantidad / $totalGrado) * 100, 1) : 0;
                    $color = $colores[$nombre] ?? '#6c757d';
                @endphp
                
                @if($cantidad > 0)
                <div class="barra-item">
                    <span class="barra-label">{{ $nombre }}</span>
                    <div class="barra-contenedor">
                        <div class="barra" style="width: {{ $porcentaje }}%; background-color: {{ $color }};"></div>
                    </div>
                    <span class="barra-porcentaje">{{ $porcentaje }}% ({{ $cantidad }})</span>
                </div>
                @endif
            @endforeach
        @else
            <div style="text-align: center; color: #6c757d; padding: 20px;">
                No hay alumnos en este grado
            </div>
        @endif
    </div>
    @endforeach
</div>

<footer>
    Documento generado por el Sistema General de Gestión Digital de la Información Personal de los Alumnos (SGGDI)
</footer>

</body>
</html>