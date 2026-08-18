@extends('layouts.app')

@section('title', 'Consulta de Datos Prioritarios - SGGDI')

@php
    use App\Helpers\PrivilegiosHelper;
    $usuarioActual = Auth::user();
    $puedeVerIndividual = false;
    
    if ($usuarioActual) {
        $privilegios = $usuarioActual->privilegios ?? 'NNNNN';
        $pos1 = $privilegios[1] ?? 'N';
        $pos2 = $privilegios[2] ?? 'N';
        $puedeVerIndividual = in_array($pos1, ['C', 'G']) || in_array($pos2, ['C', 'G']);
    }

    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Datos y Estadísticas',
            'url' => route('datos-estadisticas.index'),
            'icon' => 'fa-chart-column'
        ],
        [
            'name' => 'Consulta de Datos Prioritarios',
            'url' => null,
            'icon' => 'fa-file-circle-exclamation'
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
    .filter-badge {
        background-color: #e9ecef;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-block;
    }

    /* Estilos para el menú de resultados de búsqueda */
    .search-wrapper {
        position: relative;
        width: 100%;
    }

    #resultadosBusqueda, #localidadResults {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        background: white;
        border: 1px solid #ccc;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-height: 300px;
        overflow-y: auto;
        margin-top: 2px;
    }

    .search-result-item {
        padding: 10px 15px;
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

    input#busqueda, input#localidad_filtro {
        autocomplete: off;
        autocorrect: off;
        autocapitalize: off;
        spellcheck: false;
    }
    
    .section-separator {
        border-top: 1px solid #dee2e6;
        margin: 1rem 0;
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
                <i class="fas fa-file-circle-exclamation me-2 text-success"></i>
                CONSULTA DE DATOS PRIORITARIOS
            </h2>
        </div>
    </div>
    
    <div class="module-section">
       
        @if($puedeVerIndividual)
        <!-- Búsqueda individual de fichas de alumnos -->
        <h5 class="mb-4"><i class="fa-solid fa-user-circle"></i> BÚSQUEDA INDIVIDUAL</h5>

        <div class="row mb-4">
            <div class="col-md-5">
                <label class="form-label"><i class="fas fa-search"></i> Buscar alumno:</label>
                <div class="search-wrapper">
                    <div class="input-group">
                        <input type="text" id="busqueda" class="form-control" 
                            placeholder="Núm. control, nombre, apellidos, CURP"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="off"
                            spellcheck="false">
                        <button type="button" id="btnBuscar" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <button type="button" id="btnLimpiarBusqueda" class="btn btn-outline-secondary align-items-center" style="display: none;">
                            <i class="fas fa-times me-2"></i> Limpiar
                        </button>
                    </div>
                    <div id="resultadosBusqueda" class="search-results-dropdown" style="display: none;"></div>
                </div>
            </div>
        </div>

        <div class="section-separator"></div>
        @endif
        
        <h5 class="mb-4 mt-4"><i class="fa-solid fa-people-group"></i> FILTRAR POR GRUPOS</h5>

        <div class="row mb-4">            
            <div class="col-md-2">
                <label class="form-label"><i class="fas fa-filter"></i> Semestre:</label>
                <select id="semestre" class="form-select">
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
                <label class="form-label"><i class="fas fa-filter"></i> Grupo:</label>
                <select id="grupo_letra" class="form-select">
                    <option value="">—Todos—</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label"><i class="fas fa-filter"></i> Carrera:</label>
                <select id="carrera" class="form-select">
                    <option value="">—Todas—</option>
                    <option value="Soporte y Mantenimiento de Equipo de Cómputo">Soporte y Mantenimiento de Equipo de Cómputo</option>
                    <option value="Soporte y Gestión de Tecnologías Informáticas">Soporte y Gestión de Tecnologías Informáticas</option>
                    <option value="Enfermería General">Enfermería General</option>
                    <option value="Ventas">Ventas</option>
                    <option value="Diseño Gráfico Digital">Diseño Gráfico Digital</option>
                </select>
            </div>
        </div>

        <h5 class="mb-4"><i class="fa-solid fa-circle-exclamation"></i> OPCIONES DE DATOS PRIORITARIOS</h5>

        <div class="row mb-3 align-items-start">
            <div class="col-md-12">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="opcion_prioritaria" id="opcion_contactos" value="contactos" checked>
                    <label class="form-check-label" for="opcion_contactos">Primeros 2 números de contacto</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="opcion_prioritaria" id="opcion_telefonos" value="telefonos">
                    <label class="form-check-label" for="opcion_telefonos">Teléfonos Celulares personales</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="opcion_prioritaria" id="opcion_domicilio" value="domicilio">
                    <label class="form-check-label" for="opcion_domicilio">Domicilio</label>
                </div>
            </div>
        </div>

        <!-- Panel de Localidad (solo visible cuando se selecciona Domicilio) -->
        <div id="panelLocalidad" class="row mb-3" style="display: none;">
            <div class="col-md-6">
                <label class="form-label"><i class="fas fa-map-marker-alt"></i> Filtrar por Localidad (opcional)</label>
                <div class="search-wrapper">
                    <input type="text" id="localidad_filtro" class="form-control" 
                        placeholder="Buscar localidad específica..."
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="off"
                        spellcheck="false">
                    <div id="localidadResults" class="search-results-dropdown" style="display: none;"></div>
                </div>
                <small class="text-muted">Dejar vacío para mostrar todas las localidades</small>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12 d-flex align-items-end justify-content-end">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" id="btnAplicarFiltros">
                        <i class="fas fa-play me-2"></i>Aplicar Filtros y Opciones
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnCancelarFiltros">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <span class="filter-badge" id="totalAlumnosBadge">
                    <i class="fas fa-users"></i> <span id="totalAlumnos">0</span> alumno(s) encontrado(s)
                </span>
            </div>
        </div>

        <!-- Panel de contraseña y botones -->
        <div class="row mt-4 pt-4 border-top">
            <div class="col-md-4">
                <label for="current_password_global" class="form-label">
                    <i class="fas fa-lock me-2"></i>Contraseña de Usuario *
                </label>
                <input type="password" 
                    class="form-control" 
                    id="current_password_global" 
                    placeholder="Ingrese su contraseña para autorizar">
                <small class="text-muted">Requerida para confirmar cualquier operación</small>
            </div>
            <div class="col-md-8 d-flex align-items-end justify-content-end">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="btnDescargarDocumento" disabled>
                        <i class="fas fa-download me-2"></i>Descargar PDF
                    </button>
                    <a href="{{ route('datos-estadisticas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===========================================
    // VARIABLES GLOBALES
    // ===========================================
    let busquedaTimeout = null;
    let resultadosAlumnos = [];
    let selectedAlumnoIndex = -1;
    let pendingAlumnoId = null;
    
    let localidadTimeout = null;
    let resultadosLocalidades = [];
    let selectedLocalidadIndex = -1;
    
    let totalAlumnosEncontrados = 0;
    let filtrosActuales = null;
    let modoIndividualActivo = false;
    
    // Verificar qué elementos existen en la página
    const hayBusquedaIndividual = document.getElementById('busqueda') !== null;
    
    // ===========================================
    // ELEMENTOS DEL DOM (con verificación de existencia)
    // ===========================================
    const busquedaInput = document.getElementById('busqueda');
    const btnBuscar = document.getElementById('btnBuscar');
    const btnLimpiarBusqueda = document.getElementById('btnLimpiarBusqueda');
    const btnAplicarFiltros = document.getElementById('btnAplicarFiltros');
    const btnCancelarFiltros = document.getElementById('btnCancelarFiltros');
    const btnDescargar = document.getElementById('btnDescargarDocumento');
    const totalAlumnosSpan = document.getElementById('totalAlumnos');
    
    const semestreSelect = document.getElementById('semestre');
    const grupoLetraSelect = document.getElementById('grupo_letra');
    const carreraSelect = document.getElementById('carrera');
    const localidadFiltro = document.getElementById('localidad_filtro');
    const panelLocalidad = document.getElementById('panelLocalidad');
    const passwordInput = document.getElementById('current_password_global');
    
    const opcionContactos = document.getElementById('opcion_contactos');
    const opcionTelefonos = document.getElementById('opcion_telefonos');
    const opcionDomicilio = document.getElementById('opcion_domicilio');
    
    // ===========================================
    // CREAR CONTENEDORES DE RESULTADOS (solo si existen los inputs)
    // ===========================================
    let resultsContainer = null;
    let alumnoIdHidden = null;
    let localidadResultsContainer = null;
    
    if (busquedaInput) {
        resultsContainer = document.getElementById('resultadosBusqueda');
        if (!resultsContainer) {
            resultsContainer = document.createElement('div');
            resultsContainer.id = 'resultadosBusqueda';
            resultsContainer.className = 'search-results-dropdown';
            resultsContainer.style.display = 'none';
            busquedaInput.parentNode.appendChild(resultsContainer);
        }
        
        alumnoIdHidden = document.getElementById('alumno_seleccionado_id');
        if (!alumnoIdHidden) {
            alumnoIdHidden = document.createElement('input');
            alumnoIdHidden.type = 'hidden';
            alumnoIdHidden.id = 'alumno_seleccionado_id';
            busquedaInput.parentNode.appendChild(alumnoIdHidden);
        }
    }
    
    if (localidadFiltro) {
        localidadResultsContainer = document.getElementById('localidadResults');
        if (!localidadResultsContainer) {
            localidadResultsContainer = document.createElement('div');
            localidadResultsContainer.id = 'localidadResults';
            localidadResultsContainer.className = 'search-results-dropdown';
            localidadResultsContainer.style.display = 'none';
            localidadFiltro.parentNode.appendChild(localidadResultsContainer);
        }
    }
    
    // ===========================================
    // FUNCIONES DE RESET
    // ===========================================
    function resetearModoFiltros() {
        if (semestreSelect) semestreSelect.value = '';
        if (grupoLetraSelect) grupoLetraSelect.value = '';
        if (carreraSelect) carreraSelect.value = '';
        if (opcionContactos) opcionContactos.checked = true;
        if (localidadFiltro) localidadFiltro.value = '';
        if (panelLocalidad) panelLocalidad.style.display = 'none';
        if (totalAlumnosSpan) totalAlumnosSpan.textContent = '0';
        filtrosActuales = null;
        modoIndividualActivo = false;
        if (btnDescargar) btnDescargar.disabled = true;
    }
    
    function resetearBusquedaIndividual() {
        if (busquedaInput) busquedaInput.value = '';
        pendingAlumnoId = null;
        if (alumnoIdHidden) alumnoIdHidden.value = '';
        if (resultsContainer) resultsContainer.style.display = 'none';
        if (btnLimpiarBusqueda) btnLimpiarBusqueda.style.display = 'none';
        modoIndividualActivo = false;
    }
    
    function limpiarContraseña() {
        if (passwordInput) passwordInput.value = '';
    }
    
    function resetearTodo() {
        resetearModoFiltros();
        resetearBusquedaIndividual();
        limpiarContraseña();
        if (btnDescargar) btnDescargar.disabled = true;
    }
    
    // ===========================================
    // MOSTRAR/OCULTAR PANEL DE LOCALIDAD
    // ===========================================
    function toggleLocalidadPanel() {
        if (opcionDomicilio && opcionDomicilio.checked && panelLocalidad) {
            panelLocalidad.style.display = 'flex';
        } else if (panelLocalidad) {
            panelLocalidad.style.display = 'none';
            if (localidadFiltro) localidadFiltro.value = '';
        }
    }
    
    if (opcionContactos && opcionTelefonos && opcionDomicilio) {
        opcionContactos.addEventListener('change', toggleLocalidadPanel);
        opcionTelefonos.addEventListener('change', toggleLocalidadPanel);
        opcionDomicilio.addEventListener('change', toggleLocalidadPanel);
        toggleLocalidadPanel();
    }
    
    // ===========================================
    // BÚSQUEDA DE ALUMNOS EN TIEMPO REAL (solo si existe el input)
    // ===========================================
    if (busquedaInput) {
        function actualizarResultadosAlumnos(query) {
            if (query.length < 2) {
                if (resultsContainer) resultsContainer.style.display = 'none';
                return;
            }
            
            fetch('{{ route("datos-estadisticas.buscar-alumno") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ busqueda: query })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.alumnos && data.alumnos.length > 0) {
                    resultsContainer.innerHTML = '';
                    resultadosAlumnos = data.alumnos;
                    
                    data.alumnos.forEach((alumno, index) => {
                        const div = document.createElement('div');
                        div.className = 'search-result-item';
                        div.dataset.index = index;
                        div.dataset.id = alumno.id;
                        div.innerHTML = `<strong>${alumno.texto}</strong><br>
                                        <small>Estatus: ${alumno.estatus ? 'Activo' : 'Inactivo'}</small>`;
                        div.addEventListener('click', () => {
                            pendingAlumnoId = alumno.id;
                            busquedaInput.value = alumno.texto;
                            resultsContainer.style.display = 'none';
                            if (alumnoIdHidden) alumnoIdHidden.value = pendingAlumnoId;
                        });
                        resultsContainer.appendChild(div);
                    });
                    resultsContainer.style.display = 'block';
                    selectedAlumnoIndex = -1;
                } else {
                    resultsContainer.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error en búsqueda de alumnos:', error);
                resultsContainer.style.display = 'none';
            });
        }
        
        busquedaInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(busquedaTimeout);
            
            if (query.length < 2) {
                if (resultsContainer) resultsContainer.style.display = 'none';
                return;
            }
            
            busquedaTimeout = setTimeout(() => actualizarResultadosAlumnos(query), 300);
        });
        
        // Navegación con teclado
        busquedaInput.addEventListener('keydown', function(e) {
            if (!resultsContainer) return;
            const items = resultsContainer.querySelectorAll('.search-result-item');
            if (resultsContainer.style.display !== 'block' || items.length === 0) return;
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedAlumnoIndex = (selectedAlumnoIndex + 1) % items.length;
                updateHighlight(items, selectedAlumnoIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedAlumnoIndex = (selectedAlumnoIndex - 1 + items.length) % items.length;
                updateHighlight(items, selectedAlumnoIndex);
            } else if (e.key === 'Enter' && selectedAlumnoIndex >= 0) {
                e.preventDefault();
                const alumno = resultadosAlumnos[selectedAlumnoIndex];
                if (alumno) {
                    pendingAlumnoId = alumno.id;
                    busquedaInput.value = alumno.texto;
                    if (alumnoIdHidden) alumnoIdHidden.value = pendingAlumnoId;
                    resultsContainer.style.display = 'none';
                }
            }
        });
    }
    
    // ===========================================
    // BÚSQUEDA DE LOCALIDADES
    // ===========================================
    if (localidadFiltro && localidadResultsContainer) {
        localidadFiltro.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(localidadTimeout);
            
            if (query.length < 2) {
                localidadResultsContainer.style.display = 'none';
                return;
            }
            
            localidadTimeout = setTimeout(() => {
                fetch('{{ route("datos-estadisticas.buscar-localidades") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ busqueda: query })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.localidades && data.localidades.length > 0) {
                        localidadResultsContainer.innerHTML = '';
                        resultadosLocalidades = data.localidades;
                        
                        data.localidades.forEach((localidad, index) => {
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.dataset.index = index;
                            div.innerHTML = `<strong>${localidad}</strong>`;
                            div.addEventListener('click', () => {
                                localidadFiltro.value = localidad;
                                localidadResultsContainer.style.display = 'none';
                            });
                            localidadResultsContainer.appendChild(div);
                        });
                        localidadResultsContainer.style.display = 'block';
                        selectedLocalidadIndex = -1;
                    } else {
                        localidadResultsContainer.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error en búsqueda de localidades:', error);
                    localidadResultsContainer.style.display = 'none';
                });
            }, 300);
        });
        
        // Navegación con teclado para localidades
        localidadFiltro.addEventListener('keydown', function(e) {
            const items = localidadResultsContainer.querySelectorAll('.search-result-item');
            if (localidadResultsContainer.style.display !== 'block' || items.length === 0) return;
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedLocalidadIndex = (selectedLocalidadIndex + 1) % items.length;
                updateHighlight(items, selectedLocalidadIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedLocalidadIndex = (selectedLocalidadIndex - 1 + items.length) % items.length;
                updateHighlight(items, selectedLocalidadIndex);
            } else if (e.key === 'Enter' && selectedLocalidadIndex >= 0) {
                e.preventDefault();
                localidadFiltro.value = resultadosLocalidades[selectedLocalidadIndex];
                localidadResultsContainer.style.display = 'none';
            }
        });
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
    
    // ===========================================
    // BOTÓN BUSCAR (Individual) - solo si existe
    // ===========================================
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function() {
            if (pendingAlumnoId) {
                resetearModoFiltros();
                modoIndividualActivo = true;
                if (btnDescargar) btnDescargar.disabled = false;
                if (btnLimpiarBusqueda) btnLimpiarBusqueda.style.display = 'inline-flex';
                alert('Alumno encontrado. Puede proceder a descargar la ficha individual.');
            } else if (busquedaInput && busquedaInput.value.trim()) {
                alert('No se encontró ningún alumno con esos datos.');
                if (btnDescargar) btnDescargar.disabled = true;
            } else {
                alert('Ingrese los datos del alumno a buscar.');
            }
        });
    }
    
    // ===========================================
    // BOTÓN LIMPIAR BÚSQUEDA
    // ===========================================
    if (btnLimpiarBusqueda) {
        btnLimpiarBusqueda.addEventListener('click', function() {
            resetearBusquedaIndividual();
            resetearModoFiltros();
            limpiarContraseña();
            if (btnDescargar) btnDescargar.disabled = true;
        });
    }
    
    // ===========================================
    // APLICAR FILTROS
    // ===========================================
    if (btnAplicarFiltros) {
        btnAplicarFiltros.addEventListener('click', function() {
            if (pendingAlumnoId) {
                alert('Hay una búsqueda individual activa. Use el botón "Limpiar" para cancelarla y luego aplique filtros.');
                return;
            }
            
            const filtros = {
                semestre: semestreSelect ? semestreSelect.value : '',
                grupo_letra: grupoLetraSelect ? grupoLetraSelect.value : '',
                carrera: carreraSelect ? carreraSelect.value : '',
                opcion_prioritaria: document.querySelector('input[name="opcion_prioritaria"]:checked')?.value || 'contactos',
                localidad: localidadFiltro ? localidadFiltro.value : null
            };
            
            btnAplicarFiltros.disabled = true;
            btnAplicarFiltros.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Aplicando...';
            
            fetch('{{ route("datos-estadisticas.aplicar-filtros") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(filtros)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en el servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    totalAlumnosEncontrados = data.total;
                    if (totalAlumnosSpan) totalAlumnosSpan.textContent = totalAlumnosEncontrados;
                    filtrosActuales = filtros;
                    modoIndividualActivo = false;
                    if (btnDescargar) btnDescargar.disabled = false;
                    alert(`Se encontraron ${totalAlumnosEncontrados} alumno(s) con los criterios seleccionados.`);
                } else {
                    alert('Error: ' + (data.message || 'Error desconocido'));
                    if (btnDescargar) btnDescargar.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al aplicar los filtros. Verifique que el servidor esté funcionando.');
                if (btnDescargar) btnDescargar.disabled = true;
            })
            .finally(() => {
                btnAplicarFiltros.disabled = false;
                btnAplicarFiltros.innerHTML = '<i class="fas fa-play me-2"></i>Aplicar Filtros y Opciones';
            });
        });
    }
    
    // ===========================================
    // BOTÓN CANCELAR FILTROS
    // ===========================================
    if (btnCancelarFiltros) {
        btnCancelarFiltros.addEventListener('click', function() {
            resetearModoFiltros();
            limpiarContraseña();
            alert('Filtros cancelados. Los valores han sido restablecidos.');
        });
    }
    
    // ===========================================
    // DESCARGAR PDF
    // ===========================================
    if (btnDescargar) {
        btnDescargar.addEventListener('click', function() {
            const password = passwordInput ? passwordInput.value : '';
            if (!password) {
                alert('Ingrese su contraseña para generar el PDF.');
                return;
            }
            
            btnDescargar.disabled = true;
            btnDescargar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';
            
            let url, body;
            
            if (modoIndividualActivo && pendingAlumnoId) {
                url = '{{ route("datos-estadisticas.generar-pdf") }}';
                body = {
                    current_password: password,
                    alumno_id: pendingAlumnoId
                };
            } else if (filtrosActuales) {
                url = '{{ route("datos-estadisticas.generar-listado") }}';
                body = {
                    current_password: password,
                    filtros: filtrosActuales
                };
            } else {
                alert('No hay datos para generar el PDF. Primero busque un alumno o aplique filtros.');
                btnDescargar.disabled = false;
                btnDescargar.innerHTML = '<i class="fas fa-download me-2"></i>Descargar PDF';
                return;
            }
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(body)
            })
            .then(response => {
                if (response.status === 200) {
                    return response.blob();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error en el servidor');
                    });
                }
            })
            .then(blob => {
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = modoIndividualActivo 
                    ? `ficha_alumno_${pendingAlumnoId}.pdf` 
                    : `listado_alumnos_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(downloadUrl);
                alert('PDF descargado correctamente');
                resetearTodo();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el PDF: ' + error.message);
            })
            .finally(() => {
                btnDescargar.disabled = false;
                btnDescargar.innerHTML = '<i class="fas fa-download me-2"></i>Descargar PDF';
            });
        });
    }
    
    // ===========================================
    // OCULTAR RESULTADOS AL HACER CLIC FUERA
    // ===========================================
    document.addEventListener('click', function(e) {
        if (busquedaInput && resultsContainer && !busquedaInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.style.display = 'none';
        }
        if (localidadFiltro && localidadResultsContainer && !localidadFiltro.contains(e.target) && !localidadResultsContainer.contains(e.target)) {
            localidadResultsContainer.style.display = 'none';
        }
    });
    
    // Estado inicial
    if (btnDescargar) btnDescargar.disabled = true;
});
</script>
@endpush