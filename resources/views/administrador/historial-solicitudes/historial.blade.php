@extends('layouts.app')

@section('title', 'Historial de Solicitudes - SADHCC')

@php
    $usuario = Auth::user();
    $esAdmin = $usuario && $usuario->tipo === 'Administrador';

    $breadcrumbs = [
        ['name' => 'Inicio', 'url' => route('welcome'), 'icon' => 'fa-home'],
        ['name' => 'Historial de Solicitudes', 'url' => null, 'icon' => 'fa-history']
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
        max-height: 550px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    .table-container thead th {
        position: sticky;
        top: 0;
        background-color: #212529;
        color: white;
        z-index: 10;
        font-size: 0.8rem;
        white-space: nowrap;
    }
    .table-container tbody td {
        font-size: 0.82rem;
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
    .estatus-badge {
        padding: 0.35rem 0.7rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .estatus-Reservada { background-color: #0d6efd; color: white; }
    .estatus-Cumplida { background-color: #198754; color: white; }
    .estatus-No-cumplida { background-color: #dc3545; color: white; }
    .modal-header { border-bottom: 2px solid #dee2e6; }
    .modal-body .info-label { font-weight: 600; color: #495057; }
    .modal-body .badge-list {
        display: flex; flex-wrap: wrap; gap: 0.4rem;
    }
    .modal-body .badge-list .badge {
        font-size: 0.8rem; padding: 0.4rem 0.7rem;
    }
</style>
@endpush

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid px-4">
    @include('partials.breadcrumb')

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="border-bottom pb-3">
                <i class="fas fa-history me-2 text-info"></i>
                HISTORIAL DE SOLICITUDES
            </h2>
        </div>
    </div>

    <!-- Filtros -->
    <div class="module-section">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i> FILTROS</h5>

        <div class="row mb-3 g-2">
            <div class="col-md-4">
                <label class="form-label small">Buscar:</label>
                <input type="text" id="filtroBusqueda" class="form-control form-control-sm"
                       placeholder="Docente, grupo, nombre de práctica..."
                       autocomplete="off">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Laboratorio:</label>
                <select id="filtroLaboratorio" class="form-select form-select-sm">
                    <option value="">—Todos—</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Materia:</label>
                <select id="filtroMateria" class="form-select form-select-sm">
                    <option value="">—Todas—</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Estatus:</label>
                <select id="filtroEstatus" class="form-select form-select-sm">
                    <option value="">—Todos—</option>
                    <option value="Reservada">Reservada</option>
                    <option value="Cumplida">Cumplida</option>
                    <option value="No cumplida">No cumplida</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-info btn-sm w-100" type="button" id="btnFiltrar">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-12">
                <span class="filter-badge" id="totalResultados">
                    <i class="fas fa-list"></i> 0 solicitud(es)
                </span>
                <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarFiltros" style="display:none;">
                    <i class="fas fa-times"></i> Limpiar filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="module-section">
        <div class="table-container">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        @if($esAdmin)
                            <th>Docente</th>
                        @endif
                        <th>Grupo</th>
                        <th>Laboratorio</th>
                        <th>Materia</th>
                        <th>Nombre</th>
                        <th>Estatus</th>
                        <th>F. Solicitud</th>
                        <th>Detalles</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaHistorial">
                    <tr>
                        <td colspan="{{ $esAdmin ? 9 : 8 }}" class="text-center py-4">
                            <i class="fas fa-history fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Cargando historial...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============ MODAL: DETALLES ============ -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detalles de la Práctica</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="info-label">Docente:</div>
                        <div id="detDocente">—</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Grupo:</div>
                        <div id="detGrupo">—</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="info-label">Materia:</div>
                        <div id="detMateria">—</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Laboratorio:</div>
                        <div id="detLaboratorio">—</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="info-label">Nombre de la práctica:</div>
                        <div id="detNombre">—</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">No. Actividad:</div>
                        <div id="detNoActividad">—</div>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <div class="info-label">Competencia:</div>
                    <div id="detCompetencia" class="text-muted">—</div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Atributo:</div>
                    <div id="detAtributo" class="text-muted">—</div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Materiales:</div>
                    <div id="detMateriales" class="badge-list">
                        <span class="text-muted">—</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Herramientas:</div>
                    <div id="detHerramientas" class="badge-list">
                        <span class="text-muted">—</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Notas adicionales:</div>
                    <div id="detNotas" class="text-muted">—</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ============ MODAL: EDITAR ESTATUS ============ -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Práctica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Horas de la práctica:</label>
                    <div id="listaHorasEditar" class="border rounded p-2" style="max-height: 400px; overflow-y: auto;">
                        <!-- Se llena con JS -->
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-lock me-1"></i>Contraseña de Administrador *</label>
                    <input type="password" class="form-control" id="editPassword"
                           placeholder="Ingrese su contraseña para autorizar los cambios"
                           autocomplete="new-password">
                    <small class="text-muted">Requerida para guardar los cambios de estatus.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnGuardarEstatusMasivo">
                    <i class="fas fa-save me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
window.addEventListener('load', function() {

    console.log('Historial: JS cargado');
    
    // Verificar Bootstrap
    if (typeof bootstrap === 'undefined') {
        console.error('❌ Bootstrap no está definido. Revisa el orden de scripts en layouts/app.blade.php');
    }

    // ===== ELEMENTOS =====
    const filtroBusqueda = document.getElementById('filtroBusqueda');
    const filtroLaboratorio = document.getElementById('filtroLaboratorio');
    const filtroMateria = document.getElementById('filtroMateria');
    const filtroEstatus = document.getElementById('filtroEstatus');
    const btnFiltrar = document.getElementById('btnFiltrar');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    const tablaHistorial = document.getElementById('tablaHistorial');
    const totalResultados = document.getElementById('totalResultados');
    const esAdmin = @json($esAdmin);

    const modalDetalles = new bootstrap.Modal(document.getElementById('modalDetalles'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));

    // ===== CARGAR LABORATORIOS =====
    function cargarLaboratorios() {
        fetch('{{ route("historial.laboratorios.listar") }}', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                filtroLaboratorio.innerHTML = '<option value="">—Todos—</option>';
                data.laboratorios.forEach(l => {
                    const opt = document.createElement('option');
                    opt.value = l.id;
                    opt.textContent = l.nombre;
                    filtroLaboratorio.appendChild(opt);
                });
            }
        });
    }

    function cargarMaterias(idLaboratorio) {
        const url = new URL('{{ route("historial.materias.por.laboratorio") }}', window.location.origin);
        if (idLaboratorio) url.searchParams.append('id_laboratorio', idLaboratorio);

        fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                filtroMateria.innerHTML = '<option value="">—Todas—</option>';
                data.materias.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.nombre;
                    filtroMateria.appendChild(opt);
                });
            }
        });
    }

    filtroLaboratorio.addEventListener('change', function() {
        cargarMaterias(this.value || null);
    });

    // ===== LISTAR =====
    function listar() {
        const params = new URLSearchParams({
            busqueda: filtroBusqueda.value || '',
            id_laboratorio: filtroLaboratorio.value || '',
            id_materia: filtroMateria.value || '',
            estatus: filtroEstatus.value || '',
        });

        fetch(`{{ route("historial.listar") }}?${params}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderTabla(data.practicas);
                totalResultados.innerHTML = `<i class="fas fa-list"></i> ${data.total} solicitud(es)`;
                const hayFiltros = filtroBusqueda.value || filtroLaboratorio.value ||
                                   filtroMateria.value || filtroEstatus.value;
                btnLimpiarFiltros.style.display = hayFiltros ? 'inline-block' : 'none';
            }
        })
        .catch(e => console.error(e));
    }

    function estatusClase(estatus) {
        return 'estatus-' + estatus.replace(/\s+/g, '-');
    }

    function renderTabla(practicas) {
        if (practicas.length === 0) {
            const colspan = esAdmin ? 9 : 8;
            tablaHistorial.innerHTML = `
                <tr>
                    <td colspan="${colspan}" class="text-center py-4">
                        <i class="fas fa-history fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No se encontraron solicitudes</p>
                    </td>
                </tr>`;
            return;
        }

        let html = '';
        practicas.forEach(p => {
            html += `<tr data-id="${p.id}">`;

            if (esAdmin) {
                html += `<td><small>${p.docente}</small></td>`;
            }

            html += `
                <td><small>${p.grupo}</small></td>
                <td><small>${p.laboratorio}</small></td>
                <td><small>${p.materia}</small></td>
                <td><small>${p.nombre_practica}</small></td>
                <td><span class="estatus-badge ${estatusClase(p.estatus)}">${p.estatus}</span></td>
                <td><small>${p.fecha_solicitud}</small></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-info btn-detalles" data-id="${p.id}">
                        <i class="fas fa-eye"></i> Ver
                    </button>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-editar" data-id="${p.id}">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                </td>
            </tr>`;
        });
        tablaHistorial.innerHTML = html;

        document.querySelectorAll('.btn-detalles').forEach(btn => {
            btn.addEventListener('click', function() {
                verDetalles(this.dataset.id);
            });
        });
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', function() {
                abrirEditar(this.dataset.id);
            });
        });
    }

    // ===== VER DETALLES =====
    function verDetalles(id) {
        fetch(`{{ url('historial/detalles') }}/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const p = data.practica;
                document.getElementById('detDocente').textContent = p.docente || '—';
                document.getElementById('detGrupo').textContent = p.grupo || '—';
                document.getElementById('detMateria').textContent = p.materia || '—';
                document.getElementById('detLaboratorio').textContent = p.laboratorio || '—';
                document.getElementById('detNombre').textContent = p.nombre || '—';
                document.getElementById('detNoActividad').textContent = p.no_actividad ?? '—';
                document.getElementById('detCompetencia').textContent = p.competencia || '—';
                document.getElementById('detAtributo').textContent = p.atributo || '—';
                document.getElementById('detNotas').textContent = p.notas || '—';

                // Materiales
                const matCont = document.getElementById('detMateriales');
                if (p.materiales && p.materiales.length > 0) {
                    matCont.innerHTML = p.materiales.map(m => `<span class="badge bg-success">${m}</span>`).join('');
                } else {
                    matCont.innerHTML = '<span class="text-muted">—</span>';
                }

                // Herramientas
                const herrCont = document.getElementById('detHerramientas');
                if (p.herramientas && p.herramientas.length > 0) {
                    herrCont.innerHTML = p.herramientas.map(h => `<span class="badge bg-danger">${h}</span>`).join('');
                } else {
                    herrCont.innerHTML = '<span class="text-muted">—</span>';
                }

                modalDetalles.show();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error al cargar detalles.'); });
    }

    // Variable global para guardar los cambios pendientes en el modal
    let cambiosPendientes = {};

    // ===== ABRIR MODAL =====
    function abrirEditar(id) {
        cambiosPendientes = {}; // Limpiar cambios
        fetch(`{{ url('historial/detalles') }}/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const p = data.practica;
                document.getElementById('editPassword').value = '';

                const cont = document.getElementById('listaHorasEditar');
                if (p.horas.length === 0) {
                    cont.innerHTML = '<p class="text-muted mb-0">No hay horas registradas.</p>';
                } else {
                    let html = '';
                    p.horas.forEach(h => {
                        const estatusColor = h.estatus === 'Reservada' ? 'primary'
                                        : h.estatus === 'Cumplida' ? 'success' : 'danger';
                        html += `
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" id="fila-hora-${h.id}">
                                <div>
                                    <strong>${h.hora_legible}</strong>
                                    <span class="text-muted"> — ${h.dia_legible} ${h.fecha_reserva}</span>
                                    <br>
                                    <span class="badge bg-${estatusColor} badge-estatus" data-id="${h.id}">${h.estatus}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <select class="form-select form-select-sm estatus-select" 
                                            data-id="${h.id}" 
                                            data-original="${h.estatus}"
                                            style="width: 140px;">
                                        <option value="Reservada" ${h.estatus === 'Reservada' ? 'selected' : ''}>Reservada</option>
                                        <option value="Cumplida" ${h.estatus === 'Cumplida' ? 'selected' : ''}>Cumplida</option>
                                        <option value="No cumplida" ${h.estatus === 'No cumplida' ? 'selected' : ''}>No cumplida</option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-hora" 
                                            data-id="${h.id}" 
                                            data-nombre="${h.hora_legible} (${h.dia_legible} ${h.fecha_reserva})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>`;
                    });
                    cont.innerHTML = html;

                    // Bind de selects
                    cont.querySelectorAll('.estatus-select').forEach(sel => {
                        sel.addEventListener('change', function() {
                            const idHora = this.dataset.id;
                            const nuevoEstatus = this.value;
                            const original = this.dataset.original;
                            
                            // Actualizar badge visual
                            //document.getElementById(`badge-estatus-${idHora}`).textContent = nuevoEstatus;
                            
                            // Guardar en pendientes
                            if (nuevoEstatus !== original) {
                                cambiosPendientes[idHora] = nuevoEstatus;
                            } else {
                                delete cambiosPendientes[idHora]; // Si vuelve al original, quitar
                            }
                        });
                    });

                    // Bind de botones eliminar
                    cont.querySelectorAll('.btn-eliminar-hora').forEach(btn => {
                        btn.addEventListener('click', function() {
                            eliminarHora(this.dataset.id, this.dataset.nombre);
                        });
                    });
                }

                modalEditar.show();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error al cargar detalles.'); });
    }

    // ===== GUARDAR CAMBIOS MASIVOS =====
    document.getElementById('btnGuardarEstatusMasivo').addEventListener('click', function() {
        const password = document.getElementById('editPassword').value;
        if (!password) {
            alert('⚠️ Ingrese su contraseña de administrador.');
            document.getElementById('editPassword').focus();
            return;
        }

        // Convertir el objeto de cambios pendientes a un array
        const cambios = Object.keys(cambiosPendientes).map(id => ({
            id: id,
            estatus: cambiosPendientes[id]
        }));

        if (cambios.length === 0) {
            alert('⚠️ No hay cambios pendientes por guardar.');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';

        fetch('{{ route("historial.actualizar.estatus") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                current_password: password,
                cambios: cambios // Enviamos el array completo
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                modalEditar.hide();
                listar();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i>Guardar Cambios';
        });
    });

    // ===== ELIMINAR HORA INDIVIDUAL =====
    function eliminarHora(id, nombre) {
        if (!confirm(`¿Eliminar la hora "${nombre}"? Esta acción no se puede deshacer.`)) return;

        const password = document.getElementById('editPassword').value;
        if (!password) {
            alert('⚠️ Ingrese su contraseña para eliminar.');
            return;
        }

        fetch(`{{ url('historial') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ current_password: password })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                modalEditar.hide();
                listar();
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(e => { console.error(e); alert('Error de conexión.'); });
    }

    // ===== FILTROS =====
    btnFiltrar.addEventListener('click', listar);
    filtroBusqueda.addEventListener('keypress', e => { if (e.key === 'Enter') { e.preventDefault(); listar(); } });
    filtroEstatus.addEventListener('change', listar);
    filtroMateria.addEventListener('change', listar);
    filtroLaboratorio.addEventListener('change', function() {
        cargarMaterias(this.value || null);
        listar();
    });

    btnLimpiarFiltros.addEventListener('click', function() {
        filtroBusqueda.value = '';
        filtroLaboratorio.value = '';
        filtroEstatus.value = '';
        cargarMaterias(null);
        listar();
    });

    // ===== INICIALIZAR =====
    cargarLaboratorios();
    cargarMaterias(null);
    listar();
});
</script>
@endpush