@extends('layouts.app')

@section('title', 'Configuración de Materias y Laboratorios - SADHCC')

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
            'name' => 'Configuración de Materias y Laboratorios',
            'url' => null,
            'icon' => 'fa-laptop-file'
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
    .form-row h6 {
        color: #495057;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    .section-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0;
    }
    .counter-badge {
        font-size: 0.75rem;
        color: #6c757d;
    }
    .card-equal-height {
        display: flex;
        flex-direction: column;
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
                <i class="fas fa-laptop-file me-2 text-danger"></i>
                CONFIGURACIÓN DE MATERIAS Y LABORATORIOS
            </h2>
        </div>
    </div>
    
    <!-- ============ SECCIÓN DE FORMULARIOS LADO A LADO ============ -->
    <div class="row g-4 mb-4">
        
        <!-- FORMULARIO MATERIAS (Izquierda) -->
        <div class="col-lg-8">
            <div class="module-section h-100">
                <h5 class="mb-4"><i class="fas fa-book me-2"></i> REGISTRO Y EDICIÓN DE MATERIAS</h5>
                
                <div class="form-row">
                    <input type="hidden" id="materiaId" value="">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="materiaNombre"
                                   maxlength="50"
                                   autocomplete="off"
                                   placeholder="Ej: Submódulo I"
                                   oninput="actualizarContador(this, 'materiaNombreCounter')">
                            <small class="counter-badge"><span id="materiaNombreCounter">0</span>/50</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Laboratorio</label>
                            <select id="materiaLaboratorio" class="form-select">
                                <option value="">—Ninguno—</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Descripción *</label>
                            <textarea 
                                   id="materiaDescripcion"
                                   class="form-control"
                                   placeholder="Describa la materia."
                                   maxlength="255"
                                   rows="2"
                                   autocomplete="off"
                                   oninput="actualizarContador(this, 'materiaDescCounter')"></textarea>
                            <small class="counter-badge"><span id="materiaDescCounter">0</span>/255</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Módulo</label>
                            <select id="materiaModulo" class="form-select">
                                <option value="">—Seleccione—</option>
                                <option value="I">I</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                                <option value="V">V</option>
                                <option value="No aplica">No aplica</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Submódulo</label>
                            <select id="materiaSubmodulo" class="form-select">
                                <option value="">—Seleccione—</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="No aplica">No aplica</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="fas fa-lock me-1"></i>Contraseña de Administrador *
                            </label>
                            <input type="password" 
                                   class="form-control form-control-sm" 
                                   id="materiaPassword" 
                                   placeholder="Ingrese su contraseña"
                                   autocomplete="new-password">
                        </div>
                        <div class="col-md-7 d-flex align-items-end justify-content-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-danger btn-sm" id="btnGuardarMateria">
                                    <i class="fas fa-save me-1"></i>Guardar Materia
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCancelarMateria" style="display:none;">
                                    <i class="fas fa-times me-1"></i>Cancelar Edición
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFormMateria()">
                                    <i class="fas fa-eraser me-1"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORMULARIO LABORATORIOS (Derecha) -->
        <div class="col-lg-4">
            <div class="module-section h-100">
                <h5 class="mb-4"><i class="fas fa-flask me-2"></i> REGISTRO Y EDICIÓN DE LABORATORIOS</h5>
                
                <div class="form-row">
                    <input type="hidden" id="labId" value="">
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Nombre *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="labNombre"
                                   maxlength="30"
                                   autocomplete="off"
                                   placeholder="Ej: Laboratorio de Redes"
                                   oninput="actualizarContador(this, 'labNombreCounter')">
                            <small class="counter-badge"><span id="labNombreCounter">0</span>/30</small>
                        </div>
                    </div>

                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-12 mb-2">
                            <label class="form-label">
                                <i class="fas fa-lock me-1"></i>Contraseña de Administrador *
                            </label>
                            <input type="password" 
                                   class="form-control form-control-sm" 
                                   id="labPassword" 
                                   placeholder="Ingrese su contraseña"
                                   autocomplete="new-password">
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-danger btn-sm" id="btnGuardarLab">
                                    <i class="fas fa-save me-1"></i>Guardar Laboratorio
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCancelarLab" style="display:none;">
                                    <i class="fas fa-times me-1"></i>Cancelar
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFormLab()">
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
        
        <!-- TABLA MATERIAS (Izquierda) -->
        <div class="col-lg-8">
            <div class="module-section h-100">
                <h5 class="mb-4"><i class="fas fa-search me-2"></i> CONSULTA DE MATERIAS</h5>

                <div class="row mb-3 g-2">
                    <div class="col-md-3">
                        <label class="form-label small">Módulo:</label>
                        <select id="filtroModulo" class="form-select form-select-sm">
                            <option value="">—Todos—</option>
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                            <option value="No aplica">No aplica</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Submódulo:</label>
                        <select id="filtroSubmodulo" class="form-select form-select-sm">
                            <option value="">—Todos—</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="No aplica">No aplica</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Buscar:</label>
                        <input type="text" 
                               id="filtroMateriaBusqueda" 
                               class="form-control form-control-sm" 
                               placeholder="Nombre, descripción, laboratorio"
                               autocomplete="off">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm w-100" type="button" id="btnFiltrarMaterias">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                        <span class="filter-badge" id="totalMaterias">
                            <i class="fas fa-book"></i> 0 materia(s)
                        </span>
                        <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarFiltrosMaterias" style="display:none;">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 25%;">Nombre</th>
                                <th style="width: 30%;">Descripción</th>
                                <th style="width: 10%;">Módulo</th>
                                <th style="width: 10%;">Submód.</th>
                                <th style="width: 15%;">Laboratorio</th>
                                <th style="width: 10%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaMaterias">
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-book fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No hay materias registradas</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TABLA LABORATORIOS (Derecha) -->
        <div class="col-lg-4">
            <div class="module-section h-100">
                <h5 class="mb-4"><i class="fas fa-search me-2"></i> CONSULTA DE LABORATORIOS</h5>

                <div class="row mb-3 g-2">
                    <div class="col-8">
                        <label class="form-label small">Buscar:</label>
                        <input type="text" 
                               id="filtroLabBusqueda" 
                               class="form-control form-control-sm" 
                               placeholder="Nombre del laboratorio"
                               autocomplete="off">
                    </div>
                    <div class="col-4 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm w-100" type="button" id="btnFiltrarLabs">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                        <span class="filter-badge" id="totalLabs">
                            <i class="fas fa-flask"></i> 0 laboratorio(s)
                        </span>
                        <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarFiltrosLabs" style="display:none;">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 60%;">Nombre</th>
                                <th style="width: 40%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaLabs">
                            <tr>
                                <td colspan="2" class="text-center py-4">
                                    <i class="fas fa-flask fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No hay laboratorios</p>
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
// ===== FUNCIONES GLOBALES (fuera del DOMContentLoaded para usar onclick) =====
function actualizarContador(input, counterId) {
    const counter = document.getElementById(counterId);
    if (counter) counter.textContent = input.value.length;
}

function limpiarFormMateria() {
    document.getElementById('materiaId').value = '';
    document.getElementById('materiaNombre').value = '';
    document.getElementById('materiaDescripcion').value = '';
    document.getElementById('materiaModulo').value = '';
    document.getElementById('materiaSubmodulo').value = '';
    document.getElementById('materiaLaboratorio').value = '';
    document.getElementById('materiaPassword').value = '';
    document.getElementById('materiaNombreCounter').textContent = '0';
    document.getElementById('materiaDescCounter').textContent = '0';
    document.getElementById('btnCancelarMateria').style.display = 'none';
    document.getElementById('btnGuardarMateria').innerHTML = '<i class="fas fa-save me-1"></i>Guardar Materia';
}

function limpiarFormLab() {
    document.getElementById('labId').value = '';
    document.getElementById('labNombre').value = '';
    document.getElementById('labPassword').value = '';
    document.getElementById('labNombreCounter').textContent = '0';
    document.getElementById('btnCancelarLab').style.display = 'none';
    document.getElementById('btnGuardarLab').innerHTML = '<i class="fas fa-save me-1"></i>Guardar Laboratorio';
}

document.addEventListener('DOMContentLoaded', function() {
    @if($esAdmin)
    
    // ===== ELEMENTOS =====
    const materiaId = document.getElementById('materiaId');
    const materiaNombre = document.getElementById('materiaNombre');
    const materiaDescripcion = document.getElementById('materiaDescripcion');
    const materiaModulo = document.getElementById('materiaModulo');
    const materiaSubmodulo = document.getElementById('materiaSubmodulo');
    const materiaLaboratorio = document.getElementById('materiaLaboratorio');
    const materiaPassword = document.getElementById('materiaPassword');
    const btnGuardarMateria = document.getElementById('btnGuardarMateria');
    const btnCancelarMateria = document.getElementById('btnCancelarMateria');
    
    const labId = document.getElementById('labId');
    const labNombre = document.getElementById('labNombre');
    const labPassword = document.getElementById('labPassword');
    const btnGuardarLab = document.getElementById('btnGuardarLab');
    const btnCancelarLab = document.getElementById('btnCancelarLab');
    
    const tablaMaterias = document.getElementById('tablaMaterias');
    const tablaLabs = document.getElementById('tablaLabs');
    const totalMaterias = document.getElementById('totalMaterias');
    const totalLabs = document.getElementById('totalLabs');
    
    const filtroModulo = document.getElementById('filtroModulo');
    const filtroSubmodulo = document.getElementById('filtroSubmodulo');
    const filtroMateriaBusqueda = document.getElementById('filtroMateriaBusqueda');
    const filtroLabBusqueda = document.getElementById('filtroLabBusqueda');
    const btnFiltrarMaterias = document.getElementById('btnFiltrarMaterias');
    const btnFiltrarLabs = document.getElementById('btnFiltrarLabs');
    const btnLimpiarFiltrosMaterias = document.getElementById('btnLimpiarFiltrosMaterias');
    const btnLimpiarFiltrosLabs = document.getElementById('btnLimpiarFiltrosLabs');

    // ===== CARGAR LABORATORIOS EN EL SELECT =====
    function cargarLabsEnSelect() {
        fetch('{{ route("admin.materias.lab.select") }}')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const currentVal = materiaLaboratorio.value;
                materiaLaboratorio.innerHTML = '<option value="">—Ninguno—</option>';
                data.laboratorios.forEach(lab => {
                    const opt = document.createElement('option');
                    opt.value = lab.id;
                    opt.textContent = lab.nombre;
                    materiaLaboratorio.appendChild(opt);
                });
                if (currentVal) materiaLaboratorio.value = currentVal;
            }
        });
    }

    // ===== LISTAR MATERIAS =====
    function listarMaterias() {
        const params = new URLSearchParams({
            modulo: filtroModulo.value || '',
            submodulo: filtroSubmodulo.value || '',
            busqueda: filtroMateriaBusqueda.value || ''
        });

        fetch(`{{ route("admin.materias.listar") }}?${params}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderTablaMaterias(data.materias);
                totalMaterias.innerHTML = `<i class="fas fa-book"></i> ${data.total} materia(s)`;
                
                const hayFiltros = filtroModulo.value || filtroSubmodulo.value || filtroMateriaBusqueda.value;
                btnLimpiarFiltrosMaterias.style.display = hayFiltros ? 'inline-block' : 'none';
            }
        })
        .catch(e => console.error('Error al listar materias:', e));
    }

    function renderTablaMaterias(materias) {
        if (materias.length === 0) {
            tablaMaterias.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="fas fa-book fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No se encontraron materias</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        materias.forEach(m => {
            html += `<tr data-id="${m.id}">
                <td>${m.nombre || '—'}</td>
                <td><small>${m.descripcion || '—'}</small></td>
                <td><span class="badge bg-secondary">${m.modulo || '—'}</span></td>
                <td><span class="badge bg-info">${m.submodulo || '—'}</span></td>
                <td><small>${m.laboratorio_nombre || '—'}</small></td>
                <td class="action-buttons">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary editar-materia-btn" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger eliminar-materia-btn" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });
        tablaMaterias.innerHTML = html;

        document.querySelectorAll('.editar-materia-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.closest('tr').dataset.id;
                cargarMateria(id);
            });
        });
        document.querySelectorAll('.eliminar-materia-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.closest('tr').dataset.id;
                eliminarMateria(id);
            });
        });
    }

    // ===== CARGAR MATERIA PARA EDITAR =====
    function cargarMateria(id) {
        fetch(`{{ url('admin/materias-lab/materias') }}/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const m = data.materia;
                materiaId.value = m.id;
                materiaNombre.value = m.nombre || '';
                materiaDescripcion.value = m.descripcion || '';
                materiaModulo.value = m.modulo || '';
                materiaSubmodulo.value = m.submodulo || '';
                materiaLaboratorio.value = m.id_laboratorio || '';
                
                document.getElementById('materiaNombreCounter').textContent = (m.nombre || '').length;
                document.getElementById('materiaDescCounter').textContent = (m.descripcion || '').length;
                
                btnCancelarMateria.style.display = 'inline-block';
                btnGuardarMateria.innerHTML = '<i class="fas fa-edit me-1"></i>Actualizar Materia';
                document.getElementById('materiaNombre').scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error al cargar materia.'); });
    }

    // ===== GUARDAR MATERIA =====
    btnGuardarMateria.addEventListener('click', function() {
        if (!materiaPassword.value) {
            alert('⚠️ Debe ingresar su contraseña de administrador.');
            materiaPassword.focus();
            return;
        }
        if (!materiaNombre.value.trim()) {
            alert('⚠️ El nombre es obligatorio.');
            materiaNombre.focus();
            return;
        }
        if (!materiaDescripcion.value.trim()) {
            alert('⚠️ La descripción es obligatoria.');
            materiaDescripcion.focus();
            return;
        }

        const data = {
            current_password: materiaPassword.value,
            id: materiaId.value || null,
            nombre: materiaNombre.value,
            descripcion: materiaDescripcion.value,
            modulo: materiaModulo.value || null,
            submodulo: materiaSubmodulo.value || null,
            id_laboratorio: materiaLaboratorio.value || null
        };

        btnGuardarMateria.disabled = true;
        const original = btnGuardarMateria.innerHTML;
        btnGuardarMateria.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';

        fetch('{{ route("admin.materias.guardar") }}', {
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
                limpiarFormMateria();
                listarMaterias();
                cargarLabsEnSelect();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); })
        .finally(() => {
            btnGuardarMateria.disabled = false;
            btnGuardarMateria.innerHTML = original;
        });
    });

    // ===== ELIMINAR MATERIA =====
    function eliminarMateria(id) {
        if (!materiaPassword.value) {
            alert('⚠️ Debe ingresar su contraseña de administrador.');
            materiaPassword.focus();
            return;
        }
        if (!confirm('¿Está seguro de eliminar esta materia?')) return;

        fetch(`{{ url('admin/materias-lab/materias') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ current_password: materiaPassword.value })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                materiaPassword.value = '';
                listarMaterias();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); });
    }

    // ===== LISTAR LABORATORIOS =====
    function listarLabs() {
        const params = new URLSearchParams({
            busqueda: filtroLabBusqueda.value || ''
        });

        fetch(`{{ route("admin.materias.lab.listar") }}?${params}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderTablaLabs(data.laboratorios);
                totalLabs.innerHTML = `<i class="fas fa-flask"></i> ${data.total} laboratorio(s)`;
                btnLimpiarFiltrosLabs.style.display = filtroLabBusqueda.value ? 'inline-block' : 'none';
            }
        })
        .catch(e => console.error('Error al listar laboratorios:', e));
    }

    function renderTablaLabs(labs) {
        if (labs.length === 0) {
            tablaLabs.innerHTML = `
                <tr>
                    <td colspan="2" class="text-center py-4">
                        <i class="fas fa-flask fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No se encontraron laboratorios</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        labs.forEach(l => {
            html += `<tr data-id="${l.id}">
                <td>${l.nombre}</td>
                <td class="action-buttons">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary editar-lab-btn" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger eliminar-lab-btn" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });
        tablaLabs.innerHTML = html;

        document.querySelectorAll('.editar-lab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.closest('tr').dataset.id;
                cargarLab(id);
            });
        });
        document.querySelectorAll('.eliminar-lab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.closest('tr').dataset.id;
                eliminarLab(id);
            });
        });
    }

    // ===== CARGAR LABORATORIO PARA EDITAR =====
    function cargarLab(id) {
        fetch(`{{ url('admin/materias-lab/laboratorios') }}/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const l = data.laboratorio;
                labId.value = l.id;
                labNombre.value = l.nombre || '';
                document.getElementById('labNombreCounter').textContent = (l.nombre || '').length;
                btnCancelarLab.style.display = 'inline-block';
                btnGuardarLab.innerHTML = '<i class="fas fa-edit me-1"></i>Actualizar Laboratorio';
                document.getElementById('labNombre').scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error al cargar laboratorio.'); });
    }

    // ===== GUARDAR LABORATORIO =====
    btnGuardarLab.addEventListener('click', function() {
        if (!labPassword.value) {
            alert('⚠️ Debe ingresar su contraseña de administrador.');
            labPassword.focus();
            return;
        }
        if (!labNombre.value.trim()) {
            alert('⚠️ El nombre es obligatorio.');
            labNombre.focus();
            return;
        }

        const data = {
            current_password: labPassword.value,
            id: labId.value || null,
            nombre: labNombre.value
        };

        btnGuardarLab.disabled = true;
        const original = btnGuardarLab.innerHTML;
        btnGuardarLab.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';

        fetch('{{ route("admin.materias.lab.guardar") }}', {
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
                limpiarFormLab();
                listarLabs();
                cargarLabsEnSelect();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); })
        .finally(() => {
            btnGuardarLab.disabled = false;
            btnGuardarLab.innerHTML = original;
        });
    });

    // ===== ELIMINAR LABORATORIO =====
    function eliminarLab(id) {
        if (!labPassword.value) {
            alert('⚠️ Debe ingresar su contraseña de administrador.');
            labPassword.focus();
            return;
        }
        if (!confirm('¿Está seguro de eliminar este laboratorio? Las materias que lo usan quedarán sin laboratorio asignado.')) return;

        fetch(`{{ url('admin/materias-lab/laboratorios') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ current_password: labPassword.value })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                labPassword.value = '';
                listarLabs();
                listarMaterias();
                cargarLabsEnSelect();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); });
    }

    // ===== BOTONES DE CANCELAR =====
    btnCancelarMateria.addEventListener('click', limpiarFormMateria);
    btnCancelarLab.addEventListener('click', limpiarFormLab);

    // ===== FILTROS =====
    btnFiltrarMaterias.addEventListener('click', listarMaterias);
    btnFiltrarLabs.addEventListener('click', listarLabs);
    
    btnLimpiarFiltrosMaterias.addEventListener('click', function() {
        filtroModulo.value = '';
        filtroSubmodulo.value = '';
        filtroMateriaBusqueda.value = '';
        listarMaterias();
    });
    
    btnLimpiarFiltrosLabs.addEventListener('click', function() {
        filtroLabBusqueda.value = '';
        listarLabs();
    });

    // Enter en los filtros
    filtroMateriaBusqueda.addEventListener('keypress', e => {
        if (e.key === 'Enter') { e.preventDefault(); listarMaterias(); }
    });
    filtroLabBusqueda.addEventListener('keypress', e => {
        if (e.key === 'Enter') { e.preventDefault(); listarLabs(); }
    });

    // ===== INICIALIZAR =====
    cargarLabsEnSelect();
    listarMaterias();
    listarLabs();
    
    @endif
});
</script>
@endpush