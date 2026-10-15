@extends('layouts.app')

@section('title', 'Solicitud de Reserva de Horas - SADHCC')

@php
    $breadcrumbs = [
        ['name' => 'Inicio', 'url' => route('welcome'), 'icon' => 'fa-home'],
        ['name' => 'Solicitud de Reserva de Horas', 'url' => null, 'icon' => 'fa-solid fa-pen-to-square']
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
    .form-row {
        background-color: white;
        padding: 1.25rem;
        border-radius: 10px;
        margin-bottom: 1rem;
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
    .badge-item {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 20px;
        gap: 0.5rem;
    }
    .badge-item .remove-badge {
        cursor: pointer;
        opacity: 0.7;
    }
    .badge-item .remove-badge:hover {
        opacity: 1;
    }
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }
    .counter-badge {
        font-size: 0.8rem;
        margin-left: 5px;
        color: #6c757d;
    }
    .badge-material {
        background-color: #198754;
        color: white;
    }
    .badge-herramienta {
        background-color: #dc3545;
        color: white;
    }
    .hora-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: background-color 0.2s;
    }
    .hora-item:hover {
        background-color: #f8f9fa;
    }
    .hora-item.selected {
        background-color: #d1e7dd;
        border-color: #198754;
    }
    .hora-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
    }
    .hora-info {
        flex: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }
    .horas-list-container {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        background: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    @include('partials.breadcrumb')
    
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="border-bottom pb-3">
                <i class="fa-solid fa-pen-to-square text-warning me-2"></i>
                SOLICITUD DE RESERVA DE HORAS
            </h2>
        </div>
    </div>

    <form id="solicitudForm">
        @csrf

        <!-- ============ SECCIÓN 1: BÚSQUEDA DE HORAS ============ -->
        <div class="module-section">
            <h5 class="mb-4"><i class="fas fa-search me-2"></i> BUSCAR HORAS DISPONIBLES</h5>

            <div class="form-row">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Laboratorio *</label>
                        <select id="laboratorio" class="form-select">
                            <option value="">—Seleccione—</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Materia *</label>
                        <select id="materia" class="form-select" disabled>
                            <option value="">—Seleccione un laboratorio primero—</option>
                        </select>
                    </div>
                    <div class="col-md-4 position-relative">
                        <label class="form-label">Grupo *</label>
                        <input type="text" 
                            id="buscarGrupo"
                            class="form-control" 
                            placeholder="Semestre, grupo o carrera..."
                            autocomplete="off">
                        <input type="hidden" id="grupo_id" value="">
                        <div id="grupoResults" class="search-results-dropdown"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Fecha de inicio *</label>
                        <input type="date" class="form-control" id="fecha_inicio">
                        <small class="text-muted">A partir de esta fecha se buscarán horas</small>
                    </div>                    
                    <div class="col-md-3">
                        <label class="form-label">Horas requeridas *</label>
                        <input type="number" class="form-control" id="horas_requeridas"
                               min="1" max="168" step="1" placeholder="0">
                        <small class="text-muted">Límite de horas a seleccionar</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-info" id="btnBuscarHoras">
                            <i class="fas fa-search me-2"></i>Buscar horas disponibles
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de horas disponibles -->
            <div id="horasDisponiblesContainer" style="display:none;">
                <div class="row mb-2">
                    <div class="col-12">
                        <span class="badge bg-secondary" id="contadorSeleccionadas">0 seleccionadas</span>
                        <span class="badge bg-info" id="limiteHoras">Límite: 0</span>
                    </div>
                </div>
                <div class="horas-list-container" id="horasListContainer">
                    <p class="text-muted mb-0">Realice una búsqueda para ver resultados</p>
                </div>
            </div>
        </div>

        <!-- ============ SECCIÓN 2: DATOS DE LA PRÁCTICA ============ -->
        <div class="module-section">
            <h5 class="mb-4"><i class="fas fa-clipboard-list me-2"></i> DATOS DE LA PRÁCTICA</h5>

            <div class="form-row">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" maxlength="50"
                               placeholder="Ej: Práctica de ensamblaje"
                               oninput="actualizarContador(this, 'nombreCounter')">
                        <small class="counter-badge"><span id="nombreCounter">0</span>/50</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Número de actividad:</label>
                        <input type="number" class="form-control" id="no_actividad" min="0" step="1" placeholder="0">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Competencia o tema a desarrollar:</label>
                        <textarea id="competencia" class="form-control" maxlength="255" rows="3"
                                  placeholder="Describa los temas a tratar..."
                                  oninput="actualizarContador(this, 'compCounter')"></textarea>
                        <small class="counter-badge"><span id="compCounter">0</span>/255</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Atributo:</label>
                        <textarea id="atributo" class="form-control" maxlength="255" rows="3"
                                  placeholder="Describa los atributos..."
                                  oninput="actualizarContador(this, 'attrCounter')"></textarea>
                        <small class="counter-badge"><span id="attrCounter">0</span>/255</small>
                    </div>
                </div>

                <!-- Materiales -->
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label class="form-label">Materiales requeridos:</label>
                        <div class="input-group">
                            <input type="text" id="material_input" class="form-control"
                                   placeholder="Ej: Guantes de latex"
                                   onkeypress="if(event.key==='Enter'){event.preventDefault();agregarMaterial();}">
                            <button type="button" class="btn btn-success" onclick="agregarMaterial()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Materiales añadidos:</label>
                        <div id="materialesContainer" class="d-flex flex-wrap gap-2 p-3 bg-light rounded" style="min-height: 60px;">
                            <span class="text-muted">No hay materiales añadidos</span>
                        </div>
                    </div>
                </div>

                <!-- Herramientas -->
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label class="form-label">Herramientas requeridas:</label>
                        <div class="input-group">
                            <input type="text" id="herramienta_input" class="form-control"
                                   placeholder="Ej: Instalador de Office"
                                   onkeypress="if(event.key==='Enter'){event.preventDefault();agregarHerramienta();}">
                            <button type="button" class="btn btn-danger" onclick="agregarHerramienta()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Herramientas añadidas:</label>
                        <div id="herramientasContainer" class="d-flex flex-wrap gap-2 p-3 bg-light rounded" style="min-height: 60px;">
                            <span class="text-muted">No hay herramientas añadidas</span>
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Notas adicionales:</label>
                        <textarea id="notas" class="form-control" maxlength="255" rows="2"
                                  placeholder="Información adicional..."
                                  oninput="actualizarContador(this, 'notasCounter')"></textarea>
                        <small class="counter-badge"><span id="notasCounter">0</span>/255</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ SECCIÓN 3: CONTRASEÑA Y BOTONES ============ -->
        <div class="module-section">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Contraseña de Usuario *
                    </label>
                    <input type="password" class="form-control" id="current_password"
                           placeholder="Ingrese su contraseña"
                           autocomplete="new-password" required>
                    <small class="text-muted">Requerida para confirmar la solicitud</small>
                </div>
                <div class="col-md-8 d-flex align-items-end justify-content-end">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-warning" id="btnEnviarSolicitud">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCancelar">
                            <i class="fas fa-times me-2"></i>Cancelar Solicitud
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// ===== VARIABLES GLOBALES =====
let grupoTimeoutId = null;
let materiales = [];
let herramientas = [];
let horasDisponibles = [];
let horasSeleccionadas = [];
let limiteHoras = 0;

// ===== FUNCIONES GLOBALES =====
function actualizarContador(input, counterId) {
    const counter = document.getElementById(counterId);
    if (counter) counter.textContent = input.value.length;
}

function agregarMaterial() {
    const input = document.getElementById('material_input');
    const nombre = input.value.trim();
    if (!nombre) { alert('Escriba un material.'); return; }
    if (materiales.includes(nombre)) { alert('Este material ya fue agregado.'); return; }
    materiales.push(nombre);
    renderMateriales();
    input.value = '';
    input.focus();
}

function renderMateriales() {
    const cont = document.getElementById('materialesContainer');
    if (materiales.length === 0) {
        cont.innerHTML = '<span class="text-muted">No hay materiales añadidos</span>';
        return;
    }
    cont.innerHTML = materiales.map((item, i) =>
        `<span class="badge-item badge-material">
            ${item}
            <span class="remove-badge" onclick="eliminarMaterial(${i})">✕</span>
        </span>`
    ).join('');
}

function eliminarMaterial(i) {
    materiales.splice(i, 1);
    renderMateriales();
}

function agregarHerramienta() {
    const input = document.getElementById('herramienta_input');
    const nombre = input.value.trim();
    if (!nombre) { alert('Escriba una herramienta.'); return; }
    if (herramientas.includes(nombre)) { alert('Esta herramienta ya fue agregada.'); return; }
    herramientas.push(nombre);
    renderHerramientas();
    input.value = '';
    input.focus();
}

function renderHerramientas() {
    const cont = document.getElementById('herramientasContainer');
    if (herramientas.length === 0) {
        cont.innerHTML = '<span class="text-muted">No hay herramientas añadidas</span>';
        return;
    }
    cont.innerHTML = herramientas.map((item, i) =>
        `<span class="badge-item badge-herramienta">
            ${item}
            <span class="remove-badge" onclick="eliminarHerramienta(${i})">✕</span>
        </span>`
    ).join('');
}

function eliminarHerramienta(i) {
    herramientas.splice(i, 1);
    renderHerramientas();
}

document.addEventListener('DOMContentLoaded', function() {

    // ===== ELEMENTOS =====
    const laboratorioSelect = document.getElementById('laboratorio');
    const materiaSelect = document.getElementById('materia');
    const buscarGrupo = document.getElementById('buscarGrupo');
    const grupoId = document.getElementById('grupo_id');
    const grupoResults = document.getElementById('grupoResults');
    const fechaInicio = document.getElementById('fecha_inicio');
    const horasRequeridas = document.getElementById('horas_requeridas');
    const btnBuscarHoras = document.getElementById('btnBuscarHoras');
    const horasDisponiblesContainer = document.getElementById('horasDisponiblesContainer');
    const horasListContainer = document.getElementById('horasListContainer');
    const contadorSeleccionadas = document.getElementById('contadorSeleccionadas');
    const limiteHorasBadge = document.getElementById('limiteHoras');
    const btnEnviar = document.getElementById('btnEnviarSolicitud');
    const btnCancelar = document.getElementById('btnCancelar');
    const currentPassword = document.getElementById('current_password');

    // ===== CARGAR LABORATORIOS =====
    function cargarLaboratorios() {
        fetch('{{ route("solicitud.laboratorios.listar") }}', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                laboratorioSelect.innerHTML = '<option value="">—Seleccione—</option>';
                data.laboratorios.forEach(l => {
                    const opt = document.createElement('option');
                    opt.value = l.id;
                    opt.textContent = l.nombre;
                    laboratorioSelect.appendChild(opt);
                });
            }
        });
    }

    // ===== CARGAR MATERIAS POR LABORATORIO =====
    function cargarMaterias(idLaboratorio) {
        const url = new URL('{{ route("solicitud.materias.por.laboratorio") }}', window.location.origin);
        if (idLaboratorio) url.searchParams.append('id_laboratorio', idLaboratorio);

        fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                materiaSelect.innerHTML = '<option value="">—Seleccione—</option>';
                if (data.materias.length === 0) {
                    materiaSelect.innerHTML = '<option value="">—No hay materias—</option>';
                    materiaSelect.disabled = true;
                    return;
                }
                materiaSelect.disabled = false;
                data.materias.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.nombre;
                    materiaSelect.appendChild(opt);
                });
            }
        });
    }

    // ===== FECHA POR DEFECTO =====
    function cargarFechaDefault() {
        fetch('{{ route("solicitud.fecha.default") }}', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) fechaInicio.value = data.fecha;
        });
    }

    // ===== EVENTOS =====
    buscarGrupo.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(grupoTimeoutId);
        
        if (query.length < 1) {
            grupoResults.style.display = 'none';
            return;
        }
        
        grupoTimeoutId = setTimeout(() => {
            fetch(`{{ route('solicitud.buscar.grupos') }}?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.length > 0) {
                    grupoResults.innerHTML = data.map(item => 
                        `<div class="search-result-item" data-id="${item.id}" data-nombre="${item.nombre}">${item.nombre}</div>`
                    ).join('');
                    grupoResults.style.display = 'block';
                } else {
                    grupoResults.innerHTML = '<div class="search-result-item text-muted">No se encontraron grupos</div>';
                    grupoResults.style.display = 'block';
                }
            });
        }, 250);
    });

    grupoResults.addEventListener('click', function(e) {
        const item = e.target.closest('.search-result-item');
        if (item && item.dataset.id) {
            grupoId.value = item.dataset.id;
            buscarGrupo.value = item.dataset.nombre;
            grupoResults.style.display = 'none';
        }
    });

    // Ocultar al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!buscarGrupo.contains(e.target) && !grupoResults.contains(e.target)) {
            grupoResults.style.display = 'none';
        }
    });

    laboratorioSelect.addEventListener('change', function() {
        const idLab = this.value;
        if (idLab) {
            cargarMaterias(idLab);
        } else {
            materiaSelect.innerHTML = '<option value="">—Seleccione un laboratorio primero—</option>';
            materiaSelect.disabled = true;
        }
        // Resetear lista de horas
        horasDisponiblesContainer.style.display = 'none';
        horasDisponibles = [];
        horasSeleccionadas = [];
    });

    // ===== BUSCAR HORAS DISPONIBLES =====
    btnBuscarHoras.addEventListener('click', function() {
        const idLab = laboratorioSelect.value;
        const idMat = materiaSelect.value;
        const idGrupo = grupoId.value;
        const fecha = fechaInicio.value;
        const horas = parseInt(horasRequeridas.value) || 0;

        if (!idLab) { alert('Seleccione un laboratorio.'); return; }
        if (!idMat) { alert('Seleccione una materia.'); return; }
        if (!idGrupo) { alert('Seleccione un grupo.'); return; }
        if (!fecha) { alert('Seleccione una fecha de inicio.'); return; }
        if (horas <= 0) { alert('Ingrese la cantidad de horas requeridas.'); return; }

        limiteHoras = horas;
        limiteHorasBadge.textContent = `Límite: ${horas}`;

        const params = new URLSearchParams({
            id_laboratorio: idLab,
            id_materia: idMat,
            id_grupo: idGrupo,
            fecha_inicio: fecha,
            horas_requeridas: horas
        });

        fetch(`{{ route("solicitud.buscar.horas") }}?${params}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                horasDisponibles = data.horas;
                horasSeleccionadas = [];
                renderHorasDisponibles();
                horasDisponiblesContainer.style.display = 'block';
                actualizarContadorSeleccionadas();

                if (data.horas.length === 0) {
                    horasListContainer.innerHTML = '<p class="text-muted mb-0">No hay horas disponibles con esos criterios.</p>';
                }
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error al buscar horas.'); });
    });

    function renderHorasDisponibles() {
        if (horasDisponibles.length === 0) return;

        horasListContainer.innerHTML = horasDisponibles.map((h, i) => `
            <div class="hora-item" data-id="${h.id_horario}" data-fecha="${h.fecha}" data-index="${i}">
                <input type="checkbox" id="hora_${i}" onchange="toggleHora(${i}, this)">
                <div class="hora-info">
                    <span><strong>${h.fecha_legible}</strong></span>
                    <span class="badge bg-primary">${h.dia_semana}</span>
                    <span class="badge bg-secondary">${h.hora_legible}</span>
                    ${h.hora_fija ? '<span class="badge bg-warning text-dark"><i class="fas fa-layer-group"></i> Fija</span>' : ''}
                    <span><small>${h.grupo_nombre || '—'}</small></span>
                    <span><small>${h.maestro_nombre || '—'}</small></span>
                </div>
            </div>
        `).join('');
    }

    // ===== TOGGLE DE HORA (con restricción) =====
    window.toggleHora = function(index, checkbox) {
        const hora = horasDisponibles[index];
        const item = document.querySelector(`.hora-item[data-index="${index}"]`);

        if (checkbox.checked) {
            // Verificar límite
            if (horasSeleccionadas.length >= limiteHoras) {
                checkbox.checked = false;
                alert(`⚠️ Solo puede seleccionar hasta ${limiteHoras} hora(s).`);
                return;
            }
            horasSeleccionadas.push(hora);
            item.classList.add('selected');
        } else {
            horasSeleccionadas = horasSeleccionadas.filter(h => 
                !(h.id_horario === hora.id_horario && h.fecha === hora.fecha)
            );
            item.classList.remove('selected');
        }
        actualizarContadorSeleccionadas();
    };

    function actualizarContadorSeleccionadas() {
        contadorSeleccionadas.textContent = `${horasSeleccionadas.length} seleccionadas`;
    }

    // ===== ENVIAR SOLICITUD =====
    btnEnviar.addEventListener('click', function() {
        if (horasSeleccionadas.length === 0) {
            alert('⚠️ Debe seleccionar al menos una hora disponible.');
            return;
        }
        if (!currentPassword.value) {
            alert('⚠️ Debe ingresar su contraseña.');
            currentPassword.focus();
            return;
        }

        // Extraer solo los id_horario únicos
        const idHorarios = horasSeleccionadas.map(h => h.id_horario);

        const data = {
            current_password: currentPassword.value,
            reservas: horasSeleccionadas.map(h => ({
                id_horario: h.id_horario,
                fecha: h.fecha
            })),
            nombre: document.getElementById('nombre').value || null,
            no_actividad: document.getElementById('no_actividad').value || null,
            competencia: document.getElementById('competencia').value || null,
            atributo: document.getElementById('atributo').value || null,
            materiales: materiales.length > 0 ? materiales : null,
            herramientas: herramientas.length > 0 ? herramientas : null,
            notas: document.getElementById('notas').value || null,
        };

        btnEnviar.disabled = true;
        const original = btnEnviar.innerHTML;
        btnEnviar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';

        fetch('{{ route("solicitud.store") }}', {
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
                limpiarFormulario();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); })
        .finally(() => {
            btnEnviar.disabled = false;
            btnEnviar.innerHTML = original;
        });
    });

    // ===== LIMPIAR =====
    function limpiarFormulario() {
        document.getElementById('solicitudForm').reset();
        buscarGrupo.value = '';
        grupoId.value = '';
        grupoResults.style.display = 'none';
        materiales = [];
        herramientas = [];
        horasDisponibles = [];
        horasSeleccionadas = [];
        renderMateriales();
        renderHerramientas();
        horasDisponiblesContainer.style.display = 'none';
        horasListContainer.innerHTML = '<p class="text-muted mb-0">Realice una búsqueda para ver resultados</p>';
        actualizarContadorSeleccionadas();
        materiaSelect.disabled = true;
        materiaSelect.innerHTML = '<option value="">—Seleccione un laboratorio primero—</option>';
        cargarFechaDefault();
        // Resetear contadores
        ['nombreCounter', 'compCounter', 'attrCounter', 'notasCounter'].forEach(id => {
            document.getElementById(id).textContent = '0';
        });
    }

    btnCancelar.addEventListener('click', function() {
        if (confirm('¿Cancelar la solicitud? Se perderán todos los datos.')) {
            limpiarFormulario();
        }
    });

    // ===== INICIALIZAR =====
    cargarLaboratorios();
    cargarFechaDefault();
});
</script>
@endpush