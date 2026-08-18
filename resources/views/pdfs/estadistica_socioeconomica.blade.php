<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadística Socioeconómica - Folio {{ $folio }}</title>
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
        .segmento-morado { background-color: #6f42c1; }
        .segmento-cyan { background-color: #20c997; }
        .segmento-amarillo { background-color: #ffc107; color: #333; }
        .segmento-gris { background-color: #6c757d; }
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
    <h3>INFORME DE ESTADÍSTICAS SOBRE INFORMACIÓN SOCIOECONÓMICA, FAMILIA Y TRABAJO</h3>
</div>

<!-- DATOS DE LA CONSULTA -->
<div class="folio-box">
    <strong>Folio:</strong> {{ $folio }}<br>
    <strong>Conforme a los datos disponibles el día:</strong> {{ $fecha }} a las {{ $hora }}<br>
    <strong>Usuario que generó esta consulta:</strong> {{ $usuario }}
</div>

<div class="row-graficas">
    <!-- GRÁFICA 1: Auto propio alumno -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Dispone el alumno de automóvil propio?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['autoPropioSi'] }}%;">
                    &nbsp;{{ $porcentajes['autoPropioSi'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['autoPropioNo'] }}%;">
                    &nbsp;{{ $porcentajes['autoPropioNo'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $autoPropioSi }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $autoPropioNo }})</span></div>
            </div>
        </div>
    </div>

    <!-- GRÁFICA 2: Auto propio familia -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Dispone la familia de automóvil propio?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['autoFamiliaSi'] }}%;">
                    &nbsp;{{ $porcentajes['autoFamiliaSi'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['autoFamiliaNo'] }}%;">
                    &nbsp;{{ $porcentajes['autoFamiliaNo'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $autoFamiliaSi }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $autoFamiliaNo }})</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row-graficas">
    <!-- GRÁFICA 3: Hijos -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Tiene hijos el alumno?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['conHijos'] }}%;">
                    &nbsp;{{ $porcentajes['conHijos'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinHijos'] }}%;">
                    &nbsp;{{ $porcentajes['sinHijos'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conHijos }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinHijos }})</span></div>
            </div>
        </div>
    </div>

    <!-- GRÁFICA 4: Apoyo económico -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Recibe apoyo económico de su familia?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['conApoyo'] }}%;">
                    &nbsp;{{ $porcentajes['conApoyo'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinApoyo'] }}%;">
                    &nbsp;{{ $porcentajes['sinApoyo'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conApoyo }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinApoyo }})</span></div>
            </div>
        </div>
    </div>
</div>

<!-- GRÁFICA 5: Gasto comida/transporte -->
<div class="grafica-container">
    <div class="grafica-titulo">Gasto aproximado en comida y transporte al día</div>
    <div class="barra-apilada">
        @php
            $coloresGastos = ['#28a745', '#17a2b8', '#fd7e14', '#ffc107', '#6f42c1', '#dc3545'];
            $i = 0;
        @endphp
        @foreach($gastoRangos as $rango => $cantidad)
            @php
                $porcentaje = $totalAlumnos > 0 ? round(($cantidad / $totalAlumnos) * 100, 1) : 0;
            @endphp
            <div class="segmento" style="width: {{ $porcentaje }}%; background-color: {{ $coloresGastos[$i % count($coloresGastos)] }};">
                &nbsp;{{ $porcentaje }}%
            </div>
            @php $i++; @endphp
        @endforeach
    </div>
    <div class="leyenda">
        @foreach($gastoRangos as $rango => $cantidad)
            @php $i--; @endphp
            <div class="leyenda-item">
                <div class="cuadro-color" style="background: {{ $coloresGastos[$i % count($coloresGastos)] }};"></div>
                <span>{{ $rango }} ({{ $cantidad }})</span>
            </div>
        @endforeach
    </div>
</div>

<div class="row-graficas">
    <!-- GRÁFICA 6: Comparte domicilio con familiar -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Comparte domicilio con algún familiar?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['comparteDomicilio'] }}%;">
                    &nbsp;{{ $porcentajes['comparteDomicilio'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['noComparteDomicilio'] }}%;">
                    &nbsp;{{ $porcentajes['noComparteDomicilio'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $comparteDomicilio }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $noComparteDomicilio }})</span></div>
            </div>
        </div>
    </div>

    <!-- GRÁFICA 7: Casa propia -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Cuenta la familia con casa propia?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['casaPropiaSi'] }}%;">
                    &nbsp;{{ $porcentajes['casaPropiaSi'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['casaPropiaNo'] }}%;">
                    &nbsp;{{ $porcentajes['casaPropiaNo'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $casaPropiaSi }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $casaPropiaNo }})</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row-graficas">
    <!-- GRÁFICA 8: Trabajo -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿El alumno posee algún empleo actualmente?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['conTrabajo'] }}%;">
                    &nbsp;{{ $porcentajes['conTrabajo'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinTrabajo'] }}%;">
                    &nbsp;{{ $porcentajes['sinTrabajo'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conTrabajo }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinTrabajo }})</span></div>
            </div>
        </div>
    </div>

    <!-- GRÁFICA 9: Comidas diarias -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Cuántas comidas hace al día?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['comidas3'] }}%;">
                    &nbsp;{{ $porcentajes['comidas3'] }}%
                </div>
                <div class="segmento segmento-azul" style="width: {{ $porcentajes['comidasMenos3'] }}%;">
                    &nbsp;{{ $porcentajes['comidasMenos3'] }}%
                </div>
                <div class="segmento segmento-naranja" style="width: {{ $porcentajes['comidasMas3'] }}%;">
                    &nbsp;{{ $porcentajes['comidasMas3'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>3 comidas ({{ $comidas3 }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#17a2b8;"></div><span>Menos de 3 ({{ $comidasMenos3 }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#fd7e14;"></div><span>Más de 3 ({{ $comidasMas3 }})</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row-graficas">
    <!-- GRÁFICA 10: Internet -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Cuenta con servicio de Internet?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['conInternet'] }}%;">
                    &nbsp;{{ $porcentajes['conInternet'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinInternet'] }}%;">
                    &nbsp;{{ $porcentajes['sinInternet'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conInternet }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinInternet }})</span></div>
            </div>
        </div>
    </div>

    <!-- GRÁFICA 11: Teléfono celular personal -->
    <div class="col-grafica">
        <div class="grafica-container">
            <div class="grafica-titulo">¿Tiene registrado un teléfono celular personal?</div>
            <div class="barra-apilada">
                <div class="segmento segmento-verde" style="width: {{ $porcentajes['conTelefono'] }}%;">
                    &nbsp;{{ $porcentajes['conTelefono'] }}%
                </div>
                <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinTelefono'] }}%;">
                    &nbsp;{{ $porcentajes['sinTelefono'] }}%
                </div>
            </div>
            <div class="leyenda">
                <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conTelefono }})</span></div>
                <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinTelefono }})</span></div>
            </div>
        </div>
    </div>
</div>

<!-- GRÁFICA 12: Dispositivos para tareas -->
<div class="grafica-container">
    <div class="grafica-titulo">¿Cuenta con dispositivos para sus tareas (celular, computadora o tablet)?</div>
    <div class="barra-apilada">
        <div class="segmento segmento-verde" style="width: {{ $porcentajes['conDispositivos'] }}%;">
            &nbsp;{{ $porcentajes['conDispositivos'] }}%
        </div>
        <div class="segmento segmento-rojo" style="width: {{ $porcentajes['sinDispositivos'] }}%;">
            &nbsp;{{ $porcentajes['sinDispositivos'] }}%
        </div>
    </div>
    <div class="leyenda">
        <div class="leyenda-item"><div class="cuadro-color" style="background:#28a745;"></div><span>Sí ({{ $conDispositivos }})</span></div>
        <div class="leyenda-item"><div class="cuadro-color" style="background:#dc3545;"></div><span>No ({{ $sinDispositivos }})</span></div>
    </div>
</div>

<footer>
    Documento generado por el Sistema General de Gestión Digital de la Información Personal de los Alumnos (SGGDI)<br>
    Total de alumnos activos considerados: {{ $totalAlumnos }}
</footer>

</body>
</html>