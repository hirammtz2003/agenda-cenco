@extends('layouts.app')

@section('title', 'Configuración de Becas - SGGDI')

@php
    use App\Helpers\PrivilegiosHelper;
    $usuarioActual = Auth::user();
    $puedeEditar = $puedeEditar ?? false; // Viene del controlador
    $puedeConsultar = $puedeConsultar ?? false;

    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Configuración de Becas',
            'url' => null,
            'icon' => 'fa-hand-holding-dollar'
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
    .action-buttons {
        white-space: nowrap;
    }
    .form-row {
        background-color: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 1rem;
    }
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    .counter-badge {
        font-size: 0.8rem;
        margin-left: 5px;
        color: #6c757d;
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
                <i class="fa-solid fa-hand-holding-dollar text-warning me-2"></i>
                CONFIGURACIÓN DE BECAS
            </h2>
        </div>
    </div>
    
    @if(!$puedeConsultar)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> No tienes permisos para acceder a este módulo.
        </div>
    @else

    @if($puedeEditar)
    <!-- Formulario de registro/edición -->
    <div class="module-section">
        <h5 class="mb-4"><i class="fa-solid fa-pen-to-square"></i> REGISTRO Y EDICIÓN DE BECAS</h5>
        
        <div class="form-row" id="formBeca">
            <input type="hidden" id="becaId" value="">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tipo de Beca *</label>
                    <input type="text" 
                           id="tipo_beca"
                           class="form-control"
                           placeholder="Ej: Beca Benito Juárez, Beca de Transporte, etc."
                           maxlength="20"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')"
                           oninput="actualizarContador(this, 'tipoCounter')">
                    <small class="text-muted"><span id="tipoCounter">0</span>/20</small>
                </div>
            </div>
            
            <div class="row mb-3">                
                <div class="col-md-8">
                    <label class="form-label">Descripción de la beca <small class="text-muted">(opcional)</small></label>
                    <textarea 
                           id="descripcion"
                           class="form-control"
                           placeholder="Describa brevemente el programa de beca, requisitos o detalles importantes."
                           maxlength="255"
                           rows="4"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')"
                           oninput="actualizarContador(this, 'descCounter')"></textarea>
                    <small class="text-muted"><span id="descCounter">0</span>/255</small>
                </div>
            </div>

            <!-- Contraseña y botones -->
            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Contraseña de Usuario *
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
                        <button type="button" class="btn btn-warning" id="btnGuardarBeca">
                            <i class="fas fa-save me-2"></i>Guardar Beca
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCancelarEdicion" style="display:none;">
                            <i class="fas fa-times me-2"></i>Cancelar Edición
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormulario()">
                            <i class="fas fa-undo me-2"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtros y búsqueda -->
    <div class="module-section">
        <h5 class="mb-4"><i class="fas fa-search"></i> CONSULTA DE BECAS</h5>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Buscar:</label>
                <div class="input-group">
                    <input type="text" id="busquedaGeneral" class="form-control" 
                           placeholder="Buscar por tipo de beca o descripción..."
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

        <!-- Resultados de la búsqueda -->
        <div class="row mb-3">
            <div class="col-12">
                <span class="filter-badge" id="totalResultados">
                    <i class="fas fa-list"></i> 0 beca(s)
                </span>
                <span class="filter-badge" id="filtrosActivos" style="display:none;"></span>
            </div>
        </div>

        <!-- Tabla de becas -->
        <div class="table-container">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Tipo de Beca</th>
                        <th>Descripción</th>
                        @if($puedeEditar)
                            <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="tablaBecas">
                    <tr>
                        <td colspan="{{ $puedeEditar ? 3 : 2 }}" class="text-center py-4">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Realice una búsqueda para ver resultados</h5>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Función para actualizar contadores
function actualizarContador(input, counterId) {
    document.getElementById(counterId).textContent = input.value.length;
}

document.addEventListener('DOMContentLoaded', function() {
    // ===========================================
    // ELEMENTOS COMUNES (disponibles para todos)
    // ===========================================
    const busquedaGeneral = document.getElementById('busquedaGeneral');
    const btnBuscar = document.getElementById('btnBuscar');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    const tablaBecas = document.getElementById('tablaBecas');
    const totalResultados = document.getElementById('totalResultados');
    const filtrosActivos = document.getElementById('filtrosActivos');
    
    // Variables para saber si puede editar (desde PHP)
    const puedeEditar = @json($puedeEditar);
    
    // ===========================================
    // FUNCIONES PARA LISTAR BECAS (TODOS PUEDEN)
    // ===========================================
    function listarBecas() {
        const params = new URLSearchParams({
            busqueda: busquedaGeneral ? busquedaGeneral.value : ''
        });

        fetch(`{{ route("becas.listar") }}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarTabla(data.becas);
                if (totalResultados) {
                    totalResultados.innerHTML = `<i class="fas fa-list"></i> ${data.total} beca(s)`;
                }
                
                // Mostrar filtros activos
                if (busquedaGeneral && busquedaGeneral.value) {
                    if (filtrosActivos) {
                        filtrosActivos.innerHTML = `<i class="fas fa-search"></i> Buscando: "${busquedaGeneral.value}"`;
                        filtrosActivos.style.display = 'inline-block';
                    }
                    if (btnLimpiarFiltros) btnLimpiarFiltros.style.display = 'inline-block';
                } else {
                    if (filtrosActivos) filtrosActivos.style.display = 'none';
                    if (btnLimpiarFiltros) btnLimpiarFiltros.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error al listar becas:', error);
        });
    }

    function renderizarTabla(becas) {
        if (!tablaBecas) return;
        
        if (becas.length === 0) {
            let colspan = puedeEditar ? 3 : 2;
            tablaBecas.innerHTML = `
                <tr>
                    <td colspan="${colspan}" class="text-center py-4">
                        <i class="fas fa-list fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron becas</h5>
                        <p class="text-muted">Intente con otra búsqueda</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        
        becas.forEach(b => {
            html += `<tr data-id="${b.id}">
                <td><strong>${b.tipo_beca}</strong></td>
                <td>${b.descripcion || '<span class="text-muted">Sin descripción</span>'}</td>`;
            
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
        
        tablaBecas.innerHTML = html;
        
        // Si puede editar, agregar eventos a los botones
        if (puedeEditar) {
            document.querySelectorAll('.editar-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const id = row.dataset.id;
                    if (typeof cargarBecaParaEditar === 'function') {
                        cargarBecaParaEditar(id);
                    }
                });
            });
            
            document.querySelectorAll('.eliminar-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const id = row.dataset.id;
                    if (typeof eliminarBeca === 'function') {
                        eliminarBeca(id);
                    }
                });
            });
        }
    }

    // ===========================================
    // EVENT LISTENERS PARA BÚSQUEDA (TODOS PUEDEN)
    // ===========================================
    if (btnBuscar) btnBuscar.addEventListener('click', listarBecas);
    
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', function() {
            if (busquedaGeneral) busquedaGeneral.value = '';
            listarBecas();
        });
    }
    
    // Permitir búsqueda con Enter
    if (busquedaGeneral) {
        busquedaGeneral.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                listarBecas();
            }
        });
    }

    // Cargar lista inicial
    listarBecas();

    // ===========================================
    // FUNCIONES SOLO PARA EDITORES (con permisos G)
    // ===========================================
    @if($puedeEditar)
    
    // Elementos del DOM para edición
    const becaId = document.getElementById('becaId');
    const tipoBeca = document.getElementById('tipo_beca');
    const descripcion = document.getElementById('descripcion');
    const currentPassword = document.getElementById('current_password');
    const btnGuardar = document.getElementById('btnGuardarBeca');
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicion');

    // ===========================================
    // FUNCIONES PARA CARGAR/EDITAR BECAS
    // ===========================================
    window.cargarBecaParaEditar = function(id) {
        fetch(`{{ url('becas') }}/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const b = data.beca;
                if (becaId) becaId.value = b.id;
                if (tipoBeca) {
                    tipoBeca.value = b.tipo_beca;
                    actualizarContador(tipoBeca, 'tipoCounter');
                }
                if (descripcion) {
                    descripcion.value = b.descripcion || '';
                    actualizarContador(descripcion, 'descCounter');
                }
                
                if (btnCancelarEdicion) btnCancelarEdicion.style.display = 'inline-block';
                
                // Scroll al formulario
                const formBeca = document.getElementById('formBeca');
                if (formBeca) formBeca.scrollIntoView({ behavior: 'smooth' });
            }
        });
    };

    // ===========================================
    // FUNCIONES PARA GUARDAR
    // ===========================================
    if (btnGuardar) {
        btnGuardar.addEventListener('click', function() {
            if (!currentPassword || !currentPassword.value) {
                alert('Debe ingresar su contraseña para autorizar.');
                if (currentPassword) currentPassword.focus();
                return;
            }
            
            if (!tipoBeca || !tipoBeca.value.trim()) {
                alert('Debe ingresar el tipo de beca.');
                if (tipoBeca) tipoBeca.focus();
                return;
            }

            const data = {
                current_password: currentPassword.value,
                id: becaId ? (becaId.value || null) : null,
                tipo_beca: tipoBeca.value.trim(),
                descripcion: descripcion ? descripcion.value.trim() : null
            };

            fetch('{{ route("becas.guardar") }}', {
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
                    if (typeof limpiarFormulario === 'function') limpiarFormulario();
                    listarBecas(); // Refrescar tabla
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

    // ===========================================
    // FUNCIONES PARA ELIMINAR
    // ===========================================
    window.eliminarBeca = function(id) {
        if (!currentPassword || !currentPassword.value) {
            alert('Debe ingresar su contraseña para eliminar.');
            if (currentPassword) currentPassword.focus();
            return;
        }
        
        if (!confirm('¿Está seguro de eliminar esta beca? Esta acción no se puede deshacer.')) {
            return;
        }

        fetch(`{{ url('becas') }}/${id}`, {
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
                listarBecas(); // Refrescar tabla
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al eliminar.');
        });
    };

    // Botón cancelar edición
    if (btnCancelarEdicion) {
        btnCancelarEdicion.addEventListener('click', function() {
            if (typeof limpiarFormulario === 'function') limpiarFormulario();
        });
    }

    @endif
});

// Función global para limpiar formulario
function limpiarFormulario() {
    const becaId = document.getElementById('becaId');
    const tipoBeca = document.getElementById('tipo_beca');
    const descripcion = document.getElementById('descripcion');
    const currentPassword = document.getElementById('current_password');
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicion');
    
    if (becaId) becaId.value = '';
    if (tipoBeca) {
        tipoBeca.value = '';
        document.getElementById('tipoCounter').textContent = '0';
    }
    if (descripcion) {
        descripcion.value = '';
        document.getElementById('descCounter').textContent = '0';
    }
    if (currentPassword) currentPassword.value = '';
    if (btnCancelarEdicion) btnCancelarEdicion.style.display = 'none';
}
</script>
@endpush