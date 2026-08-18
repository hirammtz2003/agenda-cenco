<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadística de Salud y Problemas de Aprendizaje - Folio {{ $folio }}</title>
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
            font-size: 12pt;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 10pt;
            font-weight: normal;
        }
        .folio-box {
            background-color: #f0f0f0;
            padding: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #a01508;
            font-size: 8pt;
        }
        .grafica-container {
            margin: 15px 0;
            page-break-inside: avoid;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .grafica-titulo {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 9pt;
            background-color: #f8f9fa;
            padding: 4px 8px;
            border-left: 3px solid #a01508;
        }
        .barra-apilada {
            width: 100%;
            background-color: #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin: 8px 0;
            display: flex;
        }
        .segmento {
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 7pt;
            font-weight: bold;
        }
        .segmento-verde { background-color: #28a745; }
        .segmento-rojo { background-color: #dc3545; }
        .segmento-azul { background-color: #17a2b8; }
        .segmento-naranja { background-color: #fd7e14; }
        .leyenda {
            display: flex;
            gap: 15px;
            margin-top: 5px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .leyenda-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 7pt;
        }
        .cuadro-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }
        .row-graficas {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .col-grafica {
            flex: 1;
            min-width: 280px;
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
    <h3>INFORME DE ESTADÍSTICAS SOBRE SALUD Y PROBLEMAS DE APRENDIZAJE</h3>
</div>

<!-- DATOS DE LA CONSULTA -->
<div class="folio-box">
    <strong>Folio:</strong> {{ $folio }}<br>
    <strong>Conforme a los datos disponibles el día:</strong> {{ $fecha }} a las {{ $hora }}<br>
    <strong>Usuario que generó esta consulta:</strong> {{ $usuario }}
</div>

<div class="row-graficas">
    <!-- GRÁFICA 1: Problemas de aprendizaje -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Tiene diagnosticado algún problema de aprendizaje?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['problemasAprendizaje'] }}%;">
                    {{ $porcentajes['problemasAprendizaje'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinProblemasAprendizaje'] }}%;">
                    {{ $porcentajes['sinProblemasAprendizaje'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conProblemasAprendizaje }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinProblemasAprendizaje }})</span></div>
            </div>
        </div>
    </div>

    <!-- GRÁFICA 2: Limitante físico -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Padece algún limitante físico?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['limitanteFisico'] }}%;">
                    {{ $porcentajes['limitanteFisico'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinLimitanteFisico'] }}%;">
                    {{ $porcentajes['sinLimitanteFisico'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conLimitanteFisico }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinLimitanteFisico }})</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row-graficas">
    <!-- GRÁFICA 3: Problema auditivo -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Padece algún problema auditivo?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['problemaAuditivo'] }}%;">
                    {{ $porcentajes['problemaAuditivo'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinProblemaAuditivo'] }}%;">
                    {{ $porcentajes['sinProblemaAuditivo'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conProblemaAuditivo }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinProblemaAuditivo }})</span></div>
            </div>
        </div>
    </div>

    <!-- GRÁFICA 4: Adicción -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Padece alguna adicción?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['adiccion'] }}%;">
                    {{ $porcentajes['adiccion'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinAdiccion'] }}%;">
                    {{ $porcentajes['sinAdiccion'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conAdiccion }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinAdiccion }})</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row-graficas">
    <!-- GRÁFICA 5: Padecimiento emocional -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Padece algún padecimiento emocional?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['padecimientoEmocional'] }}%;">
                    {{ $porcentajes['padecimientoEmocional'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinPadecimientoEmocional'] }}%;">
                    {{ $porcentajes['sinPadecimientoEmocional'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conPadecimientoEmocional }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinPadecimientoEmocional }})</span></div>
            </div>
        </div>
    </div>

    <!-- GRÁFICA 6: Enfermedad actual -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Padece alguna enfermedad actualmente?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['enfermedadActual'] }}%;">
                    {{ $porcentajes['enfermedadActual'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinEnfermedadActual'] }}%;">
                    {{ $porcentajes['sinEnfermedadActual'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conEnfermedadActual }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinEnfermedadActual }})</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row-graficas">
    <!-- GRÁFICA 7: Síntomas de salud mental -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Ha mostrado síntomas que afecten su salud mental?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['sintomas'] }}%;">
                    {{ $porcentajes['sintomas'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinSintomas'] }}%;">
                    {{ $porcentajes['sinSintomas'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conSintomas }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinSintomas }})</span></div>
            </div>
        </div>
    </div>

    <!-- GRÁFICA 8: Medicamento controlado -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Toma algún medicamento controlado?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['medicamento'] }}%;">
                    {{ $porcentajes['medicamento'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinMedicamento'] }}%;">
                    {{ $porcentajes['sinMedicamento'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conMedicamento }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinMedicamento }})</span></div>
            </div>
        </div>
    </div>
</div>

<!-- GRÁFICA 9: Alergia a medicamento -->
<div class="grafica-container">
    <div class="grafica-titulo">¿Alergia a algún medicamento?</div>
    <div class="barra-apilada">
        <div class="segmento segmento-verde" style="width: {{ $porcentajes['alergiaMedicamento'] }}%;">
            {{ $porcentajes['alergiaMedicamento'] }}%
        </div>
        <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinAlergiaMedicamento'] }}%;">
            {{ $porcentajes['sinAlergiaMedicamento'] }}%
        </div>
    </div>
    <div class="leyenda">
        <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conAlergiaMedicamento }})</span></div>
        <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinAlergiaMedicamento }})</span></div>
    </div>
</div>

<footer>
    Documento generado por el Sistema General de Gestión Digital de la Información Personal de los Alumnos (SGGDI)<br>
    Total de alumnos activos considerados: {{ $totalAlumnos }}
</footer>

</body>
</html>