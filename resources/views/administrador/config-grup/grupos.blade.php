@extends('layouts.app')

@section('title', 'Configuración de Grupos - SADHCC')

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
@if($esAdmin)
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
    
    <!-- Formulario de registro/edición -->
    <div class="module-section">
        <h5 class="mb-4"><i class="fa-solid fa-people-group"></i> REGISTRO Y EDICIÓN DE GRUPOS</h5>
        
        <div class="form-row" id="formGrupo">
            <input type="hidden" id="grupoId" value="">
            
            <div class="row mb-3">
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label class="form-label">Grupo *</label>
                    <select id="grupo" class="form-select" required>
                        <option value="">—Seleccione—</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                    </select>
                </div>
                <div class="col-md-6">
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
                           autocomplete="new-password"
                           required>
                    <small class="text-muted">Requerida para confirmar cualquier modificación</small>
                </div>
                <div class="col-md-8 d-flex align-items-end justify-content-end">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-success" id="btnGuardarGrupo">
                            <i class="fas fa-save me-2"></i>Guardar Grupo
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCancelarEdicion" style="display:none;">
                            <i class="fas fa-times me-2"></i>Cancelar Edición
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormulario()">
                            <i class="fas fa-eraser me-2"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="module-section">
        <h5 class="mb-4"><i class="fas fa-search"></i> CONSULTA DE GRUPOS</h5>

        <div class="row mb-3">
            <div class="col-md-3">
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
            <div class="col-md-3">
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
                <button class="btn btn-primary w-100" type="button" id="btnAplicarFiltros">
                    <i class="fas fa-filter"></i> Filtrar
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
                <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarFiltros" style="display:none;">
                    <i class="fas fa-times"></i> Limpiar filtros
                </button>
            </div>
        </div>

        <!-- Tabla de grupos -->
        <div class="table-container">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 20%;">Semestre</th>
                        <th style="width: 20%;">Grupo</th>
                        <th style="width: 40%;">Carrera</th>
                        <th style="width: 20%;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaGrupos">
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Realice una búsqueda para ver resultados</h5>
                        </td>
                    </tr>
                </tbody>
            </table>
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
document.addEventListener('DOMContentLoaded', function() {
    @if($esAdmin)
    // ===== ELEMENTOS DEL DOM =====
    const filtroSemestre = document.getElementById('filtroSemestre');
    const filtroGrupo = document.getElementById('filtroGrupo');
    const filtroCarrera = document.getElementById('filtroCarrera');
    const btnAplicarFiltros = document.getElementById('btnAplicarFiltros');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    const tablaGrupos = document.getElementById('tablaGrupos');
    const totalResultados = document.getElementById('totalResultados');
    const filtrosActivos = document.getElementById('filtrosActivos');

    // Elementos del formulario
    const grupoId = document.getElementById('grupoId');
    const semestre = document.getElementById('semestre');
    const grupo = document.getElementById('grupo');
    const carrera = document.getElementById('carrera');
    const currentPassword = document.getElementById('current_password');
    const btnGuardar = document.getElementById('btnGuardarGrupo');
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicion');

    // ===== FUNCIONES PRINCIPALES =====

    // Listar grupos con filtros
    function listarGrupos() {
        const params = new URLSearchParams({
            semestre: filtroSemestre.value || '',
            grupo: filtroGrupo.value || '',
            carrera: filtroCarrera.value || ''
        });

        fetch(`{{ route("admin.grupos.listar") }}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarTabla(data.grupos);
                totalResultados.innerHTML = `<i class="fas fa-users"></i> ${data.total} grupo(s)`;
                
                // Mostrar filtros activos
                let filtrosTexto = [];
                if (filtroSemestre.value) filtrosTexto.push(`Semestre: ${filtroSemestre.value}`);
                if (filtroGrupo.value) filtrosTexto.push(`Grupo: ${filtroGrupo.value}`);
                if (filtroCarrera.value) filtrosTexto.push(`Carrera: ${filtroCarrera.value}`);
                
                if (filtrosTexto.length > 0) {
                    filtrosActivos.innerHTML = `<i class="fas fa-filter"></i> ${filtrosTexto.join(' | ')}`;
                    filtrosActivos.style.display = 'inline-block';
                    btnLimpiarFiltros.style.display = 'inline-block';
                } else {
                    filtrosActivos.style.display = 'none';
                    btnLimpiarFiltros.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error al listar grupos:', error);
            alert('Error al cargar los grupos.');
        });
    }

    // Renderizar tabla de grupos
    function renderizarTabla(grupos) {
        if (grupos.length === 0) {
            tablaGrupos.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
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
                <td><span class="badge bg-secondary">${g.semestre}</span></td>
                <td><span class="badge bg-primary">${g.grupo}</span></td>
                <td>${g.carrera}</td>
                <td class="action-buttons">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary editar-btn" title="Editar grupo">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger eliminar-btn" title="Eliminar grupo">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });
        
        tablaGrupos.innerHTML = html;

        // Eventos para botones de editar
        document.querySelectorAll('.editar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = row.dataset.id;
                cargarGrupoParaEditar(id);
            });
        });

        // Eventos para botones de eliminar
        document.querySelectorAll('.eliminar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = row.dataset.id;
                eliminarGrupo(id);
            });
        });
    }

    // Cargar grupo para editar
    function cargarGrupoParaEditar(id) {
        console.log('Cargando grupo con ID:', id); // Debug
        
        fetch(`{{ url('admin/grupos') }}/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug
            console.log('Response headers:', response.headers); // Debug
            
            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // Si no es JSON, leer como texto para ver el error
                return response.text().then(text => {
                    console.error('Respuesta no JSON:', text);
                    throw new Error('La respuesta no es JSON. Error del servidor.');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data); // Debug
            
            if (data.success) {
                const g = data.grupo;
                grupoId.value = g.id;
                semestre.value = g.semestre;
                grupo.value = g.grupo;
                carrera.value = g.carrera;
                
                btnCancelarEdicion.style.display = 'inline-block';
                
                // Scroll al formulario
                document.getElementById('formGrupo').scrollIntoView({ behavior: 'smooth' });
                
                // Cambiar texto del botón
                btnGuardar.innerHTML = '<i class="fas fa-edit me-2"></i>Actualizar Grupo';
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error al cargar grupo:', error);
            alert('❌ Error al cargar el grupo. Revisa la consola para más detalles.');
        });
    }

    // Guardar grupo (crear o actualizar)
    function guardarGrupo() {
        if (!currentPassword.value) {
            alert('⚠️ Debe ingresar su contraseña de administrador.');
            currentPassword.focus();
            return;
        }
        
        if (!semestre.value || !grupo.value || !carrera.value) {
            alert('⚠️ Debe completar todos los campos obligatorios.');
            return;
        }

        const data = {
            current_password: currentPassword.value,
            id: grupoId.value || null,
            semestre: semestre.value,
            grupo: grupo.value,
            carrera: carrera.value
        };

        // Deshabilitar botón
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
                listarGrupos();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al guardar.');
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = originalText;
        });
    }

    // Eliminar grupo
    function eliminarGrupo(id) {
        if (!currentPassword.value) {
            alert('⚠️ Debe ingresar su contraseña de administrador.');
            currentPassword.focus();
            return;
        }
        
        if (!confirm('⚠️ ¿Está seguro de eliminar este grupo?\n\nEsta acción no se puede deshacer.')) {
            return;
        }

        // Deshabilitar botón específico
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
                // Limpiar contraseña
                currentPassword.value = '';
                listarGrupos();
            } else {
                alert('⚠️ ' + data.message);
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

    // Limpiar formulario
    function limpiarFormulario() {
        grupoId.value = '';
        semestre.value = '';
        grupo.value = '';
        carrera.value = '';
        currentPassword.value = '';
        btnCancelarEdicion.style.display = 'none';
        btnGuardar.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Grupo';
    }

    // ===== EVENT LISTENERS =====

    // Botón guardar
    btnGuardar.addEventListener('click', guardarGrupo);

    // Botón cancelar edición
    btnCancelarEdicion.addEventListener('click', limpiarFormulario);

    // Botón aplicar filtros
    btnAplicarFiltros.addEventListener('click', listarGrupos);

    // Botón limpiar filtros
    btnLimpiarFiltros.addEventListener('click', function() {
        filtroSemestre.value = '';
        filtroGrupo.value = '';
        filtroCarrera.value = '';
        listarGrupos();
    });

    // Cargar lista inicial
    listarGrupos();

    // Enter en los campos de filtro
    document.querySelectorAll('#filtroSemestre, #filtroGrupo, #filtroCarrera').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                listarGrupos();
            }
        });
    });

    // Enter en el formulario
    document.getElementById('formGrupo').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            guardarGrupo();
        }
    });

    // Limpiar contraseña en la vista
    currentPassword.addEventListener('input', function() {
        // Este evento se mantiene por si se necesita
    });

    // ===== FUNCIÓN GLOBAL =====
    window.limpiarFormulario = limpiarFormulario;
    
    @endif
});
</script>
@endpush