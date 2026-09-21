@extends('layouts.app')

@section('title', 'Solicitud de Reserva de Horas - SADHCC')

@php
    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Solicitud de Reserva de Horas',
            'url' => null,
            'icon' => 'fa-solid fa-pen-to-square'
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
        transition: opacity 0.2s;
    }
    .badge-item .remove-badge:hover {
        opacity: 1;
    }
    .form-row {
        background-color: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 1rem;
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
</style>
@endpush

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    @include('partials.breadcrumb')
    
    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="border-bottom pb-3">
                <i class="fa-solid fa-pen-to-square text-warning me-2"></i>
                SOLICITUD DE RESERVA DE HORAS
            </h2>
        </div>
    </div>

    <!-- Formulario -->
    <div class="module-section">
        <form id="solicitudForm">
            @csrf
            
            <!-- Materia con autocomplete -->
            <div class="row mb-3">
                <div class="col-md-6 position-relative">
                    <label class="form-label">Materia:</label>
                    <input type="text" 
                           id="materia"
                           name="materia"
                           class="form-control"
                           placeholder="Ej: Submódulo I, Submódulo II, etc."
                           maxlength="50"
                           autocomplete="off">
                    <div id="materiaResults" class="search-results-dropdown"></div>
                    <small class="text-muted">Comience a escribir para buscar materias existentes</small>
                </div>
            </div>

            <!-- Grupo con autocomplete -->
            <div class="row mb-3">
                <div class="col-md-6 position-relative">
                    <label class="form-label">Grupo:</label>
                    <input type="text" 
                           id="buscar_grupo"
                           class="form-control" 
                           placeholder="Semestre, grupo o carrera..."
                           autocomplete="off">
                    <input type="hidden" id="grupo_id" name="grupo_id" value="">
                    <div id="grupoResults" class="search-results-dropdown"></div>
                    <small class="text-muted">Comience a escribir para buscar grupos existentes</small>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div id="grupoSeleccionado" style="display:none;" class="w-100">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Grupo seleccionado: <strong id="grupoNombreDisplay"></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fechas y horas -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Fecha de inicio:</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha de fin:</label>
                    <input type="date" class="form-control" id="fecha_final" name="fecha_final">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Horas requeridas:</label>
                    <input type="number" 
                           id="horas_requeridas"
                           name="horas_requeridas"
                           class="form-control"
                           min="0"
                           max="168"
                           step="1"
                           placeholder="0"
                           autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Número de actividad:</label>
                    <input type="number" 
                           id="numero_actividad"
                           name="numero_actividad"
                           class="form-control" 
                           min="0"
                           step="1"
                           placeholder="0"
                           autocomplete="off">
                </div>
            </div>

            <!-- Competencia y atributo -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Competencia o tema a desarrollar:</label>
                    <textarea 
                           id="competencia"
                           name="competencia"
                           class="form-control"
                           placeholder="Describa los temas que se tratarán durante la(s) sesión(es)."
                           maxlength="255"
                           rows="3"
                           autocomplete="off"></textarea>
                    <small class="text-muted"><span id="compCounter">0</span>/255</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Atributo:</label>
                    <textarea 
                           id="atributo"
                           name="atributo"
                           class="form-control"
                           placeholder="Describa los atributos de la actividad."
                           maxlength="255"
                           rows="3"
                           autocomplete="off"></textarea>
                    <small class="text-muted"><span id="attrCounter">0</span>/255</small>
                </div>
            </div>

            <!-- Materiales -->
            <div class="row mb-3">
                <div class="col-md-5 position-relative">
                    <label class="form-label">Materiales requeridos:</label>
                    <div class="input-group">
                        <input type="text" 
                               id="material_input"
                               class="form-control" 
                               placeholder="Ej: Guantes de latex, etc."
                               autocomplete="off">
                        <button type="button" class="btn btn-success" id="btnAgregarMaterial">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <small class="text-muted">Escriba un material y presione "+"</small>
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
                <div class="col-md-5 position-relative">
                    <label class="form-label">Herramientas requeridas:</label>
                    <div class="input-group">
                        <input type="text" 
                               id="herramienta_input"
                               class="form-control" 
                               placeholder="Ej: Instalador de Office, etc."
                               autocomplete="off">
                        <button type="button" class="btn btn-danger" id="btnAgregarHerramienta">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <small class="text-muted">Escriba una herramienta y presione "+"</small>
                </div>
                <div class="col-md-7">
                    <label class="form-label">Herramientas añadidas:</label>
                    <div id="herramientasContainer" class="d-flex flex-wrap gap-2 p-3 bg-light rounded" style="min-height: 60px;">
                        <span class="text-muted">No hay herramientas añadidas</span>
                    </div>
                </div>
            </div>

            <!-- Notas extras -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Notas adicionales:</label>
                    <textarea 
                           id="notas"
                           name="notas"
                           class="form-control"
                           placeholder="Agregue cualquier información adicional relevante..."
                           maxlength="255"
                           rows="2"
                           autocomplete="off"></textarea>
                    <small class="text-muted"><span id="notasCounter">0</span>/255</small>
                </div>
            </div>

            <!-- Botones -->
            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Contraseña de Usuario *
                    </label>
                    <input type="password" 
                           class="form-control" 
                           id="current_password"
                           name="current_password"
                           placeholder="Ingrese su contraseña para autorizar"
                           autocomplete="new-password"
                           required>
                    <small class="text-muted">Requerida para confirmar la solicitud</small>
                </div>
                <div class="col-md-8 d-flex align-items-end justify-content-end">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-warning" id="btnEnviarSolicitud">
                            <i class="fa-solid fa-paper-plane me-2"></i>Enviar Solicitud
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCancelar">
                            <i class="fas fa-times me-2"></i>Cancelar Solicitud
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== VARIABLES GLOBALES =====
    let materiales = [];
    let herramientas = [];
    let selectedGrupo = null;
    let timeoutId = null;
    let materiaTimeoutId = null;
    let grupoTimeoutId = null;
    
    // ===== ELEMENTOS DEL DOM =====
    const materiaInput = document.getElementById('materia');
    const materiaResults = document.getElementById('materiaResults');
    const buscarGrupo = document.getElementById('buscar_grupo');
    const grupoResults = document.getElementById('grupoResults');
    const grupoId = document.getElementById('grupo_id');
    const grupoSeleccionado = document.getElementById('grupoSeleccionado');
    const grupoNombreDisplay = document.getElementById('grupoNombreDisplay');
    const materialInput = document.getElementById('material_input');
    const herramientaInput = document.getElementById('herramienta_input');
    const materialesContainer = document.getElementById('materialesContainer');
    const herramientasContainer = document.getElementById('herramientasContainer');
    const btnAgregarMaterial = document.getElementById('btnAgregarMaterial');
    const btnAgregarHerramienta = document.getElementById('btnAgregarHerramienta');
    const btnEnviar = document.getElementById('btnEnviarSolicitud');
    const btnCancelar = document.getElementById('btnCancelar');
    const currentPassword = document.getElementById('current_password');

    // ===== AUTOCOMPLETE MATERIAS =====
    materiaInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(materiaTimeoutId);
        
        if (query.length < 2) {
            materiaResults.style.display = 'none';
            return;
        }
        
        materiaTimeoutId = setTimeout(() => {
            fetch(`{{ route('solicitud.buscar.materias') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        materiaResults.innerHTML = data.map(item => 
                            `<div class="search-result-item" data-value="${item}">${item}</div>`
                        ).join('');
                        materiaResults.style.display = 'block';
                    } else {
                        materiaResults.innerHTML = '<div class="search-result-item text-muted">No se encontraron materias</div>';
                        materiaResults.style.display = 'block';
                    }
                });
        }, 300);
    });

    materiaResults.addEventListener('click', function(e) {
        const item = e.target.closest('.search-result-item');
        if (item && item.dataset.value) {
            materiaInput.value = item.dataset.value;
            materiaResults.style.display = 'none';
        }
    });

    // ===== AUTOCOMPLETE GRUPOS =====
    buscarGrupo.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(grupoTimeoutId);
        
        if (query.length < 2) {
            grupoResults.style.display = 'none';
            return;
        }
        
        grupoTimeoutId = setTimeout(() => {
            fetch(`{{ route('solicitud.buscar.grupos') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
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
        }, 300);
    });

    grupoResults.addEventListener('click', function(e) {
        const item = e.target.closest('.search-result-item');
        if (item && item.dataset.id) {
            seleccionarGrupo(item.dataset.id, item.dataset.nombre);
        }
    });

    function seleccionarGrupo(id, nombre) {
        selectedGrupo = { id, nombre };
        grupoId.value = id;
        buscarGrupo.value = nombre;
        grupoNombreDisplay.textContent = nombre;
        grupoSeleccionado.style.display = 'block';
        grupoResults.style.display = 'none';
    }

    // ===== MATERIALES =====
    btnAgregarMaterial.addEventListener('click', function() {
        const nombre = materialInput.value.trim();
        if (!nombre) {
            alert('Por favor, escriba un material.');
            return;
        }
        
        if (materiales.includes(nombre)) {
            alert('Este material ya fue agregado.');
            return;
        }
        
        materiales.push(nombre);
        renderMateriales();
        materialInput.value = '';
        materialInput.focus();
    });

    function renderMateriales() {
        if (materiales.length === 0) {
            materialesContainer.innerHTML = '<span class="text-muted">No hay materiales añadidos</span>';
            return;
        }
        
        materialesContainer.innerHTML = materiales.map((item, index) => 
            `<span class="badge-item badge-material">
                ${item}
                <span class="remove-badge" data-index="${index}" data-tipo="material">✕</span>
            </span>`
        ).join('');
    }

    // ===== HERRAMIENTAS =====
    btnAgregarHerramienta.addEventListener('click', function() {
        const nombre = herramientaInput.value.trim();
        if (!nombre) {
            alert('Por favor, escriba una herramienta.');
            return;
        }
        
        if (herramientas.includes(nombre)) {
            alert('Esta herramienta ya fue agregada.');
            return;
        }
        
        herramientas.push(nombre);
        renderHerramientas();
        herramientaInput.value = '';
        herramientaInput.focus();
    });

    function renderHerramientas() {
        if (herramientas.length === 0) {
            herramientasContainer.innerHTML = '<span class="text-muted">No hay herramientas añadidas</span>';
            return;
        }
        
        herramientasContainer.innerHTML = herramientas.map((item, index) => 
            `<span class="badge-item badge-herramienta">
                ${item}
                <span class="remove-badge" data-index="${index}" data-tipo="herramienta">✕</span>
            </span>`
        ).join('');
    }

    // ===== ELIMINAR BADGES (Delegación de eventos) =====
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.remove-badge');
        if (!target) return;
        
        const index = parseInt(target.dataset.index);
        const tipo = target.dataset.tipo;
        
        if (tipo === 'material') {
            materiales.splice(index, 1);
            renderMateriales();
        } else if (tipo === 'herramienta') {
            herramientas.splice(index, 1);
            renderHerramientas();
        }
    });

    // ===== CONTADORES DE CARACTERES =====
    function actualizarContador(input, counterId) {
        document.getElementById(counterId).textContent = input.value.length;
    }

    document.getElementById('competencia').addEventListener('input', function() {
        actualizarContador(this, 'compCounter');
    });
    
    document.getElementById('atributo').addEventListener('input', function() {
        actualizarContador(this, 'attrCounter');
    });
    
    document.getElementById('notas').addEventListener('input', function() {
        actualizarContador(this, 'notasCounter');
    });

    // ===== ENTER PARA AGREGAR =====
    materialInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnAgregarMaterial.click();
        }
    });

    herramientaInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnAgregarHerramienta.click();
        }
    });

    // ===== ENVIAR SOLICITUD =====
    btnEnviar.addEventListener('click', function() {
        if (!currentPassword.value) {
            alert('⚠️ Debe ingresar su contraseña para autorizar la solicitud.');
            currentPassword.focus();
            return;
        }

        const data = {
            current_password: currentPassword.value,
            materia: document.getElementById('materia').value || null,
            grupo_id: grupoId.value || null,
            fecha_inicio: document.getElementById('fecha_inicio').value || null,
            fecha_final: document.getElementById('fecha_final').value || null,
            horas_requeridas: document.getElementById('horas_requeridas').value || null,
            numero_actividad: document.getElementById('numero_actividad').value || null,
            competencia: document.getElementById('competencia').value || null,
            atributo: document.getElementById('atributo').value || null,
            materiales: materiales.length > 0 ? materiales : null,
            herramientas: herramientas.length > 0 ? herramientas : null,
            notas: document.getElementById('notas').value || null
        };

        btnEnviar.disabled = true;
        const originalText = btnEnviar.innerHTML;
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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                limpiarFormulario();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error de conexión al enviar la solicitud.');
        })
        .finally(() => {
            btnEnviar.disabled = false;
            btnEnviar.innerHTML = originalText;
        });
    });

    // ===== LIMPIAR FORMULARIO =====
    function limpiarFormulario() {
        document.getElementById('solicitudForm').reset();
        materiales = [];
        herramientas = [];
        selectedGrupo = null;
        grupoId.value = '';
        grupoSeleccionado.style.display = 'none';
        renderMateriales();
        renderHerramientas();
        currentPassword.value = '';
        document.getElementById('compCounter').textContent = '0';
        document.getElementById('attrCounter').textContent = '0';
        document.getElementById('notasCounter').textContent = '0';
        materiaResults.style.display = 'none';
        grupoResults.style.display = 'none';
        // Resetear contadores
        document.querySelectorAll('textarea').forEach(textarea => {
            const counterId = textarea.id + 'Counter';
            const counter = document.getElementById(counterId);
            if (counter) counter.textContent = '0';
        });
    }

    btnCancelar.addEventListener('click', function() {
        if (confirm('¿Está seguro de cancelar la solicitud? Se perderán todos los datos ingresados.')) {
            limpiarFormulario();
        }
    });

    // ===== OCULTAR RESULTADOS AL HACER CLIC FUERA =====
    document.addEventListener('click', function(e) {
        if (!materiaInput.contains(e.target) && !materiaResults.contains(e.target)) {
            materiaResults.style.display = 'none';
        }
        if (!buscarGrupo.contains(e.target) && !grupoResults.contains(e.target)) {
            grupoResults.style.display = 'none';
        }
    });

    // ===== KEYBOARD NAVIGATION =====
    let selectedIndex = -1;

    materiaInput.addEventListener('keydown', function(e) {
        const items = materiaResults.querySelectorAll('.search-result-item');
        if (items.length === 0) return;
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            highlightItem(items, selectedIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            highlightItem(items, selectedIndex);
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            const item = items[selectedIndex];
            if (item && item.dataset.value) {
                materiaInput.value = item.dataset.value;
                materiaResults.style.display = 'none';
                selectedIndex = -1;
            }
        }
    });

    buscarGrupo.addEventListener('keydown', function(e) {
        const items = grupoResults.querySelectorAll('.search-result-item');
        if (items.length === 0) return;
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            highlightItem(items, selectedIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            highlightItem(items, selectedIndex);
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            const item = items[selectedIndex];
            if (item && item.dataset.id) {
                seleccionarGrupo(item.dataset.id, item.dataset.nombre);
                selectedIndex = -1;
            }
        }
    });

    function highlightItem(items, index) {
        items.forEach((item, i) => {
            item.classList.toggle('highlighted', i === index);
        });
    }
});
</script>
@endpush