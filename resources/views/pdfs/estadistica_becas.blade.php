<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadística de Asignaciones de Becas - Folio {{ $folio }}</title>
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
        .segmento-verde {
            background-color: #28a745;
        }
        .segmento-rojo {
            background-color: #dc3545;
        }
        .segmento-azul {
            background-color: #17a2b8;
        }
        .segmento-naranja {
            background-color: #fd7e14;
        }
        .segmento-morado {
            background-color: #6f42c1;
        }
        .segmento-amarillo {
            background-color: #ffc107;
            color: #333;
        }
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
    <h3>INFORME DE ESTADÍSTICAS SOBRE ASIGNACIONES DE BECAS</h3>
</div>

<!-- DATOS DE LA CONSULTA -->
<div class="folio-box">
    <strong>Folio:</strong> {{ $folio }}<br>
    <strong>Conforme a los datos disponibles el día:</strong> {{ $fecha }} a las {{ $hora }}<br>
    <strong>Usuario que generó esta consulta:</strong> {{ $usuario }}
</div>

<!-- TABLA DE BECAS -->
<h4 style="margin-bottom: 10px;">Programas de becas registrados</h4>

<table>
    <thead>
        <tr>
            <th>Nombre de la beca</th>
            <th>Descripción</th>
            <th style="width: 25%; text-align: center;">Alumnos beneficiados</th>
        </tr>
    </thead>
    <tbody>
        @forelse($becasData as $nombre => $beca)
        <tr>
            <td>{{ $nombre }}</td>
            <td>{{ $beca['descripcion'] ?: 'Sin descripción' }}</td>
            <td style="text-align: center;">{{ $beca['cantidad'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" style="text-align: center;">No hay becas registradas</td>
        </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="2" style="text-align: right;"><strong>TOTAL DE ALUMNOS CON BECA ACTIVA</strong></td>
            <td style="text-align: center;"><strong>{{ $alumnosConBeca }}</strong></td>
        </tr>
    </tbody>
</table>

<!-- GRÁFICA 1: Alumnos con/sin beca -->
<div class="grafica-container">
    <div class="grafica-titulo">
        Porcentaje de alumnos que reciben beca en el plantel
    </div>
    
    <div class="barra-apilada">
        <div class="segmento segmento-verde" style="width: {{ $porcentajeConBeca }}%;">
            &nbsp;{{ $porcentajeConBeca }}%
        </div>
        <div class="segmento segmento-rojo" style="width: {{ $porcentajeSinBeca }}%;">
            &nbsp;{{ $porcentajeSinBeca }}%
        </div>
    </div>
    
    <div class="leyenda">
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #28a745;"></div>
            <span>Reciben al menos 1 beca ({{ $alumnosConBeca }} alumnos)</span>
        </div>
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #dc3545;"></div>
            <span>No reciben ninguna beca ({{ $alumnosSinBeca }} alumnos)</span>
        </div>
    </div>
</div>

<!-- GRÁFICA 2: Alumnos con 1 beca vs múltiples becas -->
<div class="grafica-container">
    <div class="grafica-titulo">
        Distribución de cantidad de becas por alumno (alumnos con beca activa)
    </div>
    
    <div class="barra-apilada">
        <div class="segmento segmento-azul" style="width: {{ $porcentajeUnaBeca }}%;">
            &nbsp;{{ $porcentajeUnaBeca }}%
        </div>
        <div class="segmento segmento-morado" style="width: {{ $porcentajeMultiplesBecas }}%;">
            &nbsp;{{ $porcentajeMultiplesBecas }}%
        </div>
    </div>
    
    <div class="leyenda">
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #17a2b8;"></div>
            <span>Reciben exactamente 1 beca ({{ $alumnosConUnaBeca }} alumnos)</span>
        </div>
        <div class="leyenda-item">
            <div class="cuadro-color" style="background-color: #6f42c1;"></div>
            <span>Reciben más de 1 beca ({{ $alumnosConMultiplesBecas }} alumnos)</span>
        </div>
    </div>
</div>

<!-- Alumnos con becas inactivas -->
<div class="grafica-container">
    <div class="grafica-titulo">
        Alumnos con becas inactivas (sin ninguna beca activa)
    </div>
    <p style="margin: 10px 0;">Cantidad de alumnos que solo tienen becas inactivas: <strong>{{ $alumnosConBecasInactivas }}</strong></p>
</div>

<!-- GRÁFICA 3: Correlación beca vs apoyo económico -->
<div class="grafica-container">
    <div class="grafica-titulo">
        Correlación entre beca y apoyo económico familiar
    </div>
    
    <div style="margin-bottom: 15px;">
        <strong>Alumnos CON beca activa:</strong>
        <div class="barra-apilada" style="margin-top: 5px;">
            <div class="segmento segmento-azul" style="width: {{ $porcentajeConBecaApoyo }}%;">
                &nbsp;{{ $porcentajeConBecaApoyo }}%
            </div>
            <div class="segmento segmento-naranja" style="width: {{ $porcentajeConBecaSinApoyo }}%;">
                &nbsp;{{ $porcentajeConBecaSinApoyo }}%
            </div>
        </div>
        <div class="leyenda">
            <div class="leyenda-item">
                <div class="cuadro-color" style="background-color: #17a2b8;"></div>
                <span>Reciben apoyo económico ({{ $conBecaYApoyo }} alumnos)</span>
            </div>
            <div class="leyenda-item">
                <div class="cuadro-color" style="background-color: #fd7e14;"></div>
                <span>No reciben apoyo económico ({{ $conBecaSinApoyo }} alumnos)</span>
            </div>
        </div>
    </div>
    
    <div>
        <strong>Alumnos SIN beca activa:</strong>
        <div class="barra-apilada" style="margin-top: 5px;">
            <div class="segmento segmento-azul" style="width: {{ $porcentajeSinBecaApoyo }}%;">
                &nbsp;{{ $porcentajeSinBecaApoyo }}%
            </div>
            <div class="segmento segmento-naranja" style="width: {{ $porcentajeSinBecaSinApoyo }}%;">
                &nbsp;{{ $porcentajeSinBecaSinApoyo }}%
            </div>
        </div>
        <div class="leyenda">
            <div class="leyenda-item">
                <div class="cuadro-color" style="background-color: #17a2b8;"></div>
                <span>Reciben apoyo económico ({{ $sinBecaConApoyo }} alumnos)</span>
            </div>
            <div class="leyenda-item">
                <div class="cuadro-color" style="background-color: #fd7e14;"></div>
                <span>No reciben apoyo económico ({{ $sinBecaSinApoyo }} alumnos)</span>
            </div>
        </div>
    </div>
</div>

<footer>
    Documento generado por el Sistema General de Gestión Digital de la Información Personal de los Alumnos (SGGDI)
</footer>

</body>
</html>