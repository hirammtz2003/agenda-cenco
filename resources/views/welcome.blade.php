@extends('layouts.app')

@section('title', 'Inicio - SADHCC')

@php
    $usuario = Auth::user();
    $esAdmin = $usuario && $usuario->tipo === 'Administrador';
@endphp

@push('styles')
<style>
    .welcome-section {
        background: linear-gradient(135deg, #4e0d0dff 0%, #a01508 100%);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
        border-radius: 10px;
    }
    .module-section {
        background-color: #f8f9fa;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .module-card {
        transition: transform 0.2s;
        height: 100%;
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .module-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .module-card .card-body {
        padding: 1.5rem;
    }
    .module-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    //Notificaciones
    .practicas-tabla {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }
    .practicas-tabla th {
        background-color: #f1f3f5;
        color: #495057;
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .practicas-tabla td {
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }
    .practicas-tabla tr:last-child td {
        border-bottom: none;
    }
    .practicas-tabla .fila-en-curso {
        background-color: #f8d7da !important; /* Rojo */
    }
    .practicas-tabla .fila-siguiente {
        background-color: #ffe5d0 !important; /* Naranja */
    }
    .practicas-tabla .fila-despues {
        background-color: #fff3cd !important; /* Amarillo */
    }
    .practicas-tabla .fila-posterior {
        background-color: #d1e7dd !important; /* Verde */
    }
    .practicas-tabla .badge-mini {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        background-color: #e9ecef;
        color: #495057;
        border-radius: 4px;
        margin-right: 3px;
    }
    .practicas-tabla .hora-col {
        font-weight: 600;
        color: #212529;
        white-space: nowrap;
    }
    .practicas-tabla .herramientas-col,
    .practicas-tabla .materiales-col {
        max-width: 150px;
        font-size: 0.78rem;
        color: #6c757d;
    }
    .practicas-tabla .practica-vacia {
        text-align: center;
        color: #adb5bd;
        font-style: italic;
        padding: 1rem !important;
    }
    .practicas-tabla .lab-titulo {
        background-color: #e7f1ff;
        color: #0d6efd;
        padding: 0.4rem 0.75rem;
        font-weight: 700;
        font-size: 0.85rem;
        border-top: 1px solid #b6d4fe;
        border-bottom: 1px solid #b6d4fe;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
    <!--Welcome Section -->
    <div class="welcome-section">
        <div class="container text-center">
            <h1>Sistema de Agendado Digital de Horarios del Centro de Cómputo</h1>
            <p class="lead">Plataforma Oficial de Reservación</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Mensaje de bienvenida -->
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h5><i class="fas fa-info-circle"></i> ¡Bienvenido(a) al sistema! Te encuentras en la página de INICIO</h5>
            <p class="mb-0">
                Seleccione alguna de las siguientes opciones. Los módulos y funciones disponibles dependen del tipo de usuario 
                y privilegios del sistema asignados.
            </p>
        </div>

        <!-- ========== PRÁCTICAS DE HOY (solo admin) ========== -->
        @if($esAdmin)
        <div class="module-section mb-4" id="practicasHoySection" style="display:none;">
            <h5 class="mb-3"><i class="fas fa-calendar-day me-2 text-primary"></i> PRÁCTICAS DE HOY:</h5>
            <div id="practicasHoyContainer">
                <!-- Se llena con JS -->
            </div>
        </div>
        @endif

        <!-- Módulos disponibles -->
        <div class="module-section">
            <h5 class="mb-4"><i class="fas fa-cubes"></i> MÓDULOS DISPONIBLES:</h5>
            
            <div class="row g-4">
                <!-- Módulo de Administración de Usuarios -->
                @if($esAdmin)
                <div class="col-md-4">
                    <div class="card module-card bg-primary text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <h5>Administración de Usuarios</h5>
                            <p class="small">Gestiona los usuarios del sistema</p>
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Módulo de Configuración de Grupos -->
                <div class="col-md-4">
                    <div class="card module-card bg-success text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Configuración de Grupos</h5>
                            <p class="small">Administra los grupos y clases</p>
                            <a href="{{ route('admin.grupos.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>                

                <!-- Módulo de Configuración de Materias y Laboratorios -->
                <div class="col-md-4">
                    <div class="card module-card bg-danger text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fa-solid fa-laptop-file"></i>
                            </div>
                            <h5>Configuración de Materias y Laboratorios</h5>
                            <p class="small">Gestiona datos de materias y lugares de trabajo</p>
                            <a href="{{ route('admin.materias.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Módulo de Carga de Horarios -->
                <div class="col-md-4">
                    <div class="card module-card bg-secondary text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <h5>Carga de Horarios</h5>
                            <p class="small">Gestiona horarios y días inhábiles</p>
                            <a href="{{ route('admin.horarios.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Módulo de Solicitud de Reserva de Horas -->
                <div class="col-md-4">
                    <div class="card module-card bg-warning text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </div>
                            <h5>Solicitud de Reserva de Horas</h5>
                            <p class="small">Formulario para apartados</p>
                            <a href="{{ route('solicitud.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Módulo de Historial de Solicitudes -->
                <div class="col-md-4">
                    <div class="card module-card bg-info text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <h5>Historial de Solicitudes</h5>
                            <p class="small">Consulta y gestiona las solicitudes</p>
                            <a href="{{ route('historial.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Horario General -->
                <div class="col-md-4">
                    <div class="card module-card bg-dark text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <h5>Horario General</h5>
                            <p class="small">Consulta los horarios de todos los laboratorios</p>
                            <a href="{{ route('admin.horario-general.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    @if($esAdmin)
    document.addEventListener('DOMContentLoaded', function() {
        const section = document.getElementById('practicasHoySection');
        const container = document.getElementById('practicasHoyContainer');
        if (!section || !container) return;

        // Horas de inicio y fin de cada bloque
        const HORAS = {
            1: { ini: '08:00', fin: '08:50' },
            2: { ini: '08:50', fin: '09:40' },
            3: { ini: '09:40', fin: '10:30' },
            4: { ini: '11:00', fin: '11:50' },
            5: { ini: '11:50', fin: '12:40' },
            6: { ini: '12:40', fin: '13:30' },
            7: { ini: '13:30', fin: '14:20' },
            8: { ini: '14:20', fin: '15:00' },
        };

        let timerInterval = null;

        function cargarDatos() {
            fetch('{{ route("admin.horario-general.welcome-datos") }}', {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    render(data);
                    section.style.display = 'block';
                }
            })
            .catch(e => console.error('Error en welcome datos:', e));
        }

        function render(data) {
            let html = '';

            data.laboratorios.forEach(item => {
                html += `<div class="practicas-tabla-wrapper">
                    <div class="lab-titulo"><i class="fas fa-flask me-2"></i>${item.laboratorio.nombre}</div>`;

                if (item.es_inhabil) {
                    html += `<table class="practicas-tabla">
                        <tr><td class="practica-vacia">Día inhábil — No hay clases programadas.</td></tr>
                    </table>`;
                } else if (!item.practicas || item.practicas.length === 0) {
                    html += `<table class="practicas-tabla">
                        <tr><td class="practica-vacia">No hay prácticas programadas para hoy.</td></tr>
                    </table>`;
                } else {
                    // Calcular minutos actuales
                    const ahora = new Date();
                    const minutosAhora = ahora.getHours() * 60 + ahora.getMinutes();

                    // Filtrar solo las que no han terminado
                    const activas = item.practicas.filter(p => {
                        const h = HORAS[p.hora];
                        if (!h) return false;
                        const [hf, mf] = h.fin.split(':').map(Number);
                        const finMin = hf * 60 + mf;
                        return minutosAhora < finMin;
                    });

                    if (activas.length === 0) {
                        html += `<table class="practicas-tabla">
                            <tr><td class="practica-vacia">No hay más prácticas por hoy.</td></tr>
                        </table>`;
                    } else {
                        // Determinar si la primera (fila 0) está en curso
                        const h0 = HORAS[activas[0].hora];
                        const [hi0, mi0] = h0.ini.split(':').map(Number);
                        const [hf0, mf0] = h0.fin.split(':').map(Number);
                        const inicio0 = hi0 * 60 + mi0;
                        const fin0 = hf0 * 60 + mf0;
                        const primeraEnCurso = minutosAhora >= inicio0 && minutosAhora < fin0;

                        html += `<table class="practicas-tabla">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Docente</th>
                                    <th>Grupo</th>
                                    <th>Materia</th>
                                    <th>Herramientas</th>
                                    <th>Materiales</th>
                                </tr>
                            </thead>
                            <tbody>`;

                        activas.forEach((p, idx) => {
                            let claseFila = '';

                            if (idx === 0) {
                                // Fila 0: rojo si está en curso, naranja si es la siguiente
                                claseFila = primeraEnCurso ? 'fila-en-curso' : 'fila-siguiente';
                            } else if (idx === 1) {
                                // Fila 1: siempre amarillo
                                claseFila = 'fila-despues';
                            } else {
                                // Fila 2+: verde
                                claseFila = 'fila-posterior';
                            }

                            const herramientasHtml = p.herramientas.length > 0
                                ? p.herramientas.map(h => `<span class="badge-mini">${h}</span>`).join('')
                                : '<span class="text-muted">—</span>';

                            const materialesHtml = p.materiales.length > 0
                                ? p.materiales.map(m => `<span class="badge-mini">${m}</span>`).join('')
                                : '<span class="text-muted">—</span>';

                            html += `<tr class="${claseFila}">
                                <td class="hora-col">${p.hora_legible}</td>
                                <td>${p.docente}</td>
                                <td>${p.grupo}</td>
                                <td>${p.materia}</td>
                                <td class="herramientas-col">${herramientasHtml}</td>
                                <td class="materiales-col">${materialesHtml}</td>
                            </tr>`;
                        });

                        html += `</tbody></table>`;
                    }
                }

                html += `</div>`;
            });

            container.innerHTML = html;
        }

        // Cargar datos iniciales
        cargarDatos();

        // Recargar cada 60 segundos para actualizar estados
        setInterval(cargarDatos, 60000);
    });
    @endif
</script>
@endpush