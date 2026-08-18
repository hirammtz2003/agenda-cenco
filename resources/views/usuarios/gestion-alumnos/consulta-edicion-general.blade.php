@extends('layouts.app')

@section('title', 'Consulta y Edición General de Alumnos - SGGDI')

@php
    use App\Helpers\PrivilegiosHelper;
    $usuarioActual = Auth::user();
    $puedeEditar = $puedeEditar ?? false;
    $puedeConsultar = $puedeConsultar ?? false;
    
    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Gestión de Información de Alumnos',
            'url' => route('alumnos.index'),
            'icon' => 'fa-folder-open'
        ],
        [
            'name' => 'Consulta y Edición General de Alumnos',
            'url' => null,
            'icon' => 'fa-users-between-lines'
        ]
    ];
@endphp

@push('styles')
<style>
    .module-section {
        background-color: #f8f9fa;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .table-container {
        max-height: 500px;
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
    }
    .table-container tbody tr:hover {
        background-color: rgba(0,0,0,0.05);
    }
    .alumno-row.editing {
        background-color: #fff3cd !important;
    }
    .editable-field {
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    .editable-field:hover {
        background-color: #e9ecef;
    }
    .editing .editable-field {
        display: none;
    }
    .edit-input {
        display: none;
        width: 100%;
        padding: 5px;
        border: 2px solid #a01508;
        border-radius: 4px;
    }
    .editing .edit-input {
        display: block;
    }
    .filter-badge {
        background-color: #e9ecef;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-block;
    }
    .action-buttons {
        white-space: nowrap;
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
    }
    .search-result-item:hover {
        background-color: #f0f0f0;
    }
    .search-result-item.highlighted {
        background-color: #ffc107 !important;
        color: #212529;
    }
    .grupo-seleccionado {
        font-size: 0.85rem;
        color: #28a745;
    }

    /* Estilos para la columna de grupo */
    .grupo-display {
        padding: 5px;
        border-radius: 4px;
    }

    .alumno-row.editing .grupo-display {
        display: none;
    }

    .alumno-row.editing .grupo-edit-mode {
        display: block !important;
    }

    .grupo-edit-mode {
        min-width: 250px;
    }

    .grupo-edit-mode .search-results-dropdown {
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
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    @include('partials.breadcrumb')
    
    <!-- Título del menú -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="border-bottom pb-3">
                <i class="fas fa-users-between-lines me-2 text-warning"></i>
                CONSULTA Y EDICIÓN GENERAL DE ALUMNOS
            </h2>
        </div>
    </div>
    
    @if(!$puedeConsultar)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> No tienes permisos para consultar alumnos.
        </div>
    @else
    <!-- Filtros y búsqueda -->
    <div class="module-section">
        <form method="GET" action="{{ route('alumnos.consulta-general') }}" id="filterForm">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-search"></i> Buscar:</label>
                    <div class="input-group">
                        <input type="text" name="busqueda" class="form-control" 
                            placeholder="Núm. control, nombre, apellidos..." 
                            value="{{ request('busqueda') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request()->anyFilled(['busqueda', 'semestre', 'grupo_letra', 'carrera', 'orden', 'mostrar_inactivos']))
                            <a href="{{ route('alumnos.consulta-general') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        @endif
                    </div>
                </div>
                
                @if($permisoGeneral || $permisoGrado)
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-filter"></i> Semestre:</label>
                    <select name="semestre" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">—Todos—</option>
                        <option value="1°" {{ request('semestre') == '1°' ? 'selected' : '' }}>1°</option>
                        <option value="2°" {{ request('semestre') == '2°' ? 'selected' : '' }}>2°</option>
                        <option value="3°" {{ request('semestre') == '3°' ? 'selected' : '' }}>3°</option>
                        <option value="4°" {{ request('semestre') == '4°' ? 'selected' : '' }}>4°</option>
                        <option value="5°" {{ request('semestre') == '5°' ? 'selected' : '' }}>5°</option>
                        <option value="6°" {{ request('semestre') == '6°' ? 'selected' : '' }}>6°</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-filter"></i> Grupo:</label>
                    <select name="grupo_letra" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">—Todos—</option>
                        <option value="A" {{ request('grupo_letra') == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ request('grupo_letra') == 'B' ? 'selected' : '' }}>B</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-filter"></i> Carrera:</label>
                    <select name="carrera" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">—Todas—</option>
                        <option value="Soporte y Mantenimiento de Equipo de Cómputo" {{ request('carrera') == 'Soporte y Mantenimiento de Equipo de Cómputo' ? 'selected' : '' }}>Soporte y Mantenimiento de Equipo de Cómputo</option>
                        <option value="Soporte y Gestión de Tecnologías Informáticas" {{ request('carrera') == 'Soporte y Gestión de Tecnologías Informáticas' ? 'selected' : '' }}>Soporte y Gestión de Tecnologías Informáticas</option>
                        <option value="Enfermería General" {{ request('carrera') == 'Enfermería General' ? 'selected' : '' }}>Enfermería General</option>
                        <option value="Ventas" {{ request('carrera') == 'Ventas' ? 'selected' : '' }}>Ventas</option>
                        <option value="Diseño Gráfico Digital" {{ request('carrera') == 'Diseño Gráfico Digital' ? 'selected' : '' }}>Diseño Gráfico Digital</option>
                    </select>
                </div>
                @endif
                
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-sort"></i> Ordenar por:</label>
                    <select name="orden" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="id_asc" {{ request('orden') == 'id_asc' ? 'selected' : '' }}>Primeros registros</option>
                        <option value="nombre_asc" {{ request('orden') == 'nombre_asc' ? 'selected' : '' }}>Nombre(s)</option>
                        <option value="apellido_asc" {{ request('orden') == 'apellido_asc' ? 'selected' : '' }}>Primer Apellido</option>
                        <option value="num_control_asc" {{ request('orden') == 'num_control_asc' ? 'selected' : '' }}>Número de Control</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    @if($permisoGeneral || $permisoGrado)
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="mostrar_inactivos" 
                            name="mostrar_inactivos" value="1" 
                            {{ request('mostrar_inactivos') ? 'checked' : '' }}
                            onchange="document.getElementById('filterForm').submit()">
                        <label class="form-check-label" for="mostrar_inactivos">
                            <i class="fas fa-eye-slash"></i> Mostrar alumnos inactivos
                        </label>
                    </div>
                    @endif
                </div>
                <div class="col-md-4">
                    @if($permisoGeneral || $permisoGrado)
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="mostrar_sin_grupo" 
                            name="mostrar_sin_grupo" value="1" 
                            {{ request('mostrar_sin_grupo') ? 'checked' : '' }}
                            onchange="document.getElementById('filterForm').submit()">
                        <label class="form-check-label" for="mostrar_sin_grupo">
                            <i class="fas fa-eye-slash"></i> Mostrar alumnos activos sin grupo
                        </label>
                    </div>
                    @endif
                </div>
            </div>
        </form>

        <!-- Resultados de la búsqueda -->
        <div class="row mb-3">
            <div class="col-12">
                <span class="filter-badge">
                    <i class="fas fa-users"></i> {{ $totalEncontrados }} alumno(s) encontrado(s)
                </span>
                @if(request('busqueda'))
                    <span class="filter-badge">
                        <i class="fas fa-search"></i> Buscando: "{{ request('busqueda') }}"
                    </span>
                @endif
                @if(request('semestre'))
                    <span class="filter-badge">
                        <i class="fas fa-graduation-cap"></i> Semestre: {{ request('semestre') }}
                    </span>
                @endif
                @if(request('grupo_letra'))
                    <span class="filter-badge">
                        <i class="fas fa-users"></i> Grupo: {{ request('grupo_letra') }}
                    </span>
                @endif
                @if(request('carrera'))
                    <span class="filter-badge">
                        <i class="fas fa-laptop-code"></i> Carrera: {{ request('carrera') }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Formulario principal de edición -->
        <form method="POST" action="{{ route('alumnos.actualizar-alumno-tabla') }}" id="bulkEditForm">
            @csrf
            <input type="hidden" name="current_password" id="current_password_hidden">

            <!-- Tabla de Alumnos con scroll -->
            <div class="table-container">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Núm. Control</th>
                            <th>Nombre(s)</th>
                            <th>Primer Apellido</th>
                            <th>Segundo Apellido</th>
                            <th>Semestre, Grupo y Carrera</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alumnos as $alumno)
                            <tr id="alumno-{{ $alumno->id }}" class="alumno-row" data-id="{{ $alumno->id }}">
                                <td>
                                    <span class="editable-field" data-field="num_control">{{ $alumno->num_control ?? '—' }}</span>
                                    <input type="text" class="edit-input" data-field="num_control" 
                                           value="{{ $alumno->num_control }}" maxlength="14" placeholder="Opcional">
                                </td>
                                <td>
                                    <span class="editable-field" data-field="nombre">{{ $alumno->nombre }}</span>
                                    <input type="text" class="edit-input" data-field="nombre" 
                                           value="{{ $alumno->nombre }}" maxlength="30">
                                </td>
                                <td>
                                    <span class="editable-field" data-field="apellido1">{{ $alumno->apellido1 }}</span>
                                    <input type="text" class="edit-input" data-field="apellido1" 
                                           value="{{ $alumno->apellido1 }}" maxlength="20">
                                </td>
                                <td>
                                    <span class="editable-field" data-field="apellido2">{{ $alumno->apellido2 ?? '—' }}</span>
                                    <input type="text" class="edit-input" data-field="apellido2" 
                                           value="{{ $alumno->apellido2 }}" maxlength="20" placeholder="Opcional">
                                </td>
                                <td class="position-relative">
                                    <!-- Modo visualización (normal) -->
                                    <div class="grupo-display" data-grupo-id="{{ $alumno->id_grupo }}">
                                        @if($alumno->grupo)
                                            {{ $alumno->grupo->semestre }} {{ $alumno->grupo->grupo }} - {{ $alumno->grupo->carrera }}
                                        @else
                                            <span class="text-muted">Sin grupo asignado</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Modo edición (se muestra dentro de la misma celda al editar) -->
                                    <div class="edit-input grupo-edit-mode" style="display: none;">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control buscar-grupo-input" 
                                                placeholder="Buscar grupo..." 
                                                data-alumno-id="{{ $alumno->id }}"
                                                autocomplete="off">
                                            <button type="button" class="btn btn-outline-secondary limpiar-grupo-btn" title="Limpiar grupo">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="search-results-dropdown grupo-results-{{ $alumno->id }}" style="display: none;"></div>
                                    </div>
                                    
                                    <!-- Campo oculto para almacenar el ID del grupo -->
                                    <input type="hidden" class="id_grupo_hidden" value="{{ $alumno->id_grupo }}">
                                    <!-- Campo para almacenar el texto original del grupo (para cancelar) -->
                                    <input type="hidden" class="grupo_texto_original" value="@if($alumno->grupo){{ $alumno->grupo->semestre }} {{ $alumno->grupo->grupo }} - {{ $alumno->grupo->carrera }}@endif">
                                    <input type="hidden" class="grupo_id_original" value="{{ $alumno->id_grupo }}">
                                </td>
                                <td>
                                    @if($alumno->estatus)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                @if($puedeEditar || $puedeConsultar)
                                <td class="action-buttons">
                                    <div class="btn-group" role="group">
                                        <!-- Botón Editar: solo si tiene permisos de GESTIÓN en algún nivel -->
                                        @if($puedeEditar)
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-row-btn" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success save-row-btn" 
                                                style="display:none;" title="Guardar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary cancel-row-btn" 
                                                style="display:none;" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                        
                                        <!-- Botón Consulta Avanzada: visible para todos con permiso de consulta -->
                                        <button type="button" class="btn btn-sm btn-outline-info consulta-avanzada-btn" 
                                                title="Consulta Avanzada" data-id="{{ $alumno->id }}">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                        
                                        <!-- Botón Cambiar Estatus: solo si tiene permisos de GESTIÓN GENERAL o DE GRADO -->
                                        @if(($puedeEditarGeneral || $puedeEditarGrado) && $puedeEditar)
                                        <button type="button" class="btn btn-sm btn-outline-danger cambiar-estatus-btn" 
                                                title="Cambiar Estatus" data-id="{{ $alumno->id }}"
                                                data-estatus="{{ $alumno->estatus ? 1 : 0 }}">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $puedeEditar ? 7 : 6 }}" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No se encontraron alumnos</h5>
                                    <p class="text-muted">Intenta con otros filtros de búsqueda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-end mt-4">
                {{ $alumnos->appends(request()->query())->links() }}
            </div>

            @if($puedeEditar && $alumnos->isNotEmpty())
                <!-- Panel de contraseña y guardado - SOLO para usuarios con GESTIÓN -->
                <div class="row mt-4 pt-4 border-top">
                    <div class="col-md-4">
                        <label for="current_password_global" class="form-label">
                            <i class="fas fa-lock me-2"></i>Contraseña de Usuario *
                        </label>
                        <input type="password" 
                            class="form-control" 
                            id="current_password_global" 
                            placeholder="Ingrese su contraseña para guardar cambios"
                            required>
                        <small class="text-muted">Requerida para confirmar cualquier modificación</small>
                    </div>
                    <div class="col-md-8 d-flex align-items-end justify-content-end">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning" id="guardarTodosBtn" disabled>
                                <i class="fas fa-save me-2"></i>Guardar Todos los Cambios
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="cancelarTodoBtn" disabled>
                                <i class="fas fa-times me-2"></i>Cancelar Todo
                            </button>
                            <a href="{{ route('alumnos.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </form>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CONSULTA AVANZADA
    document.querySelectorAll('.consulta-avanzada-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const alumnoId = this.dataset.id;
            const url = `{{ route("alumnos.consulta-individual") }}?consulta_avanzada=${alumnoId}`;
            window.open(url, '_blank');
        });
    });

    @if($puedeEditar)
    // Variables de estado
    let filasEnEdicion = new Set();
    const currentPasswordGlobal = document.getElementById('current_password_global');
    const guardarTodosBtn = document.getElementById('guardarTodosBtn');
    const cancelarTodoBtn = document.getElementById('cancelarTodoBtn');
    
    // Timeouts para búsqueda de grupos
    let grupoTimeouts = {};
    let resultadosGrupos = {};

    function actualizarBotonesGlobales() {
        const hayEdiciones = filasEnEdicion.size > 0;
        if (guardarTodosBtn) guardarTodosBtn.disabled = !hayEdiciones || !currentPasswordGlobal?.value;
        if (cancelarTodoBtn) cancelarTodoBtn.disabled = !hayEdiciones;
    }

    if (currentPasswordGlobal) {
        currentPasswordGlobal.addEventListener('input', actualizarBotonesGlobales);
    }

    // FUNCIONES PARA BÚSQUEDA DE GRUPOS
    function buscarGrupos(query, alumnoId) {
        const resultsDiv = document.querySelector(`.grupo-results-${alumnoId}`);
        if (query.length < 1) {
            if (resultsDiv) resultsDiv.style.display = 'none';
            return;
        }

        clearTimeout(grupoTimeouts[alumnoId]);
        grupoTimeouts[alumnoId] = setTimeout(() => {
            fetch('{{ route("alumnos.buscar-grupos-tabla") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ busqueda: query })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.grupos.length > 0) {
                    mostrarResultadosGrupo(data.grupos, alumnoId);
                } else {
                    const resultsDiv = document.querySelector(`.grupo-results-${alumnoId}`);
                    if (resultsDiv) resultsDiv.style.display = 'none';
                }
            });
        }, 300);
    }

    function mostrarResultadosGrupo(grupos, alumnoId) {
        const resultsDiv = document.querySelector(`.grupo-results-${alumnoId}`);
        if (!resultsDiv) return;
        
        resultsDiv.innerHTML = '';
        resultadosGrupos[alumnoId] = grupos;
        
        grupos.forEach((grupo, index) => {
            const div = document.createElement('div');
            div.className = 'search-result-item';
            div.dataset.index = index;
            div.innerHTML = `<strong>${grupo.nombre}</strong><br>
                            <small>Asesor: ${grupo.asesor} | Tutor: ${grupo.tutor}</small>`;
            div.addEventListener('click', () => seleccionarGrupo(grupo, alumnoId));
            resultsDiv.appendChild(div);
        });
        
        resultsDiv.style.display = 'block';
    }

    function seleccionarGrupo(grupo, alumnoId) {
        const row = document.getElementById(`alumno-${alumnoId}`);
        if (!row) return;
        
        const hiddenInput = row.querySelector('.id_grupo_hidden');
        const buscarInput = row.querySelector('.buscar-grupo-input');
        const resultsDiv = document.querySelector(`.grupo-results-${alumnoId}`);
        
        // Actualizar hidden con el ID del grupo seleccionado
        hiddenInput.value = grupo.id;
        
        // Mostrar el grupo seleccionado en el input
        if (buscarInput) buscarInput.value = grupo.nombre;
        
        // Ocultar resultados
        if (resultsDiv) resultsDiv.style.display = 'none';
    }

    function limpiarGrupo(alumnoId) {
        const row = document.getElementById(`alumno-${alumnoId}`);
        if (!row) return;
        
        const hiddenInput = row.querySelector('.id_grupo_hidden');
        const buscarInput = row.querySelector('.buscar-grupo-input');
        const resultsDiv = document.querySelector(`.grupo-results-${alumnoId}`);
        
        hiddenInput.value = '';
        if (buscarInput) buscarInput.value = '';
        if (resultsDiv) resultsDiv.style.display = 'none';
    }

    // FUNCIONES PARA RESTAURAR ESTADO ORIGINAL
    function restaurarGrupoOriginal(row) {
        const grupoIdOriginal = row.querySelector('.grupo_id_original')?.value || '';
        const grupoTextoOriginal = row.querySelector('.grupo_texto_original')?.value || '';
        const hiddenInput = row.querySelector('.id_grupo_hidden');
        const grupoDisplay = row.querySelector('.grupo-display');
        const buscarInput = row.querySelector('.buscar-grupo-input');
        const resultsDiv = row.querySelector('[class^="grupo-results-"]');
        
        // Restaurar hidden al valor original
        if (hiddenInput) hiddenInput.value = grupoIdOriginal;
        
        // Restaurar display visual
        if (grupoDisplay) {
            if (grupoTextoOriginal) {
                grupoDisplay.innerHTML = grupoTextoOriginal;
                grupoDisplay.dataset.grupoId = grupoIdOriginal;
            } else {
                grupoDisplay.innerHTML = '<span class="text-muted">Sin grupo asignado</span>';
                grupoDisplay.dataset.grupoId = '';
            }
        }
        
        // Limpiar input de búsqueda
        if (buscarInput) buscarInput.value = '';
        
        // Ocultar resultados
        if (resultsDiv) resultsDiv.style.display = 'none';
    }

    // EVENTO: EDITAR FILA
    document.querySelectorAll('.edit-row-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.alumno-row');
            const rowId = row.dataset.id;
            
            if (filasEnEdicion.has(rowId)) return;
            
            // GUARDAR ESTADO ORIGINAL antes de editar
            const hiddenInput = row.querySelector('.id_grupo_hidden');
            const grupoDisplay = row.querySelector('.grupo-display');
            const textoActual = grupoDisplay.innerText.trim();
            const grupoIdActual = hiddenInput.value;
            
            // Guardar originales en los campos ocultos
            const originalIdInput = row.querySelector('.grupo_id_original');
            const originalTextoInput = row.querySelector('.grupo_texto_original');
            if (originalIdInput) originalIdInput.value = grupoIdActual;
            if (originalTextoInput) originalTextoInput.value = (textoActual !== 'Sin grupo asignado') ? textoActual : '';
            
            row.classList.add('editing');
            filasEnEdicion.add(rowId);
            
            this.style.display = 'none';
            if (row.querySelector('.save-row-btn')) row.querySelector('.save-row-btn').style.display = 'inline-block';
            if (row.querySelector('.cancel-row-btn')) row.querySelector('.cancel-row-btn').style.display = 'inline-block';
            
            // Configurar input de búsqueda de grupos
            const grupoInput = row.querySelector('.buscar-grupo-input');
            if (grupoInput) {
                grupoInput.value = '';
                
                // Evento input para búsqueda
                const inputHandler = function() {
                    buscarGrupos(this.value, rowId);
                };
                grupoInput.removeEventListener('input', window[`inputHandler_${rowId}`]);
                grupoInput.addEventListener('input', inputHandler);
                window[`inputHandler_${rowId}`] = inputHandler;
                
                // Navegación con teclado
                const keydownHandler = function(e) {
                    const resultsDiv = document.querySelector(`.grupo-results-${rowId}`);
                    if (resultsDiv?.style.display !== 'block' || !resultadosGrupos[rowId]) return;
                    
                    const items = resultsDiv.querySelectorAll('.search-result-item');
                    if (items.length === 0) return;
                    
                    // Obtener índice actual del dataset o usar -1
                    let selectedIndex = parseInt(resultsDiv.dataset.selectedIndex);
                    if (isNaN(selectedIndex)) selectedIndex = -1;
                    
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        selectedIndex = (selectedIndex + 1) % items.length;
                        resultsDiv.dataset.selectedIndex = selectedIndex;
                        updateHighlight(items, selectedIndex);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                        resultsDiv.dataset.selectedIndex = selectedIndex;
                        updateHighlight(items, selectedIndex);
                    } else if (e.key === 'Enter' && selectedIndex >= 0) {
                        e.preventDefault();
                        const grupoSeleccionado = resultadosGrupos[rowId][selectedIndex];
                        if (grupoSeleccionado) {
                            seleccionarGrupo(grupoSeleccionado, rowId);
                            // Limpiar índice después de seleccionar
                            delete resultsDiv.dataset.selectedIndex;
                        }
                    }
                    
                    // Asegurar que el evento no se propague
                    return false;
                };
                grupoInput.removeEventListener('keydown', window[`keydownHandler_${rowId}`]);
                grupoInput.addEventListener('keydown', keydownHandler);
                window[`keydownHandler_${rowId}`] = keydownHandler;
            }
            
            // Botón limpiar grupo
            const limpiarBtn = row.querySelector('.limpiar-grupo-btn');
            if (limpiarBtn) {
                const limpiarHandler = function() {
                    limpiarGrupo(rowId);
                };
                limpiarBtn.removeEventListener('click', window[`limpiarHandler_${rowId}`]);
                limpiarBtn.addEventListener('click', limpiarHandler);
                window[`limpiarHandler_${rowId}`] = limpiarHandler;
            }
            
            actualizarBotonesGlobales();
        });
    });

    function updateHighlight(items, index) {
        items.forEach((item, i) => {
            if (i === index) {
                item.classList.add('highlighted');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('highlighted');
            }
        });
    }

    // EVENTO: CANCELAR EDICIÓN DE FILA
    document.querySelectorAll('.cancel-row-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.alumno-row');
            const rowId = row.dataset.id;
            
            // Restaurar valores originales de los campos de texto
            row.querySelectorAll('.edit-input[data-field]').forEach(input => {
                const field = input.dataset.field;
                const originalValue = input.defaultValue;
                input.value = originalValue;
                const span = row.querySelector(`.editable-field[data-field="${field}"]`);
                if (span) {
                    span.textContent = originalValue || (field === 'apellido2' ? '—' : originalValue || '');
                }
            });
            
            // Restaurar grupo al estado original
            restaurarGrupoOriginal(row);
            
            // Salir del modo edición
            row.classList.remove('editing');
            filasEnEdicion.delete(rowId);
            
            if (row.querySelector('.edit-row-btn')) row.querySelector('.edit-row-btn').style.display = 'inline-block';
            if (row.querySelector('.save-row-btn')) row.querySelector('.save-row-btn').style.display = 'none';
            if (row.querySelector('.cancel-row-btn')) row.querySelector('.cancel-row-btn').style.display = 'none';
            
            actualizarBotonesGlobales();
        });
    });

    // EVENTO: GUARDAR FILA INDIVIDUAL
    document.querySelectorAll('.save-row-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!currentPasswordGlobal?.value) {
                alert('Debes ingresar tu contraseña para guardar cambios.');
                currentPasswordGlobal?.focus();
                return;
            }
            
            const row = this.closest('.alumno-row');
            const rowId = row.dataset.id;
            
            const alumnoData = {
                id: rowId,
                num_control: row.querySelector('input[data-field="num_control"]')?.value || null,
                nombre: row.querySelector('input[data-field="nombre"]')?.value,
                apellido1: row.querySelector('input[data-field="apellido1"]')?.value,
                apellido2: row.querySelector('input[data-field="apellido2"]')?.value || null,
                id_grupo: row.querySelector('.id_grupo_hidden')?.value || null
            };
            
            fetch('{{ route("alumnos.actualizar-alumno-tabla") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_password: currentPasswordGlobal.value,
                    alumnos: [alumnoData]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar valores por defecto
                    row.querySelectorAll('.edit-input[data-field]').forEach(input => {
                        input.defaultValue = input.value;
                        const field = input.dataset.field;
                        const span = row.querySelector(`.editable-field[data-field="${field}"]`);
                        if (span) span.textContent = input.value || (field === 'apellido2' ? '—' : input.value || '');
                    });
                    
                    // Actualizar grupo en display
                    const hiddenInput = row.querySelector('.id_grupo_hidden');
                    const grupoId = hiddenInput.value;
                    const grupoDisplay = row.querySelector('.grupo-display');
                    
                    if (grupoId) {
                        fetch(`{{ url('admin/grupos') }}/${grupoId}`)
                        .then(res => res.json())
                        .then(grupoData => {
                            if (grupoData.success) {
                                const textoGrupo = `${grupoData.grupo.semestre} ${grupoData.grupo.grupo} - ${grupoData.grupo.carrera}`;
                                grupoDisplay.innerHTML = textoGrupo;
                                grupoDisplay.dataset.grupoId = grupoId;
                                // Actualizar campos originales
                                if (row.querySelector('.grupo_id_original')) row.querySelector('.grupo_id_original').value = grupoId;
                                if (row.querySelector('.grupo_texto_original')) row.querySelector('.grupo_texto_original').value = textoGrupo;
                            }
                        });
                    } else {
                        grupoDisplay.innerHTML = '<span class="text-muted">Sin grupo asignado</span>';
                        grupoDisplay.dataset.grupoId = '';
                        if (row.querySelector('.grupo_id_original')) row.querySelector('.grupo_id_original').value = '';
                        if (row.querySelector('.grupo_texto_original')) row.querySelector('.grupo_texto_original').value = '';
                    }
                    
                    // Actualizar default del hidden
                    hiddenInput.defaultValue = hiddenInput.value;
                    
                    row.classList.remove('editing');
                    filasEnEdicion.delete(rowId);
                    
                    if (row.querySelector('.edit-row-btn')) row.querySelector('.edit-row-btn').style.display = 'inline-block';
                    if (row.querySelector('.save-row-btn')) row.querySelector('.save-row-btn').style.display = 'none';
                    if (row.querySelector('.cancel-row-btn')) row.querySelector('.cancel-row-btn').style.display = 'none';
                    
                    if (currentPasswordGlobal) currentPasswordGlobal.value = '';
                    actualizarBotonesGlobales();
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al guardar.');
            });
        });
    });

    // EVENTO: GUARDAR TODOS LOS CAMBIOS
    if (guardarTodosBtn) {
        guardarTodosBtn.addEventListener('click', function() {
            if (!currentPasswordGlobal?.value) {
                alert('Debes ingresar tu contraseña para guardar cambios.');
                currentPasswordGlobal?.focus();
                return;
            }
            
            const alumnosData = [];
            filasEnEdicion.forEach(rowId => {
                const row = document.getElementById(`alumno-${rowId}`);
                if (row) {
                    alumnosData.push({
                        id: rowId,
                        num_control: row.querySelector('input[data-field="num_control"]')?.value || null,
                        nombre: row.querySelector('input[data-field="nombre"]')?.value,
                        apellido1: row.querySelector('input[data-field="apellido1"]')?.value,
                        apellido2: row.querySelector('input[data-field="apellido2"]')?.value || null,
                        id_grupo: row.querySelector('.id_grupo_hidden')?.value || null
                    });
                }
            });
            
            if (alumnosData.length === 0) {
                alert('No hay cambios para guardar.');
                return;
            }
            
            if (!confirm('¿Estás seguro de guardar los cambios en ' + alumnosData.length + ' alumno(s)?')) return;
            
            fetch('{{ route("alumnos.actualizar-alumno-tabla") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_password: currentPasswordGlobal.value,
                    alumnos: alumnosData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recargar la página para mostrar los cambios actualizados
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al guardar.');
            });
        });
    }

    // EVENTO: CANCELAR TODAS LAS EDICIONES
    if (cancelarTodoBtn) {
        cancelarTodoBtn.addEventListener('click', function() {
            if (filasEnEdicion.size === 0) return;
            if (!confirm('¿Cancelar todas las ediciones? Se perderán los cambios no guardados.')) return;
            
            filasEnEdicion.forEach(rowId => {
                const row = document.getElementById(`alumno-${rowId}`);
                if (row) {
                    // Restaurar valores originales de texto
                    row.querySelectorAll('.edit-input[data-field]').forEach(input => {
                        input.value = input.defaultValue;
                        const field = input.dataset.field;
                        const span = row.querySelector(`.editable-field[data-field="${field}"]`);
                        if (span) span.textContent = input.defaultValue || (field === 'apellido2' ? '—' : input.defaultValue || '');
                    });
                    
                    // Restaurar grupo original
                    restaurarGrupoOriginal(row);
                    
                    row.classList.remove('editing');
                    if (row.querySelector('.edit-row-btn')) row.querySelector('.edit-row-btn').style.display = 'inline-block';
                    if (row.querySelector('.save-row-btn')) row.querySelector('.save-row-btn').style.display = 'none';
                    if (row.querySelector('.cancel-row-btn')) row.querySelector('.cancel-row-btn').style.display = 'none';
                }
            });
            
            filasEnEdicion.clear();
            actualizarBotonesGlobales();
        });
    }

    // EVENTO: CAMBIAR ESTATUS
    document.querySelectorAll('.cambiar-estatus-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.alumno-row');
            const rowId = row.dataset.id;
            const estatusActual = parseInt(this.dataset.estatus);
            const nuevoEstatus = estatusActual === 1 ? 0 : 1;
            const accion = nuevoEstatus === 1 ? 'activar' : 'desactivar';
            
            let mensajeConfirmacion = `¿Estás seguro de ${accion} a este alumno?`;
            if (nuevoEstatus === 0) {
                mensajeConfirmacion += ' Al desactivarlo, se eliminará su asignación al grupo y todas sus becas.';
            }
            
            if (!confirm(mensajeConfirmacion)) return;

            if (!currentPasswordGlobal?.value) {
                alert('Debes ingresar tu contraseña para cambiar el estatus.');
                currentPasswordGlobal?.focus();
                return;
            }
            
            fetch('{{ route("alumnos.cambiar-estatus-alumno") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_password: currentPasswordGlobal.value,
                    id: rowId,
                    estatus: nuevoEstatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const estatusBadge = row.querySelector('td:nth-child(6)');
                    if (nuevoEstatus === 1) {
                        estatusBadge.innerHTML = '<span class="badge bg-success">Activo</span>';
                        this.dataset.estatus = '1';
                    } else {
                        estatusBadge.innerHTML = '<span class="badge bg-danger">Inactivo</span>';
                        this.dataset.estatus = '0';
                        
                        // Limpiar grupo visualmente
                        const hiddenInput = row.querySelector('.id_grupo_hidden');
                        const grupoDisplay = row.querySelector('.grupo-display');
                        if (hiddenInput) hiddenInput.value = '';
                        if (grupoDisplay) {
                            grupoDisplay.innerHTML = '<span class="text-muted">Sin grupo asignado</span>';
                            grupoDisplay.dataset.grupoId = '';
                        }
                        
                        // Actualizar campos originales
                        if (row.querySelector('.grupo_id_original')) row.querySelector('.grupo_id_original').value = '';
                        if (row.querySelector('.grupo_texto_original')) row.querySelector('.grupo_texto_original').value = '';
                    }
                    if (currentPasswordGlobal) currentPasswordGlobal.value = '';
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al cambiar estatus.');
            });
        });
    });

    // Ocultar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.grupo-edit-mode')) {
            document.querySelectorAll('[class^="grupo-results-"]').forEach(el => {
                if (el) el.style.display = 'none';
            });
        }
    });
    @endif
});
</script>
@endpush