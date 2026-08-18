@extends('layouts.app')

@section('title', 'Configuración de Grupos - SGGDI')

@php
    use App\Helpers\PrivilegiosHelper;
    $usuarioActual = Auth::user();
    $puedeEditar = PrivilegiosHelper::puedeEditar($usuarioActual);
    $puedeConsultar = PrivilegiosHelper::consultaGeneral($usuarioActual);

    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Configuración de Grupos',
            'url' => null,
            'icon' => 'fa-users'
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
        max-height: 400px;
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
    .filter-badge {
        background-color: #e9ecef;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-block;
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
    .action-buttons {
        white-space: nowrap;
    }
    .form-row {
        background-color: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 1rem;
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
                <i class="fas fa-users me-2 text-success"></i>
                CONFIGURACIÓN DE GRUPOS
            </h2>
        </div>
    </div>
    
    @if(!$puedeEditar && !$puedeConsultar)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> No tienes permisos para acceder a este módulo.
        </div>
    @else

    @if($puedeEditar)
    <!-- Formulario de registro/edición -->
    <div class="module-section">
        <h5 class="mb-4"><i class="fa-solid fa-people-group"></i> REGISTRO Y EDICIÓN DE GRUPOS</h5>
        
        <div class="form-row" id="formGrupo">
            <input type="hidden" id="grupoId" value="">
            
            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="form-label">Semestre *</label>
                    <select id="semestre" class="form-select" required>
                        <option value="">—Seleccione—</option>
                        <option value="1°">1°</option>
                        <option value="2°">2°</option>
                        <option value="3°">3°</option>
                        <option value="4°">4°</option>
                        <option value="5°">5°</option>
                        <option value="6°">6°</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Grupo *</label>
                    <select id="grupo" class="form-select" required>
                        <option value="">—Seleccione—</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Carrera *</label>
                    <select id="carrera" class="form-select" required>
                        <option value="">—Seleccione—</option>
                        <option value="Soporte y Mantenimiento de Equipo de Cómputo">Soporte y Mantenimiento de Equipo de Cómputo</option>
                        <option value="Soporte y Gestión de Tecnologías Informáticas">Soporte y Gestión de Tecnologías Informáticas</option>
                        <option value="Enfermería General">Enfermería General</option>
                        <option value="Ventas">Ventas</option>
                        <option value="Diseño Gráfico Digital">Diseño Gráfico Digital</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4 position-relative">
                    <label class="form-label">Asesor(a) (Docente)</label>
                    <input type="text" 
                           id="asesorBusqueda"
                           class="form-control"
                           placeholder="Buscar asesor..."
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')">
                    <input type="hidden" id="id_asesor" value="">
                    <div id="asesorResults" class="search-results-dropdown"></div>
                    <small class="text-muted" id="asesorSeleccionado"></small>
                </div>
                <div class="col-md-4 position-relative">
                    <label class="form-label">Tutor(a) (Trabajo Social)</label>
                    <input type="text" 
                           id="tutorBusqueda"
                           class="form-control"
                           placeholder="Buscar tutor..."
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')">
                    <input type="hidden" id="id_tutor" value="">
                    <div id="tutorResults" class="search-results-dropdown"></div>
                    <small class="text-muted" id="tutorSeleccionado"></small>
                </div>
            </div>

            <!-- Contraseña y botones -->
            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Contraseña de Administrador *
                    </label>
                    <input type="password" 
                           class="form-control" 
                           id="current_password" 
                           placeholder="Ingrese su contraseña para autorizar"
                           required>
                    <small class="text-muted">Requerida para confirmar cualquier modificación</small>
                </div>
                <div class="col-md-8 d-flex align-items-end justify-content-end">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" id="btnGuardarGrupo">
                            <i class="fas fa-save me-2"></i>Guardar Grupo
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCancelarEdicion" style="display:none;">
                            <i class="fas fa-times me-2"></i>Cancelar Edición
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormulario()">
                            <i class="fas fa-times me-2"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Filtros y búsqueda -->
    <div class="module-section">
        <h5 class="mb-4"><i class="fas fa-search"></i> CONSULTA DE GRUPOS</h5>
        
        <div class="row mb-3">
            <div class="col-md-5">
                <label class="form-label">Búsqueda general:</label>
                <div class="input-group">
                    <input type="text" id="busquedaGeneral" class="form-control" 
                           placeholder="Buscar por semestre, grupo, carrera, asesor o tutor..."
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')">
                    <button class="btn btn-primary" type="button" id="btnBuscar">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-outline-secondary" type="button" id="btnLimpiarFiltros" style="display:none;">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-2">
                <label class="form-label">Filtrar por semestre:</label>
                <select id="filtroSemestre" class="form-select">
                    <option value="">—Todos—</option>
                    <option value="1°">1°</option>
                    <option value="2°">2°</option>
                    <option value="3°">3°</option>
                    <option value="4°">4°</option>
                    <option value="5°">5°</option>
                    <option value="6°">6°</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Filtrar por grupo:</label>
                <select id="filtroGrupo" class="form-select">
                    <option value="">—Todos—</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Filtrar por carrera:</label>
                <select id="filtroCarrera" class="form-select">
                    <option value="">—Todas—</option>
                    <option value="Soporte y Mantenimiento de Equipo de Cómputo">Soporte y Mantenimiento de Equipo de Cómputo</option>
                    <option value="Soporte y Gestión de Tecnologías Informáticas">Soporte y Gestión de Tecnologías Informáticas</option>
                    <option value="Enfermería General">Enfermería General</option>
                    <option value="Ventas">Ventas</option>
                    <option value="Diseño Gráfico Digital">Diseño Gráfico Digital</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-primary w-100" type="button" id="btnAplicarFiltros">
                    <i class="fas fa-filter"></i> Aplicar Filtros
                </button>
            </div>
        </div>

        <!-- Resultados de la búsqueda -->
        <div class="row mb-3">
            <div class="col-12">
                <span class="filter-badge" id="totalResultados">
                    <i class="fas fa-users"></i> 0 grupo(s)
                </span>
                <span class="filter-badge" id="filtrosActivos" style="display:none;"></span>
            </div>
        </div>

        <!-- Tabla de grupos -->
        <div class="table-container">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Semestre</th>
                        <th>Grupo</th>
                        <th>Carrera</th>
                        <th>Asesor</th>
                        <th>Tutor</th>
                        <th>Cantidad de Alumnos</th>
                        @if($puedeEditar)
                            <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="tablaGrupos">
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Realice una búsqueda para ver resultados</h5>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
    <!-- Migración de Grupos -->
    @if($puedeEditar)
    <div class="module-section mt-4">
        <h5 class="mb-4"><i class="fa-solid fa-arrow-right-arrow-left"></i> MIGRACIÓN DE GRUPOS</h5>
        
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>¿Qué hace esta función?</strong>
            <ul class="mb-0 mt-2">
                <li>Los alumnos de <strong>1° a 5° semestre</strong> serán ascendidos al siguiente semestre.</li>
                <li>Los alumnos de <strong>6° semestre</strong> serán marcados como <strong>graduados</strong> (estatus inactivo).</li>
                <li>Los alumnos graduados perderán su asignación de grupo y se eliminarán sus becas asociadas.</li>
                <li>Se requiere que exista el grupo destino para que la migración sea exitosa.</li>
            </ul>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-warning bg-opacity-10 mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-exclamation-triangle text-warning"></i> Advertencia</h6>
                        <p class="card-text small">
                            Esta acción no se puede deshacer fácilmente. Se recomienda hacer un respaldo de la base de datos antes de continuar.
                            Los cambios son permanentes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3 pt-3 border-top">
            <div class="col-md-4">
                <label class="form-label">
                    <i class="fas fa-lock me-2"></i>Contraseña de Administrador *
                </label>
                <input type="password" 
                    class="form-control" 
                    id="current_password_migracion" 
                    placeholder="Ingrese su contraseña para autorizar"
                    required>
                <small class="text-muted">Requerida para confirmar la migración</small>
            </div>
            <div class="col-md-8 d-flex align-items-end justify-content-end">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" id="btnMigrarGrupos">
                        <i class="fas fa-arrow-right me-2"></i>Mover alumnos al siguiente semestre
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Modal para mostrar resultados detallados -->
        <div class="modal fade" id="modalResultadosMigracion" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-chart-bar"></i> Resultados de la Migración</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalResultadosBody">
                        <!-- Resultados se insertarán aquí -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ELEMENTOS COMUNES (disponibles para todos)
    const busquedaGeneral = document.getElementById('busquedaGeneral');
    const filtroSemestre = document.getElementById('filtroSemestre');
    const filtroGrupo = document.getElementById('filtroGrupo');
    const filtroCarrera = document.getElementById('filtroCarrera');
    const btnBuscar = document.getElementById('btnBuscar');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    const btnAplicarFiltros = document.getElementById('btnAplicarFiltros');
    const tablaGrupos = document.getElementById('tablaGrupos');
    const totalResultados = document.getElementById('totalResultados');
    const filtrosActivos = document.getElementById('filtrosActivos');
    
    // Variables para saber si puede editar (desde PHP)
    const puedeEditar = @json($puedeEditar);
    
    // FUNCIONES PARA LISTAR GRUPOS (TODOS PUEDEN)
    function listarGrupos() {
        const params = new URLSearchParams({
            busqueda: busquedaGeneral ? busquedaGeneral.value : '',
            semestre: filtroSemestre ? filtroSemestre.value : '',
            grupo: filtroGrupo ? filtroGrupo.value : '',
            carrera: filtroCarrera ? filtroCarrera.value : ''
        });

        fetch(`{{ route("admin.grupos.listar") }}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarTabla(data.grupos);
                if (totalResultados) {
                    totalResultados.innerHTML = `<i class="fas fa-users"></i> ${data.total} grupo(s)`;
                }
                
                // Mostrar filtros activos
                let filtrosTexto = [];
                if (busquedaGeneral && busquedaGeneral.value) filtrosTexto.push(`Buscando: "${busquedaGeneral.value}"`);
                if (filtroSemestre && filtroSemestre.value) filtrosTexto.push(`Semestre: ${filtroSemestre.value}`);
                if (filtroGrupo && filtroGrupo.value) filtrosTexto.push(`Grupo: ${filtroGrupo.value}`);
                if (filtroCarrera && filtroCarrera.value) filtrosTexto.push(`Carrera: ${filtroCarrera.value}`);
                
                if (filtrosTexto.length > 0 && filtrosActivos) {
                    filtrosActivos.innerHTML = `<i class="fas fa-filter"></i> ${filtrosTexto.join(' | ')}`;
                    filtrosActivos.style.display = 'inline-block';
                    if (btnLimpiarFiltros) btnLimpiarFiltros.style.display = 'inline-block';
                } else {
                    if (filtrosActivos) filtrosActivos.style.display = 'none';
                    if (btnLimpiarFiltros) btnLimpiarFiltros.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error al listar grupos:', error);
        });
    }

    function renderizarTabla(grupos) {
        if (!tablaGrupos) return;
        
        if (grupos.length === 0) {
            let colspan = puedeEditar ? 6 : 5;
            tablaGrupos.innerHTML = `
                <tr>
                    <td colspan="${colspan}" class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron grupos</h5>
                        <p class="text-muted">Intente con otros filtros de búsqueda</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        
        grupos.forEach(g => {
            html += `<tr data-id="${g.id}">
                <td>${g.semestre}</td>
                <td>${g.grupo}</td>
                <td>${g.carrera}</td>
                <td>${g.nombre_asesor}</td>
                <td>${g.nombre_tutor}</td>
                <td>${g.cantidad_alumnos}</td>`;
            
            if (puedeEditar) {
                html += `<td class="action-buttons">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary editar-btn" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger eliminar-btn" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>`;
            }
            
            html += `</tr>`;
        });
        
        tablaGrupos.innerHTML = html;
        
        // Si puede editar, agregar eventos a los botones
        if (puedeEditar) {
            document.querySelectorAll('.editar-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const id = row.dataset.id;
                    if (typeof cargarGrupoParaEditar === 'function') {
                        cargarGrupoParaEditar(id);
                    }
                });
            });
            
            document.querySelectorAll('.eliminar-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const id = row.dataset.id;
                    if (typeof eliminarGrupo === 'function') {
                        eliminarGrupo(id);
                    }
                });
            });
        }
    }

    // EVENT LISTENERS PARA FILTROS (TODOS PUEDEN)
    if (btnBuscar) btnBuscar.addEventListener('click', listarGrupos);
    if (btnAplicarFiltros) btnAplicarFiltros.addEventListener('click', listarGrupos);
    
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', function() {
            if (busquedaGeneral) busquedaGeneral.value = '';
            if (filtroSemestre) filtroSemestre.value = '';
            if (filtroGrupo) filtroGrupo.value = '';
            if (filtroCarrera) filtroCarrera.value = '';
            listarGrupos();
        });
    }
    
    // Permitir búsqueda con Enter
    if (busquedaGeneral) {
        busquedaGeneral.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                listarGrupos();
            }
        });
    }

    // Cargar lista inicial
    listarGrupos();

    // FUNCIONES SOLO PARA EDITORES (G)
    @if($puedeEditar)
    
    // Elementos del DOM para edición
    const grupoId = document.getElementById('grupoId');
    const semestre = document.getElementById('semestre');
    const grupo = document.getElementById('grupo');
    const carrera = document.getElementById('carrera');
    const idAsesor = document.getElementById('id_asesor');
    const asesorBusqueda = document.getElementById('asesorBusqueda');
    const asesorResults = document.getElementById('asesorResults');
    const asesorSeleccionado = document.getElementById('asesorSeleccionado');
    const idTutor = document.getElementById('id_tutor');
    const tutorBusqueda = document.getElementById('tutorBusqueda');
    const tutorResults = document.getElementById('tutorResults');
    const tutorSeleccionado = document.getElementById('tutorSeleccionado');
    const currentPassword = document.getElementById('current_password');
    const btnGuardar = document.getElementById('btnGuardarGrupo');
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicion');

    // Variables para búsquedas
    let asesorTimeout = null;
    let tutorTimeout = null;
    let asesorResultados = [];
    let tutorResultados = [];
    let selectedAsesorIndex = -1;
    let selectedTutorIndex = -1;

    // FUNCIONES PARA BÚSQUEDA DE ASESORES/TUTORES
    function buscarUsuarios(tipo, busqueda, callback) {
        fetch('{{ route("admin.grupos.buscar-usuarios") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ tipo: tipo, busqueda: busqueda })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                callback(data.usuarios);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Búsqueda de asesores
    if (asesorBusqueda) {
        asesorBusqueda.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(asesorTimeout);
            asesorTimeout = setTimeout(() => {
                buscarUsuarios('asesor', query, (usuarios) => {
                    asesorResultados = usuarios;
                    mostrarResultadosAsesor(usuarios);
                });
            }, 300);
        });

        // Navegación con teclado para asesores
        asesorBusqueda.addEventListener('keydown', function(e) {
            if (asesorResults.style.display !== 'block' || asesorResultados.length === 0) return;

            const items = document.querySelectorAll('#asesorResults .search-result-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedAsesorIndex = (selectedAsesorIndex + 1) % items.length;
                updateHighlight(items, selectedAsesorIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedAsesorIndex = (selectedAsesorIndex - 1 + items.length) % items.length;
                updateHighlight(items, selectedAsesorIndex);
            } else if (e.key === 'Enter' && selectedAsesorIndex >= 0) {
                e.preventDefault();
                seleccionarAsesor(asesorResultados[selectedAsesorIndex]);
            }
        });

        // Cargar sugerencias al hacer focus
        asesorBusqueda.addEventListener('focus', function() {
            if (this.value === '') {
                buscarUsuarios('asesor', '', (usuarios) => {
                    asesorResultados = usuarios;
                    mostrarResultadosAsesor(usuarios);
                });
            }
        });
    }

    // Botón para limpiar asesor
    function agregarBotonLimpiarAsesor() {
        const asesorContainer = document.querySelector('#asesorBusqueda').closest('.col-md-4');
        if (asesorContainer && !document.getElementById('btnLimpiarAsesor')) {
            const btnLimpiar = document.createElement('button');
            btnLimpiar.id = 'btnLimpiarAsesor';
            btnLimpiar.type = 'button';
            btnLimpiar.className = 'btn btn-sm btn-outline-secondary mt-1';
            btnLimpiar.innerHTML = '<i class="fas fa-times"></i> Quitar asesor';
            btnLimpiar.style.fontSize = '12px';
            btnLimpiar.onclick = function() {
                if (idAsesor) idAsesor.value = '';
                if (asesorBusqueda) asesorBusqueda.value = '';
                if (asesorSeleccionado) asesorSeleccionado.innerHTML = '';
            };
            asesorContainer.appendChild(btnLimpiar);
        }
    }

    // Botón para limpiar tutor
    function agregarBotonLimpiarTutor() {
        const tutorContainer = document.querySelector('#tutorBusqueda').closest('.col-md-4');
        if (tutorContainer && !document.getElementById('btnLimpiarTutor')) {
            const btnLimpiar = document.createElement('button');
            btnLimpiar.id = 'btnLimpiarTutor';
            btnLimpiar.type = 'button';
            btnLimpiar.className = 'btn btn-sm btn-outline-secondary mt-1';
            btnLimpiar.innerHTML = '<i class="fas fa-times"></i> Quitar tutor';
            btnLimpiar.style.fontSize = '12px';
            btnLimpiar.onclick = function() {
                if (idTutor) idTutor.value = '';
                if (tutorBusqueda) tutorBusqueda.value = '';
                if (tutorSeleccionado) tutorSeleccionado.innerHTML = '';
            };
            tutorContainer.appendChild(btnLimpiar);
        }
    }

    agregarBotonLimpiarAsesor();
    agregarBotonLimpiarTutor();

    // Búsqueda de tutores
    if (tutorBusqueda) {
        tutorBusqueda.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(tutorTimeout);
            tutorTimeout = setTimeout(() => {
                buscarUsuarios('tutor', query, (usuarios) => {
                    tutorResultados = usuarios;
                    mostrarResultadosTutor(usuarios);
                });
            }, 300);
        });

        // Navegación con teclado para tutores
        tutorBusqueda.addEventListener('keydown', function(e) {
            if (tutorResults.style.display !== 'block' || tutorResultados.length === 0) return;

            const items = document.querySelectorAll('#tutorResults .search-result-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedTutorIndex = (selectedTutorIndex + 1) % items.length;
                updateHighlight(items, selectedTutorIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedTutorIndex = (selectedTutorIndex - 1 + items.length) % items.length;
                updateHighlight(items, selectedTutorIndex);
            } else if (e.key === 'Enter' && selectedTutorIndex >= 0) {
                e.preventDefault();
                seleccionarTutor(tutorResultados[selectedTutorIndex]);
            }
        });

        // Cargar sugerencias al hacer focus
        tutorBusqueda.addEventListener('focus', function() {
            if (this.value === '') {
                buscarUsuarios('tutor', '', (usuarios) => {
                    tutorResultados = usuarios;
                    mostrarResultadosTutor(usuarios);
                });
            }
        });
    }

    function mostrarResultadosAsesor(usuarios) {
        if (!asesorResults) return;
        asesorResults.innerHTML = '';
        if (usuarios.length === 0) {
            asesorResults.style.display = 'none';
            return;
        }
        
        usuarios.forEach((user, index) => {
            const div = document.createElement('div');
            div.className = 'search-result-item';
            div.dataset.index = index;
            div.innerHTML = `<strong>${user.nombre_completo}</strong><br><small>Num. Empleado: ${user.num_empleado}</small>`;
            div.addEventListener('click', () => seleccionarAsesor(user));
            asesorResults.appendChild(div);
        });
        
        asesorResults.style.display = 'block';
        selectedAsesorIndex = -1;
    }

    function mostrarResultadosTutor(usuarios) {
        if (!tutorResults) return;
        tutorResults.innerHTML = '';
        if (usuarios.length === 0) {
            tutorResults.style.display = 'none';
            return;
        }
        
        usuarios.forEach((user, index) => {
            const div = document.createElement('div');
            div.className = 'search-result-item';
            div.dataset.index = index;
            div.innerHTML = `<strong>${user.nombre_completo}</strong><br><small>Num. Empleado: ${user.num_empleado}</small>`;
            div.addEventListener('click', () => seleccionarTutor(user));
            tutorResults.appendChild(div);
        });
        
        tutorResults.style.display = 'block';
        selectedTutorIndex = -1;
    }

    function seleccionarAsesor(user) {
        if (!idAsesor || !asesorBusqueda || !asesorSeleccionado) return;
        idAsesor.value = user.id;
        asesorBusqueda.value = user.nombre_completo;
        asesorSeleccionado.innerHTML = `<i class="fas fa-check-circle text-success"></i> Asesor: ${user.nombre_completo}`;
        asesorResults.style.display = 'none';
    }

    function seleccionarTutor(user) {
        if (!idTutor || !tutorBusqueda || !tutorSeleccionado) return;
        idTutor.value = user.id;
        tutorBusqueda.value = user.nombre_completo;
        tutorSeleccionado.innerHTML = `<i class="fas fa-check-circle text-success"></i> Tutor: ${user.nombre_completo}`;
        tutorResults.style.display = 'none';
    }

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

    // Ocultar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (asesorResults && !asesorBusqueda.contains(e.target) && !asesorResults.contains(e.target)) {
            asesorResults.style.display = 'none';
        }
        if (tutorResults && !tutorBusqueda.contains(e.target) && !tutorResults.contains(e.target)) {
            tutorResults.style.display = 'none';
        }
    });

    // FUNCIONES PARA CARGAR/EDITAR GRUPOS
    function cargarGrupoParaEditar(id) {
        fetch(`{{ url('admin/grupos') }}/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const g = data.grupo;
                if (grupoId) grupoId.value = g.id;
                if (semestre) semestre.value = g.semestre;
                if (grupo) grupo.value = g.grupo;
                if (carrera) carrera.value = g.carrera;
                
                // Cargar asesor
                if (g.id_asesor) {
                    if (idAsesor) idAsesor.value = g.id_asesor;
                    if (asesorBusqueda) asesorBusqueda.value = g.nombre_asesor;
                    if (asesorSeleccionado) asesorSeleccionado.innerHTML = `<i class="fas fa-check-circle text-success"></i> Asesor: ${g.nombre_asesor}`;
                } else {
                    if (idAsesor) idAsesor.value = '';
                    if (asesorBusqueda) asesorBusqueda.value = '';
                    if (asesorSeleccionado) asesorSeleccionado.innerHTML = '';
                }
                
                // Cargar tutor
                if (g.id_tutor) {
                    if (idTutor) idTutor.value = g.id_tutor;
                    if (tutorBusqueda) tutorBusqueda.value = g.nombre_tutor;
                    if (tutorSeleccionado) tutorSeleccionado.innerHTML = `<i class="fas fa-check-circle text-success"></i> Tutor: ${g.nombre_tutor}`;
                } else {
                    if (idTutor) idTutor.value = '';
                    if (tutorBusqueda) tutorBusqueda.value = '';
                    if (tutorSeleccionado) tutorSeleccionado.innerHTML = '';
                }
                
                if (btnCancelarEdicion) btnCancelarEdicion.style.display = 'inline-block';
                
                // Scroll al formulario
                const formGrupo = document.getElementById('formGrupo');
                if (formGrupo) formGrupo.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    // FUNCIONES PARA GUARDAR
    if (btnGuardar) {
        btnGuardar.addEventListener('click', function() {
            if (!currentPassword || !currentPassword.value) {
                alert('Debe ingresar su contraseña de administrador.');
                if (currentPassword) currentPassword.focus();
                return;
            }
            
            if (!semestre.value || !grupo.value || !carrera.value) {
                alert('Debe completar los campos obligatorios: Semestre, Grupo y Carrera.');
                return;
            }

            const data = {
                current_password: currentPassword.value,
                id: grupoId ? (grupoId.value || null) : null,
                semestre: semestre.value,
                grupo: grupo.value,
                carrera: carrera.value,
                id_asesor: idAsesor ? (idAsesor.value || null) : null,
                id_tutor: idTutor ? (idTutor.value || null) : null
            };

            // Deshabilitar botón mientras se procesa
            btnGuardar.disabled = true;
            const originalText = btnGuardar.innerHTML;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

            fetch('{{ route("admin.grupos.guardar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    limpiarFormulario();
                    listarGrupos(); // Refrescar tabla
                } else {
                    alert('Error: ' + data.message);
                    // No limpiar la contraseña para que pueda reintentar
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al guardar.');
            })
            .finally(() => {
                // Rehabilitar botón
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = originalText;
            });
        });
    }

    // FUNCIONES PARA ELIMINAR
    function eliminarGrupo(id) {
        if (!currentPassword || !currentPassword.value) {
            alert('Debe ingresar su contraseña de administrador para eliminar.');
            if (currentPassword) currentPassword.focus();
            return;
        }
        
        if (!confirm('⚠️ ¿Está seguro de eliminar este grupo?\n\nEsta acción no se puede deshacer. Los alumnos de este grupo no se verán afectados, pero quedarán sin grupo asignado.')) {
            return;
        }

        // Deshabilitar botón de eliminar temporalmente (buscamos el botón específico)
        const deleteBtn = event?.target?.closest('.eliminar-btn');
        if (deleteBtn) {
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        fetch(`{{ url('admin/grupos') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ current_password: currentPassword.value })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // LIMPIAR CONTRASEÑA después de eliminar exitosamente
                if (currentPassword) currentPassword.value = '';
                listarGrupos(); // Refrescar tabla
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al eliminar.');
        })
        .finally(() => {
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            }
        });
    }

    // Botón cancelar edición
    if (btnCancelarEdicion) {
        btnCancelarEdicion.addEventListener('click', function() {
            if (typeof limpiarFormulario === 'function') limpiarFormulario();
        });
    }

    @endif

    // FUNCIONES PARA MIGRACIÓN DE GRUPOS
    @if($puedeEditar)
    const btnMigrarGrupos = document.getElementById('btnMigrarGrupos');
    const btnActualizarEstadisticas = document.getElementById('btnActualizarEstadisticas');
    const currentPasswordMigracion = document.getElementById('current_password_migracion');

    // Ejecutar migración
    if (btnMigrarGrupos) {
        btnMigrarGrupos.addEventListener('click', function() {
            if (!currentPasswordMigracion || !currentPasswordMigracion.value) {
                alert('Debe ingresar su contraseña de administrador para realizar la migración.');
                if (currentPasswordMigracion) currentPasswordMigracion.focus();
                return;
            }
            
            if (!confirm('⚠️ ADVERTENCIA: Esta acción moverá a todos los alumnos al siguiente semestre.\n\n' +
                        '• Alumnos de 1°-5° semestre: Ascenderán un semestre\n' +
                        '• Alumnos de 6° semestre: Serán marcados como graduados (inactivos)\n' +
                        '• Se eliminarán las becas de los alumnos graduados\n\n' +
                        '¿Está SEGURO de que desea continuar? Esta acción NO se puede deshacer fácilmente.')) {
                return;
            }
            
            // Deshabilitar botón durante la operación
            btnMigrarGrupos.disabled = true;
            btnMigrarGrupos.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';
            
            fetch('{{ route("admin.grupos.migrar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_password: currentPasswordMigracion.value
                })
            })
            .then(response => response.json())
            .then(data => {
                // Mostrar resultados usando SweetAlert si está disponible, o alert normal
                const mensajeResumen = `${data.message}\n\nMigrados: ${data.resultados.migrados}\nGraduados: ${data.resultados.graduados}\nErrores: ${data.resultados.errores ? data.resultados.errores.length : 0}`;
                
                if (typeof Swal !== 'undefined') {
                    // Si tienes SweetAlert instalado
                    Swal.fire({
                        title: data.success ? '¡Migración Completada!' : 'Migración con Problemas',
                        html: `
                            <p>${data.message}</p>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="text-success">
                                        <h3>${data.resultados.migrados}</h3>
                                        <small>Migrados</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-info">
                                        <h3>${data.resultados.graduados}</h3>
                                        <small>Graduados</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-danger">
                                        <h3>${data.resultados.errores ? data.resultados.errores.length : 0}</h3>
                                        <small>Errores</small>
                                    </div>
                                </div>
                            </div>
                            ${data.resultados.detalles ? `<div class="mt-3"><small>${data.resultados.detalles.slice(0, 5).join('<br>')}${data.resultados.detalles.length > 5 ? '<br>...' : ''}</small></div>` : ''}
                        `,
                        icon: data.success ? 'success' : 'warning'
                    });
                } else {
                    // Fallback a alert normal
                    alert(mensajeResumen);
                    
                    // Si quieres ver detalles, console.log
                    console.log('Detalles de migración:', data.resultados);
                }
                
                // Refrescar la tabla de grupos
                if (typeof listarGrupos === 'function') {
                    listarGrupos();
                }
                
                // Limpiar campo de contraseña
                if (currentPasswordMigracion) currentPasswordMigracion.value = '';
                
                // Recargar estadísticas
                if (typeof cargarEstadisticasPrevias === 'function') {
                    cargarEstadisticasPrevias();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión durante la migración: ' + error.message);
            })
            .finally(() => {
                // Rehabilitar botón
                btnMigrarGrupos.disabled = false;
                btnMigrarGrupos.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Mover alumnos al siguiente semestre';
            });
        });
    }
    @endif
});

// Función global para limpiar formulario
function limpiarFormulario() {
    const grupoId = document.getElementById('grupoId');
    const semestre = document.getElementById('semestre');
    const grupo = document.getElementById('grupo');
    const carrera = document.getElementById('carrera');
    const idAsesor = document.getElementById('id_asesor');
    const asesorBusqueda = document.getElementById('asesorBusqueda');
    const asesorSeleccionado = document.getElementById('asesorSeleccionado');
    const idTutor = document.getElementById('id_tutor');
    const tutorBusqueda = document.getElementById('tutorBusqueda');
    const tutorSeleccionado = document.getElementById('tutorSeleccionado');
    const currentPassword = document.getElementById('current_password');
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicion');
    
    if (grupoId) grupoId.value = '';
    if (semestre) semestre.value = '';
    if (grupo) grupo.value = '';
    if (carrera) carrera.value = '';
    if (idAsesor) idAsesor.value = '';
    if (asesorBusqueda) asesorBusqueda.value = '';
    if (asesorSeleccionado) asesorSeleccionado.innerHTML = '';
    if (idTutor) idTutor.value = '';
    if (tutorBusqueda) tutorBusqueda.value = '';
    if (tutorSeleccionado) tutorSeleccionado.innerHTML = '';
    if (currentPassword) currentPassword.value = '';
    if (btnCancelarEdicion) btnCancelarEdicion.style.display = 'none';
}
</script>
@endpush