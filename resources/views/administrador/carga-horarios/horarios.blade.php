@extends('layouts.app')

@section('title', 'Carga de Horarios - SADHCC')

@php
    $usuario = Auth::user();
    $esAdmin = $usuario && $usuario->tipo === 'Administrador';
    
    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Carga de Horarios',
            'url' => null,
            'icon' => 'fa-clock'
        ]
    ];
@endphp

@push('styles')
<style>
    .module-section {
        background-color: #f8f9fa;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .table-container {
        max-height: 350px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    .table-container thead th {
        position: sticky;
        top: 0;
        background-color: #212529;
        color: white;
        z-index: 10;
        font-size: 0.85rem;
    }
    .table-container tbody td {
        font-size: 0.85rem;
        vertical-align: middle;
    }
    .filter-badge {
        background-color: #e9ecef;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-block;
        font-size: 0.85rem;
    }
    .action-buttons {
        white-space: nowrap;
    }
    .form-row {
        background-color: white;
        padding: 1.25rem;
        border-radius: 10px;
        margin-bottom: 1rem;
    }
    .counter-badge {
        font-size: 0.75rem;
        color: #6c757d;
    }
    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 0 0 8px 8px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .search-result-item {
        padding: 8px 15px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }
    .search-result-item:hover {
        background-color: #f0f0f0;
    }
    .search-result-item.highlighted {
        background-color: #ffc107 !important;
        color: #212529;
    }
</style>
@endpush

@section('content')
@if($esAdmin)
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    @include('partials.breadcrumb')
    
    <!-- Título del menú -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="border-bottom pb-3">
                <i class="fas fa-clock me-2 text-secondary"></i>
                CARGA DE HORARIOS
            </h2>
        </div>
    </div>
    
    <!-- ============ SECCIÓN DE FORMULARIOS LADO A LADO ============ -->
    <div class="row g-4 mb-4">
        
        <!-- FORMULARIO HORARIOS (Izquierda) -->
        <div class="col-lg-8">
            <div class="module-section h-100">
                <h5 class="mb-4"><i class="fa-solid fa-hourglass-half me-2"></i> REGISTRO Y EDICIÓN DE HORARIOS</h5>
                
                <div class="form-row">
                    <input type="hidden" id="horarioId" value="">

                    <!-- Hora, Día y Hora Fija -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Hora *</label>
                            <select id="horarioHora" class="form-select">
                                <option value="">—Seleccione—</option>
                                <option value="1">1 (8:00 - 8:50)</option>
                                <option value="2">2 (8:50 - 9:40)</option>
                                <option value="3">3 (9:40 - 10:30)</option>
                                <option value="4">4 (11:00 - 11:50)</option>
                                <option value="5">5 (11:50 - 12:40)</option>
                                <option value="6">6 (12:40 - 13:30)</option>
                                <option value="7">7 (13:30 - 14:20)</option>
                                <option value="8">8 (14:20 - 15:00)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Día *</label>
                            <select id="horarioDia" class="form-select">
                                <option value="">—Seleccione—</option>
                                <option value="L">Lunes</option>
                                <option value="M">Martes</option>
                                <option value="X">Miércoles</option>
                                <option value="J">Jueves</option>
                                <option value="V">Viernes</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="horarioHoraFija">
                                <label class="form-check-label" for="horarioHoraFija">
                                    <i class="fas fa-layer-group"></i> Hora fija
                                </label>
                                <small class="d-block text-muted">Se repite todas las semanas</small>
                            </div>
                        </div>
                    </div>

                    <!-- Grupo con autocomplete -->
                    <div class="row mb-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label">Grupo *</label>
                            <input type="text" 
                                   id="buscarGrupo"
                                   class="form-control" 
                                   placeholder="Escriba semestre, grupo o carrera..."
                                   autocomplete="off">
                            <input type="hidden" id="horarioGrupoId" value="">
                            <div id="grupoResults" class="search-results-dropdown"></div>
                            <small class="text-muted">Comience a escribir para buscar</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div id="grupoSeleccionado" style="display:none;" class="w-100">
                                <div class="alert alert-info mb-0 py-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong id="grupoNombreDisplay"></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maestro con autocomplete -->
                    <div class="row mb-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label">Maestro *</label>
                            <input type="text" 
                                   id="buscarMaestro"
                                   class="form-control"
                                   placeholder="Escriba nombre o número de empleado..."
                                   autocomplete="off">
                            <input type="hidden" id="horarioMaestroId" value="">
                            <div id="maestroResults" class="search-results-dropdown"></div>
                            <small class="text-muted">Comience a escribir para buscar</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div id="maestroSeleccionado" style="display:none;" class="w-100">
                                <div class="alert alert-info mb-0 py-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong id="maestroNombreDisplay"></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Materia con autocomplete -->
                    <div class="row mb-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label">Materia *</label>
                            <input type="text" 
                                   id="buscarMateria"
                                   class="form-control"
                                   placeholder="Escriba el nombre de la materia..."
                                   autocomplete="off">
                            <input type="hidden" id="horarioMateriaId" value="">
                            <div id="materiaResults" class="search-results-dropdown"></div>
                            <small class="text-muted">Comience a escribir para buscar</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div id="materiaSeleccionada" style="display:none;" class="w-100">
                                <div class="alert alert-info mb-0 py-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong id="materiaNombreDisplay"></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="fas fa-lock me-1"></i>Contraseña de Administrador *
                            </label>
                            <input type="password" 
                                   class="form-control form-control-sm" 
                                   id="horarioPassword" 
                                   placeholder="Ingrese su contraseña"
                                   autocomplete="new-password">
                        </div>
                        <div class="col-md-7 d-flex align-items-end justify-content-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-secondary btn-sm" id="btnGuardarHorario">
                                    <i class="fas fa-save me-1"></i>Guardar Horario
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCancelarHorario" style="display:none;">
                                    <i class="fas fa-times me-1"></i>Cancelar Edición
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFormHorario()">
                                    <i class="fas fa-eraser me-1"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DÍAS INHÁBILES (Derecha) -->
        <div class="col-lg-4">
            <div class="module-section h-100">
                <h5 class="mb-4"><i class="fas fa-calendar-times me-2"></i> DÍAS INHÁBILES</h5>
                
                <div class="form-row">
                    <input type="hidden" id="diaId" value="">
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Fecha *</label>
                            <input type="date" class="form-control" id="diaFecha">
                            <small class="text-muted">Seleccione la fecha sin clases</small>
                        </div>
                    </div>

                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-12 mb-2">
                            <label class="form-label">
                                <i class="fas fa-lock me-1"></i>Contraseña de Administrador *
                            </label>
                            <input type="password" 
                                   class="form-control form-control-sm" 
                                   id="diaPassword" 
                                   placeholder="Ingrese su contraseña"
                                   autocomplete="new-password">
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-secondary btn-sm" id="btnGuardarDia">
                                    <i class="fas fa-save me-1"></i>Guardar Día
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCancelarDia" style="display:none;">
                                    <i class="fas fa-times me-1"></i>Cancelar
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFormDia()">
                                    <i class="fas fa-eraser me-1"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ SECCIÓN DE TABLAS LADO A LADO ============ -->
    <div class="row g-4">
        
        <!-- TABLA HORARIOS (Izquierda) -->
        <div class="col-lg-8">
            <div class="module-section h-100">
                <h5 class="mb-4"><i class="fas fa-search me-2"></i> CONSULTA DE HORARIOS</h5>

                <div class="row mb-3 g-2">
                    <div class="col-md-3">
                        <label class="form-label small">Hora:</label>
                        <select id="filtroHora" class="form-select form-select-sm">
                            <option value="">—Todas—</option>
                            <option value="1">1 (8:00 - 8:50)</option>
                            <option value="2">2 (8:50 - 9:40)</option>
                            <option value="3">3 (9:40 - 10:30)</option>
                            <option value="4">4 (11:00 - 11:50)</option>
                            <option value="5">5 (11:50 - 12:40)</option>
                            <option value="6">6 (12:40 - 13:30)</option>
                            <option value="7">7 (13:30 - 14:20)</option>
                            <option value="8">8 (14:20 - 15:00)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Día:</label>
                        <select id="filtroDia" class="form-select form-select-sm">
                            <option value="">—Todos—</option>
                            <option value="L">Lunes</option>
                            <option value="M">Martes</option>
                            <option value="X">Miércoles</option>
                            <option value="J">Jueves</option>
                            <option value="V">Viernes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Buscar:</label>
                        <input type="text" 
                               id="filtroBusquedaHorario" 
                               class="form-control form-control-sm" 
                               placeholder="Grupo, maestro, materia..."
                               autocomplete="off">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm w-100" type="button" id="btnFiltrarHorarios">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                        <span class="filter-badge" id="totalHorarios">
                            <i class="fas fa-hourglass-half"></i> 0 horario(s)
                        </span>
                        <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarFiltrosHorarios" style="display:none;">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 18%;">Hora</th>
                                <th style="width: 12%;">Día</th>
                                <th style="width: 8%;">¿Fija?</th>
                                <th style="width: 22%;">Grupo</th>
                                <th style="width: 18%;">Maestro</th>
                                <th style="width: 14%;">Materia</th>
                                <th style="width: 8%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaHorarios">
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No hay horarios registrados</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TABLA DÍAS INHÁBILES (Derecha) -->
        <div class="col-lg-4">
            <div class="module-section h-100">
                <h5 class="mb-4"><i class="fas fa-search me-2"></i> CONSULTA DE DÍAS INHÁBILES</h5>

                <div class="row mb-3 g-2">
                    <div class="col-8">
                        <label class="form-label small">Buscar:</label>
                        <input type="text" 
                               id="filtroBusquedaDia" 
                               class="form-control form-control-sm" 
                               placeholder="Buscar fecha..."
                               autocomplete="off">
                    </div>
                    <div class="col-4 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm w-100" type="button" id="btnFiltrarDias">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                        <span class="filter-badge" id="totalDias">
                            <i class="fas fa-calendar-times"></i> 0 día(s)
                        </span>
                        <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarFiltrosDias" style="display:none;">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 60%;">Fecha</th>
                                <th style="width: 40%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDias">
                            <tr>
                                <td colspan="2" class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No hay días inhábiles</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="container mt-5">
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Acceso denegado. Solo los administradores pueden acceder a esta sección.
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// ===== FUNCIONES GLOBALES =====
function formatearFecha(fecha) {
    if (!fecha) return '—';
    
    if (fecha.includes('T')) {
        const date = new Date(fecha);
        if (!isNaN(date.getTime())) {
            const dia = String(date.getUTCDate()).padStart(2, '0');
            const mes = String(date.getUTCMonth() + 1).padStart(2, '0');
            const anio = date.getUTCFullYear();
            return `${dia}/${mes}/${anio}`;
        }
    }
    
    // Si viene en formato YYYY-MM-DD
    if (fecha.includes('-')) {
        const partes = fecha.split('-');
        if (partes.length === 3) {
            return `${partes[2]}/${partes[1]}/${partes[0]}`;
        }
    }
    
    return fecha;
}

function limpiarFormHorario() {
    document.getElementById('horarioId').value = '';
    document.getElementById('horarioHora').value = '';
    document.getElementById('horarioDia').value = '';
    document.getElementById('horarioHoraFija').checked = false;
    document.getElementById('buscarGrupo').value = '';
    document.getElementById('horarioGrupoId').value = '';
    document.getElementById('buscarMaestro').value = '';
    document.getElementById('horarioMaestroId').value = '';
    document.getElementById('buscarMateria').value = '';
    document.getElementById('horarioMateriaId').value = '';
    document.getElementById('horarioPassword').value = '';
    document.getElementById('grupoSeleccionado').style.display = 'none';
    document.getElementById('maestroSeleccionado').style.display = 'none';
    document.getElementById('materiaSeleccionada').style.display = 'none';
    document.getElementById('btnCancelarHorario').style.display = 'none';
    document.getElementById('btnGuardarHorario').innerHTML = '<i class="fas fa-save me-1"></i>Guardar Horario';
}

function limpiarFormDia() {
    document.getElementById('diaId').value = '';
    document.getElementById('diaFecha').value = '';
    document.getElementById('diaPassword').value = '';
    document.getElementById('btnCancelarDia').style.display = 'none';
    document.getElementById('btnGuardarDia').innerHTML = '<i class="fas fa-save me-1"></i>Guardar Día';
}

document.addEventListener('DOMContentLoaded', function() {
    @if($esAdmin)
    
    // ===== ELEMENTOS =====
    const horarioId = document.getElementById('horarioId');
    const horarioHora = document.getElementById('horarioHora');
    const horarioDia = document.getElementById('horarioDia');
    const horarioHoraFija = document.getElementById('horarioHoraFija');
    const buscarGrupo = document.getElementById('buscarGrupo');
    const horarioGrupoId = document.getElementById('horarioGrupoId');
    const grupoResults = document.getElementById('grupoResults');
    const grupoSeleccionado = document.getElementById('grupoSeleccionado');
    const grupoNombreDisplay = document.getElementById('grupoNombreDisplay');
    const buscarMaestro = document.getElementById('buscarMaestro');
    const horarioMaestroId = document.getElementById('horarioMaestroId');
    const maestroResults = document.getElementById('maestroResults');
    const maestroSeleccionado = document.getElementById('maestroSeleccionado');
    const maestroNombreDisplay = document.getElementById('maestroNombreDisplay');
    const buscarMateria = document.getElementById('buscarMateria');
    const horarioMateriaId = document.getElementById('horarioMateriaId');
    const materiaResults = document.getElementById('materiaResults');
    const materiaSeleccionada = document.getElementById('materiaSeleccionada');
    const materiaNombreDisplay = document.getElementById('materiaNombreDisplay');
    const horarioPassword = document.getElementById('horarioPassword');
    const btnGuardarHorario = document.getElementById('btnGuardarHorario');
    const btnCancelarHorario = document.getElementById('btnCancelarHorario');

    const diaId = document.getElementById('diaId');
    const diaFecha = document.getElementById('diaFecha');
    const diaPassword = document.getElementById('diaPassword');
    const btnGuardarDia = document.getElementById('btnGuardarDia');
    const btnCancelarDia = document.getElementById('btnCancelarDia');

    // ===== FUNCIONES DE AUTOCOMPLETE =====
    function setupAutocomplete(input, resultsDiv, onSelect, url) {
        let timeoutId = null;
        input.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(timeoutId);
            
            if (query.length < 1) {
                resultsDiv.style.display = 'none';
                return;
            }
            
            timeoutId = setTimeout(() => {
                fetch(`${url}?q=${encodeURIComponent(query)}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.length > 0) {
                        resultsDiv.innerHTML = data.map(item => 
                            `<div class="search-result-item" data-id="${item.id}" data-nombre="${item.nombre}">${item.nombre}</div>`
                        ).join('');
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.innerHTML = '<div class="search-result-item text-muted">No se encontraron resultados</div>';
                        resultsDiv.style.display = 'block';
                    }
                })
                .catch(e => console.error('Error autocomplete:', e));
            }, 250);
        });

        resultsDiv.addEventListener('click', function(e) {
            const item = e.target.closest('.search-result-item');
            if (item && item.dataset.id) {
                onSelect(item.dataset.id, item.dataset.nombre);
                resultsDiv.style.display = 'none';
            }
        });
    }

    setupAutocomplete(buscarGrupo, grupoResults, (id, nombre) => {
        horarioGrupoId.value = id;
        buscarGrupo.value = nombre;
        grupoNombreDisplay.textContent = nombre;
        grupoSeleccionado.style.display = 'block';
    }, '{{ route("admin.horarios.buscar.grupos") }}');

    setupAutocomplete(buscarMaestro, maestroResults, (id, nombre) => {
        horarioMaestroId.value = id;
        buscarMaestro.value = nombre;
        maestroNombreDisplay.textContent = nombre;
        maestroSeleccionado.style.display = 'block';
    }, '{{ route("admin.horarios.buscar.maestros") }}');

    setupAutocomplete(buscarMateria, materiaResults, (id, nombre) => {
        horarioMateriaId.value = id;
        buscarMateria.value = nombre;
        materiaNombreDisplay.textContent = nombre;
        materiaSeleccionada.style.display = 'block';
    }, '{{ route("admin.horarios.buscar.materias") }}');

    // ===== LISTAR HORARIOS =====
    function listarHorarios() {
        const params = new URLSearchParams({
            hora: document.getElementById('filtroHora').value || '',
            dia: document.getElementById('filtroDia').value || '',
            busqueda: document.getElementById('filtroBusquedaHorario').value || ''
        });

        fetch(`{{ route("admin.horarios.listar") }}?${params}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderTablaHorarios(data.horarios);
                document.getElementById('totalHorarios').innerHTML = `<i class="fas fa-hourglass-half"></i> ${data.total} horario(s)`;
                const hayFiltros = document.getElementById('filtroHora').value || 
                                   document.getElementById('filtroDia').value || 
                                   document.getElementById('filtroBusquedaHorario').value;
                document.getElementById('btnLimpiarFiltrosHorarios').style.display = hayFiltros ? 'inline-block' : 'none';
            }
        })
        .catch(e => console.error('Error:', e));
    }

    function renderTablaHorarios(horarios) {
        const tabla = document.getElementById('tablaHorarios');
        if (horarios.length === 0) {
            tabla.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-hourglass-half fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No se encontraron horarios</p>
                    </td>
                </tr>`;
            return;
        }

        let html = '';
        horarios.forEach(h => {
            html += `<tr data-id="${h.id}">
                <td><span class="badge bg-secondary">${h.hora_legible}</span></td>
                <td><span class="badge bg-primary">${h.dia_legible}</span></td>
                <td>${h.hora_fija ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-muted"></i>'}</td>
                <td><small>${h.grupo_nombre || '—'}</small></td>
                <td><small>${h.maestro_nombre || '—'}</small></td>
                <td><small>${h.materia_nombre || '—'}</small></td>
                <td class="action-buttons">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary editar-horario-btn" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger eliminar-horario-btn" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });
        tabla.innerHTML = html;

        document.querySelectorAll('.editar-horario-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.closest('tr').dataset.id;
                cargarHorario(id);
            });
        });
        document.querySelectorAll('.eliminar-horario-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.closest('tr').dataset.id;
                eliminarHorario(id);
            });
        });
    }

    function cargarHorario(id) {
        fetch(`{{ url('admin/horarios') }}/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const h = data.horario;
                horarioId.value = h.id;
                horarioHora.value = h.hora;
                horarioDia.value = h.dia;
                horarioHoraFija.checked = h.hora_fija;
                
                horarioGrupoId.value = h.id_grupo;
                buscarGrupo.value = h.grupo_nombre || '';
                grupoNombreDisplay.textContent = h.grupo_nombre || '';
                grupoSeleccionado.style.display = 'block';
                
                horarioMaestroId.value = h.id_maestro;
                buscarMaestro.value = h.maestro_nombre || '';
                maestroNombreDisplay.textContent = h.maestro_nombre || '';
                maestroSeleccionado.style.display = 'block';
                
                horarioMateriaId.value = h.id_materia;
                buscarMateria.value = h.materia_nombre || '';
                materiaNombreDisplay.textContent = h.materia_nombre || '';
                materiaSeleccionada.style.display = 'block';
                
                btnCancelarHorario.style.display = 'inline-block';
                btnGuardarHorario.innerHTML = '<i class="fas fa-edit me-1"></i>Actualizar Horario';
                document.getElementById('horarioHora').scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error al cargar horario.'); });
    }

    btnGuardarHorario.addEventListener('click', function() {
        if (!horarioPassword.value) {
            alert('⚠️ Debe ingresar su contraseña de administrador.');
            horarioPassword.focus();
            return;
        }
        if (!horarioHora.value || !horarioDia.value || !horarioGrupoId.value || !horarioMaestroId.value || !horarioMateriaId.value) {
            alert('⚠️ Debe completar todos los campos obligatorios.');
            return;
        }

        const data = {
            current_password: horarioPassword.value,
            id: horarioId.value || null,
            hora: horarioHora.value,
            dia: horarioDia.value,
            hora_fija: horarioHoraFija.checked ? 1 : 0,
            id_grupo: horarioGrupoId.value,
            id_maestro: horarioMaestroId.value,
            id_materia: horarioMateriaId.value
        };

        btnGuardarHorario.disabled = true;
        const original = btnGuardarHorario.innerHTML;
        btnGuardarHorario.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';

        fetch('{{ route("admin.horarios.guardar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                limpiarFormHorario();
                listarHorarios();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); })
        .finally(() => {
            btnGuardarHorario.disabled = false;
            btnGuardarHorario.innerHTML = original;
        });
    });

    function eliminarHorario(id) {
        if (!horarioPassword.value) {
            alert('⚠️ Debe ingresar su contraseña de administrador.');
            horarioPassword.focus();
            return;
        }
        if (!confirm('¿Está seguro de eliminar este horario?')) return;

        fetch(`{{ url('admin/horarios') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ current_password: horarioPassword.value })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                horarioPassword.value = '';
                listarHorarios();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); });
    }

    // ===== DÍAS INHÁBILES =====
    function listarDias() {
        const params = new URLSearchParams({
            busqueda: document.getElementById('filtroBusquedaDia').value || ''
        });

        fetch(`{{ route("admin.horarios.dias.listar") }}?${params}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderTablaDias(data.dias);
                document.getElementById('totalDias').innerHTML = `<i class="fas fa-calendar-times"></i> ${data.total} día(s)`;
                document.getElementById('btnLimpiarFiltrosDias').style.display = 
                    document.getElementById('filtroBusquedaDia').value ? 'inline-block' : 'none';
            }
        })
        .catch(e => console.error('Error:', e));
    }

    function renderTablaDias(dias) {
        const tabla = document.getElementById('tablaDias');
        if (dias.length === 0) {
            tabla.innerHTML = `
                <tr>
                    <td colspan="2" class="text-center py-4">
                        <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No hay días inhábiles</p>
                    </td>
                </tr>`;
            return;
        }

        let html = '';
        dias.forEach(d => {
            html += `<tr data-id="${d.id}">
                <td>${formatearFecha(d.fecha)}</td>
                <td class="action-buttons">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary editar-dia-btn" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger eliminar-dia-btn" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });
        tabla.innerHTML = html;

        document.querySelectorAll('.editar-dia-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.closest('tr').dataset.id;
                cargarDia(id);
            });
        });
        document.querySelectorAll('.eliminar-dia-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.closest('tr').dataset.id;
                eliminarDia(id);
            });
        });
    }

    function cargarDia(id) {
        fetch(`{{ url('admin/horarios/dias') }}/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            console.log('Datos recibidos del servidor:', data); // ← debug temporal
            
            if (data.success) {
                const d = data.dia;
                diaId.value = d.id;
                
                // Asegurar formato YYYY-MM-DD para el input type="date"
                let fechaFormateada = d.fecha;
                if (fechaFormateada && fechaFormateada.includes('T')) {
                    // Si viene en formato ISO, extraer solo la parte de la fecha
                    fechaFormateada = fechaFormateada.split('T')[0];
                }
                diaFecha.value = fechaFormateada || '';
                
                console.log('Fecha asignada al input:', diaFecha.value); // ← debug temporal
                
                btnCancelarDia.style.display = 'inline-block';
                btnGuardarDia.innerHTML = '<i class="fas fa-edit me-1"></i>Actualizar Día';
                document.getElementById('diaFecha').scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error al cargar día.'); });
    }

    btnGuardarDia.addEventListener('click', function() {
        if (!diaPassword.value) {
            alert('⚠️ Debe ingresar su contraseña de administrador.');
            diaPassword.focus();
            return;
        }
        if (!diaFecha.value) {
            alert('⚠️ Debe seleccionar una fecha.');
            diaFecha.focus();
            return;
        }

        const data = {
            current_password: diaPassword.value,
            id: diaId.value || null,
            fecha: diaFecha.value
        };

        btnGuardarDia.disabled = true;
        const original = btnGuardarDia.innerHTML;
        btnGuardarDia.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';

        fetch('{{ route("admin.horarios.dias.guardar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                limpiarFormDia();
                listarDias();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); })
        .finally(() => {
            btnGuardarDia.disabled = false;
            btnGuardarDia.innerHTML = original;
        });
    });

    function eliminarDia(id) {
        if (!diaPassword.value) {
            alert('⚠️ Debe ingresar su contraseña de administrador.');
            diaPassword.focus();
            return;
        }
        if (!confirm('¿Está seguro de eliminar este día inhábil?')) return;

        fetch(`{{ url('admin/horarios/dias') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ current_password: diaPassword.value })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                diaPassword.value = '';
                listarDias();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); });
    }

    // ===== BOTONES CANCELAR =====
    btnCancelarHorario.addEventListener('click', limpiarFormHorario);
    btnCancelarDia.addEventListener('click', limpiarFormDia);

    // ===== FILTROS =====
    document.getElementById('btnFiltrarHorarios').addEventListener('click', listarHorarios);
    document.getElementById('btnFiltrarDias').addEventListener('click', listarDias);
    
    document.getElementById('btnLimpiarFiltrosHorarios').addEventListener('click', function() {
        document.getElementById('filtroHora').value = '';
        document.getElementById('filtroDia').value = '';
        document.getElementById('filtroBusquedaHorario').value = '';
        listarHorarios();
    });
    
    document.getElementById('btnLimpiarFiltrosDias').addEventListener('click', function() {
        document.getElementById('filtroBusquedaDia').value = '';
        listarDias();
    });

    // Ocultar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!buscarGrupo.contains(e.target) && !grupoResults.contains(e.target)) {
            grupoResults.style.display = 'none';
        }
        if (!buscarMaestro.contains(e.target) && !maestroResults.contains(e.target)) {
            maestroResults.style.display = 'none';
        }
        if (!buscarMateria.contains(e.target) && !materiaResults.contains(e.target)) {
            materiaResults.style.display = 'none';
        }
    });

    // ===== INICIALIZAR =====
    listarHorarios();
    listarDias();
    
    @endif
});
</script>
@endpush