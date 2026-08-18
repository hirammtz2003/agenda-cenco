@extends('layouts.app')

@section('title', 'Consulta y Edición de Usuarios - SGGDI')

@php
    use App\Helpers\PrivilegiosHelper;
    $usuarioActual = Auth::user();
    $puedeEditar = PrivilegiosHelper::puedeEditar($usuarioActual);
    $puedeConsultar = PrivilegiosHelper::puedeConsultar($usuarioActual);
    
    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Administración de Usuarios',
            'url' => route('admin.usuarios.index'),
            'icon' => 'fa-users-cog'
        ],
        [
            'name' => 'Consulta y Edición de Usuarios',
            'url' => null,
            'icon' => 'fa-user-edit'
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
    .user-row.editing {
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
    .badge-custom {
        font-size: 0.85rem;
        padding: 0.5rem 0.8rem;
    }
    .filter-badge {
        background-color: #e9ecef;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-block;
    }
    .privilegios-container {
        max-width: 300px;
    }
    .action-buttons {
        white-space: nowrap;
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
                <i class="fas fa-user-edit me-2 text-secondary"></i>
                CONSULTA Y EDICIÓN DE USUARIOS
            </h2>
        </div>
    </div>
    
    @if(!$puedeConsultar)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> No tienes permisos para consultar usuarios.
        </div>
    @else
    <!-- Filtros y búsqueda -->
    <div class="module-section">
        <form method="GET" action="{{ route('admin.usuarios.consulta') }}" id="filterForm">
            <div class="row mb-3">
                <div class="col-md-5">
                    <label class="form-label"><i class="fas fa-search"></i> Buscar:</label>
                    <div class="input-group">
                        <input type="text" name="busqueda" class="form-control" 
                            placeholder="Nombre, apellidos, email, num. empleado..." 
                            value="{{ request('busqueda') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request()->anyFilled(['busqueda', 'tipo', 'orden', 'mostrar_inactivos']))
                            <a href="{{ route('admin.usuarios.consulta') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-filter"></i> Tipo:</label>
                    <select name="tipo" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="todos" {{ request('tipo') == 'todos' ? 'selected' : '' }}>—Todos los tipos—</option>
                        <option value="Administrador" {{ request('tipo') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="Directivo" {{ request('tipo') == 'Directivo' ? 'selected' : '' }}>Directivo</option>
                        <option value="Docente" {{ request('tipo') == 'Docente' ? 'selected' : '' }}>Docente</option>
                        <option value="Trabajo Social" {{ request('tipo') == 'Trabajo Social' ? 'selected' : '' }}>Trabajo Social</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-sort"></i> Ordenar por:</label>
                    <select name="orden" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="id_asc" {{ request('orden') == 'id_asc' ? 'selected' : '' }}>ID (ascendente)</option>
                        <option value="nombre_asc" {{ request('orden') == 'nombre_asc' ? 'selected' : '' }}>Nombre(s)</option>
                        <option value="apellido_asc" {{ request('orden') == 'apellido_asc' ? 'selected' : '' }}>Primer Apellido</option>
                        <option value="num_empleado_asc" {{ request('orden') == 'num_empleado_asc' ? 'selected' : '' }}>Número de Empleado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" id="mostrar_inactivos" 
                               name="mostrar_inactivos" value="1" 
                               {{ request('mostrar_inactivos') ? 'checked' : '' }}
                               onchange="document.getElementById('filterForm').submit()">
                        <label class="form-check-label" for="mostrar_inactivos">
                            <i class="fas fa-eye-slash"></i> Mostrar usuarios inactivos
                        </label>
                    </div>
                </div>
            </div>
        </form>

        <!-- Resultados de la búsqueda -->
        <div class="row mb-3">
            <div class="col-12">
                <span class="filter-badge">
                    <i class="fas fa-users"></i> {{ $totalEncontrados }} usuario(s) encontrado(s)
                </span>
                @if(request('busqueda'))
                    <span class="filter-badge">
                        <i class="fas fa-search"></i> Buscando: "{{ request('busqueda') }}"
                    </span>
                @endif
                @if(request('tipo') && request('tipo') != 'todos')
                    <span class="filter-badge">
                        <i class="fas fa-user-tag"></i> Tipo: {{ request('tipo') }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Formulario principal de edición -->
        <form method="POST" action="{{ route('admin.usuarios.update-bulk') }}" id="bulkEditForm">
            @csrf
            <input type="hidden" name="current_password" id="current_password_hidden">

            <!-- Tabla de Usuarios con scroll -->
            <div class="table-container mb-5">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Núm. Empleado</th>
                            <th>Nombre(s)</th>
                            <th>Primer Apellido</th>
                            <th>Segundo Apellido</th>
                            <th>Email Personal</th>
                            <th>Email Institucional</th>
                            <th>Teléfono</th>                            
                            <th>Tipo</th>
                            <th>Privilegios</th>
                            <th>Estatus</th>
                            @if($puedeEditar)
                                <th>Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $user)
                            <tr id="user-{{ $user->id }}" class="user-row" data-id="{{ $user->id }}">
                                <td>
                                    <span class="editable-field" data-field="num_empleado">{{ $user->num_empleado }}</span>
                                    <input type="number" class="edit-input" data-field="num_empleado" 
                                           value="{{ $user->num_empleado }}">
                                </td>
                                <td>
                                    <span class="editable-field" data-field="nombre">{{ $user->nombre }}</span>
                                    <input type="text" class="edit-input" data-field="nombre" 
                                           value="{{ $user->nombre }}" maxlength="30">
                                </td>
                                <td>
                                    <span class="editable-field" data-field="apellido1">{{ $user->apellido1 }}</span>
                                    <input type="text" class="edit-input" data-field="apellido1" 
                                           value="{{ $user->apellido1 }}" maxlength="20">
                                </td>
                                <td>
                                    <span class="editable-field" data-field="apellido2">{{ $user->apellido2 ?? '—' }}</span>
                                    <input type="text" class="edit-input" data-field="apellido2" 
                                           value="{{ $user->apellido2 }}" maxlength="20" placeholder="Opcional">
                                </td>
                                <td>
                                    <span class="editable-field" data-field="email_personal">{{ $user->email_personal }}</span>
                                    <input type="email" class="edit-input" data-field="email_personal" 
                                           value="{{ $user->email_personal }}" maxlength="50">
                                </td>
                                <td>
                                    <span class="editable-field" data-field="email_institucional">{{ $user->email_institucional }}</span>
                                    <input type="email" class="edit-input" data-field="email_institucional" 
                                           value="{{ $user->email_institucional }}" maxlength="50">
                                </td>
                                <td>
                                    <span class="editable-field" data-field="telefono">{{ $user->telefono }}</span>
                                    <input type="tel" class="edit-input" data-field="telefono" 
                                           value="{{ $user->telefono }}" maxlength="10" pattern="[0-9]{10}">
                                </td>                                
                                <td>
                                    <div class="col-md-9 info-value">
                                        @php
                                            $badgeClass = $user->tipo === 'Administrador' ? 'bg-danger' : 
                                                        ($user->tipo === 'Directivo' ? 'bg-warning' : 
                                                        ($user->tipo === 'Docente' ? 'bg-primary' : 'bg-info'));
                                        @endphp
                                        <span class="badge {{ $badgeClass }} badge-custom">
                                            {{ $user->tipo }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-9 info-value">
                                        @if($user->privilegios[0] == "G")
                                            <span class="badge bg-primary">Administración general del Sistema</span>
                                        @elseif($user->privilegios[0] == "C")
                                            <span class="badge bg-primary">Sólo consulta de Usuarios</span>    
                                        @endif
                                                                                        
                                        @if($user->privilegios[1] == "C")
                                            <span class="badge bg-secondary">Sólo consulta general de Alumnos</span>
                                        @elseif($user->privilegios[2] == "C")
                                            <span class="badge bg-danger">Consulta por Grado</span>
                                        @elseif($user->privilegios[3] == "C")
                                            <span class="badge bg-warning">Consulta por Grupo</span>
                                        @elseif($user->privilegios[4] == "C")
                                            <span class="badge bg-success">Consulta individual de Alumnos</span>
                                        @endif

                                        @if($user->privilegios[1] == "G")
                                            <span class="badge bg-secondary">Gestión general de Alumnos</span>
                                        @elseif($user->privilegios[2] == "G")
                                            <span class="badge bg-danger">Gestión por Grado</span>
                                        @elseif($user->privilegios[3] == "G")
                                            <span class="badge bg-warning">Gestión por Grupo</span>    
                                        @elseif($user->privilegios[4] == "G")
                                            <span class="badge bg-success">Gestión individual de Alumnos</span>                           
                                        @endif 
                                    </div>
                                </td>
                                <td>
                                    @if($user->estatus)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                @if($puedeEditar)
                                    <td class="action-buttons">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-row-btn" 
                                                    title="Editar">
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
                                            @if($user->id !== Auth::id())
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-row-btn" 
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $puedeEditar ? 11 : 10 }}" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No se encontraron usuarios</h5>
                                    <p class="text-muted">Intenta con otros filtros de búsqueda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($puedeEditar && $usuarios->isNotEmpty())
                <!-- Panel de contraseña y guardado -->
                <div class="row mt-4 pt-4 border-top">
                    <div class="col-md-4">
                        <label for="current_password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Contraseña de Administrador *
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   placeholder="Ingrese su contraseña para guardar cambios"
                                   required>
                        </div>
                        <small class="text-muted">Requerida para confirmar cualquier modificación</small>
                    </div>
                    <div class="col-md-8 d-flex align-items-end justify-content-end">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" id="guardarTodosBtn" disabled>
                                <i class="fas fa-save me-2"></i>Guardar Todos los Cambios
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="cancelarTodoBtn" disabled>
                                <i class="fas fa-times me-2"></i>Cancelar Todo
                            </button>
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-primary">
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
    @if($puedeEditar)
    // Variables de estado
    let filasEnEdicion = new Set();
    const currentPasswordInput = document.getElementById('current_password');
    const guardarTodosBtn = document.getElementById('guardarTodosBtn');
    const cancelarTodoBtn = document.getElementById('cancelarTodoBtn');
    const bulkEditForm = document.getElementById('bulkEditForm');

    // Función para actualizar estado de botones globales
    function actualizarBotonesGlobales() {
        const hayEdiciones = filasEnEdicion.size > 0;
        guardarTodosBtn.disabled = !hayEdiciones || !currentPasswordInput.value;
        cancelarTodoBtn.disabled = !hayEdiciones;
        
        // Habilitar/deshabilitar botones de eliminar según si hay ediciones
        document.querySelectorAll('.delete-row-btn').forEach(btn => {
            btn.disabled = hayEdiciones;
        });
    }

    // Validar contraseña en tiempo real
    currentPasswordInput.addEventListener('input', actualizarBotonesGlobales);

    // Editar fila
    document.querySelectorAll('.edit-row-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.user-row');
            const rowId = row.dataset.id;
            
            if (filasEnEdicion.has(rowId)) return;
            
            // Activar modo edición en la fila
            row.classList.add('editing');
            filasEnEdicion.add(rowId);
            
            // Mostrar/ocultar botones
            this.style.display = 'none';
            row.querySelector('.save-row-btn').style.display = 'inline-block';
            row.querySelector('.cancel-row-btn').style.display = 'inline-block';
            
            actualizarBotonesGlobales();
        });
    });

    // Cancelar edición de fila
    document.querySelectorAll('.cancel-row-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.user-row');
            const rowId = row.dataset.id;
            
            // Restaurar valores originales
            row.querySelectorAll('.edit-input').forEach(input => {
                const field = input.dataset.field;
                const originalValue = input.defaultValue;
                input.value = originalValue;
                row.querySelector(`.editable-field[data-field="${field}"]`).textContent = 
                    field.includes('email') ? originalValue : (originalValue || '-');
            });
            
            // Salir del modo edición
            row.classList.remove('editing');
            filasEnEdicion.delete(rowId);
            
            // Restaurar botones
            row.querySelector('.edit-row-btn').style.display = 'inline-block';
            row.querySelector('.save-row-btn').style.display = 'none';
            row.querySelector('.cancel-row-btn').style.display = 'none';
            
            // Reactivar otros botones de edición si no hay filas en edición
            if (filasEnEdicion.size === 0) {
                document.querySelectorAll('.edit-row-btn').forEach(b => b.disabled = false);
            }
            
            actualizarBotonesGlobales();
        });
    });

    // Guardar fila individual
    document.querySelectorAll('.save-row-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!currentPasswordInput.value) {
                alert('Debes ingresar tu contraseña para guardar cambios.');
                currentPasswordInput.focus();
                return;
            }
            
            const row = this.closest('.user-row');
            const rowId = row.dataset.id;
            
            // Recopilar datos de la fila
            const userData = {
                id: rowId,
                nombre: row.querySelector('input[data-field="nombre"]').value,
                apellido1: row.querySelector('input[data-field="apellido1"]').value,
                apellido2: row.querySelector('input[data-field="apellido2"]').value,
                email_personal: row.querySelector('input[data-field="email_personal"]').value,
                email_institucional: row.querySelector('input[data-field="email_institucional"]').value,
                telefono: row.querySelector('input[data-field="telefono"]').value,
                num_empleado: row.querySelector('input[data-field="num_empleado"]').value,
            };

            // Enviar solo esta fila
            fetch('{{ route("admin.usuarios.update-bulk") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_password: currentPasswordInput.value,
                    usuarios: [userData]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar valores por defecto
                    row.querySelectorAll('.edit-input').forEach(input => {
                        input.defaultValue = input.value;
                    });
                    
                    // Actualizar textos mostrados
                    row.querySelectorAll('.editable-field').forEach(span => {
                        const field = span.dataset.field;
                        const input = row.querySelector(`input[data-field="${field}"]`);
                        span.textContent = input.value || '-';
                    });
                    
                    // Salir del modo edición
                    row.classList.remove('editing');
                    filasEnEdicion.delete(rowId);
                    
                    row.querySelector('.edit-row-btn').style.display = 'inline-block';
                    row.querySelector('.save-row-btn').style.display = 'none';
                    row.querySelector('.cancel-row-btn').style.display = 'none';
                    
                    if (filasEnEdicion.size === 0) {
                        document.querySelectorAll('.edit-row-btn').forEach(b => b.disabled = false);
                    }
                    
                    currentPasswordInput.value = '';
                    actualizarBotonesGlobales();
                    
                    // Mostrar mensaje de éxito
                    alert(data.message || 'Usuario actualizado correctamente.');
                } else {
                    alert('Error al guardar: ' + (data.error || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al guardar.');
            });
        });
    });

    // Guardar todos los cambios
    guardarTodosBtn.addEventListener('click', function() {
        if (!currentPasswordInput.value) {
            alert('Debes ingresar tu contraseña para guardar cambios.');
            currentPasswordInput.focus();
            return;
        }
        
        const usuariosData = [];
        
        filasEnEdicion.forEach(rowId => {
            const row = document.getElementById(`user-${rowId}`);
            if (row) {
                usuariosData.push({
                    id: rowId,
                    nombre: row.querySelector('input[data-field="nombre"]').value,
                    apellido1: row.querySelector('input[data-field="apellido1"]').value,
                    apellido2: row.querySelector('input[data-field="apellido2"]').value,
                    email_personal: row.querySelector('input[data-field="email_personal"]').value,
                    email_institucional: row.querySelector('input[data-field="email_institucional"]').value,
                    telefono: row.querySelector('input[data-field="telefono"]').value,
                    num_empleado: row.querySelector('input[data-field="num_empleado"]').value,
                });
            }
        });

        if (usuariosData.length === 0) {
            alert('No hay cambios para guardar.');
            return;
        }

        if (!confirm('¿Estás seguro de guardar los cambios en ' + usuariosData.length + ' usuario(s)?')) {
            return;
        }

        fetch('{{ route("admin.usuarios.update-bulk") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                current_password: currentPasswordInput.value,
                usuarios: usuariosData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar todas las filas editadas
                filasEnEdicion.forEach(rowId => {
                    const row = document.getElementById(`user-${rowId}`);
                    if (row) {
                        // Actualizar valores por defecto
                        row.querySelectorAll('.edit-input').forEach(input => {
                            input.defaultValue = input.value;
                        });
                        
                        // Actualizar textos mostrados
                        row.querySelectorAll('.editable-field').forEach(span => {
                            const field = span.dataset.field;
                            const input = row.querySelector(`input[data-field="${field}"]`);
                            span.textContent = input.value || '-';
                        });
                        
                        // Salir del modo edición
                        row.classList.remove('editing');
                        row.querySelector('.edit-row-btn').style.display = 'inline-block';
                        row.querySelector('.save-row-btn').style.display = 'none';
                        row.querySelector('.cancel-row-btn').style.display = 'none';
                    }
                });
                
                filasEnEdicion.clear();
                document.querySelectorAll('.edit-row-btn').forEach(b => b.disabled = false);
                currentPasswordInput.value = '';
                actualizarBotonesGlobales();
                
                alert(data.message || 'Cambios guardados correctamente.');
                
                if (data.errors && data.errors.length > 0) {
                    console.warn('Errores:', data.errors);
                    alert('Algunos usuarios no pudieron actualizarse. Revisa la consola.');
                }
            } else {
                alert('Error al guardar: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al guardar.');
        });
    });

    // Cancelar todas las ediciones
    cancelarTodoBtn.addEventListener('click', function() {
        if (filasEnEdicion.size === 0) return;
        
        if (!confirm('¿Cancelar todas las ediciones? Se perderán los cambios no guardados.')) {
            return;
        }
        
        filasEnEdicion.forEach(rowId => {
            const row = document.getElementById(`user-${rowId}`);
            if (row) {
                // Restaurar valores originales
                row.querySelectorAll('.edit-input').forEach(input => {
                    input.value = input.defaultValue;
                });
                
                // Salir del modo edición
                row.classList.remove('editing');
                row.querySelector('.edit-row-btn').style.display = 'inline-block';
                row.querySelector('.save-row-btn').style.display = 'none';
                row.querySelector('.cancel-row-btn').style.display = 'none';
                
                const deleteBtn = row.querySelector('.delete-row-btn');
                if (deleteBtn) deleteBtn.disabled = true;
            }
        });
        
        filasEnEdicion.clear();
        document.querySelectorAll('.edit-row-btn').forEach(b => b.disabled = false);
        actualizarBotonesGlobales();
    });

    // Eliminar usuario
    document.querySelectorAll('.delete-row-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!currentPasswordInput.value) {
                alert('Debes ingresar tu contraseña para eliminar.');
                currentPasswordInput.focus();
                return;
            }
            
            const row = this.closest('.user-row');
            const rowId = row.dataset.id;
            const nombre = row.querySelector('.editable-field[data-field="nombre"]').textContent;
            
            if (!confirm(`¿Estás seguro de eliminar al usuario ${nombre}? Esta acción no se puede deshacer.`)) {
                return;
            }
            
            fetch(`{{ url('admin/usuarios') }}/${rowId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_password: currentPasswordInput.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    alert(data.message || 'Usuario eliminado correctamente.');
                    
                    // Actualizar contador de resultados
                    location.reload(); // Recargar para actualizar contadores
                } else {
                    alert('Error al eliminar: ' + (data.error || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al eliminar.');
            });
        });
    });

    // Validación de teléfono en tiempo real
    document.querySelectorAll('input[data-field="telefono"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });
    });

    // Prevenir envío del formulario por Enter
    bulkEditForm.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });
    @endif
});
</script>
@endpush