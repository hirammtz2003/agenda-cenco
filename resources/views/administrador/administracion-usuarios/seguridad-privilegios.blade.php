@extends('layouts.app')

@section('title', 'Seguridad y Privilegios de Usuario - SGGDI')

@php
    use App\Helpers\PrivilegiosHelper;
    $usuarioActual = Auth::user();

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
            'name' => 'Seguridad y Privilegios de Usuario',
            'url' => null,
            'icon' => 'fa-solid fa-shield-alt'
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
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
    .search-result-item.selected {
        background-color: #e3f2fd;
    }
    .search-result-item.highlighted {
        background-color: #ffc107 !important;
        color: #212529;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
        background-color: #e9ecef;
        padding: 0.5rem;
        border-radius: 5px;
    }
    .info-value {
        padding: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    .privilegios-table {
        width: 100%;
        border-collapse: collapse;
    }
    .privilegios-table th {
        background-color: #343a40;
        color: white;
        padding: 10px;
        text-align: center;
    }
    .privilegios-table td {
        padding: 8px;
        text-align: center;
        border: 1px solid #dee2e6;
    }
    .privilegios-table td:first-child {
        font-weight: 600;
        background-color: #f8f9fa;
        text-align: left;
        padding-left: 15px;
    }
    .privilegios-table input[type="radio"] {
        transform: scale(1.2);
        cursor: pointer;
    }
    .privilegios-table input[type="radio"]:checked {
        accent-color: #ffc107;
    }
    .privilegios-table input[type="radio"]:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }
    .email-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 0 0 8px 8px;
        max-height: 150px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    .email-suggestion-item {
        padding: 8px 15px;
        cursor: pointer;
    }
    .email-suggestion-item:hover {
        background-color: #f0f0f0;
    }
    .email-suggestion-item.highlighted {
        background-color: #ffc107 !important;
        color: #212529;
    }
    .badge-custom {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    .disabled-section {
        opacity: 0.6;
        pointer-events: none;
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
                <i class="fa-solid fa-shield-alt me-2 text-warning"></i>
                SEGURIDAD Y PRIVILEGIOS DE USUARIO
            </h2>
        </div>
    </div>
    
    <div class="module-section">
        <!-- Búsqueda de usuarios -->
        <div class="row mb-4">
            <div class="col-md-4 position-relative">
                <label class="form-label"><i class="fas fa-search"></i> Buscar Usuario:</label>
                <div class="input-group">
                    <input type="text" 
                           id="busquedaInput" 
                           class="form-control" 
                           placeholder="Nombre, apellidos, número de empleado..."
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')">
                    <button class="btn btn-primary" type="button" id="btnBuscar">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-outline-secondary" type="button" id="btnLimpiar" style="display:none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="searchResults" class="search-results-dropdown"></div>
            </div>
        </div>

        <!-- Información del usuario seleccionado -->
        <div id="usuarioSeleccionado" style="display: none;">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Usuario seleccionado</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-2">
                                <div class="col-md-4 info-label">Nombre completo:</div>
                                <div class="col-md-8 info-value" id="displayNombreCompleto"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-2">
                                <div class="col-md-4 info-label">Número de empleado:</div>
                                <div class="col-md-8 info-value" id="displayNumEmpleado"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="seguridadForm">
            @csrf
            <input type="hidden" name="user_id" id="userId">

            <h5 class="mb-4"><i class="fa-solid fa-unlock-keyhole"></i> EDICIÓN DE TIPO, ESTATUS Y PRIVILEGIOS</h5>

            <div class="row mb-4">
                <div class="col-md-2">
                    <label class="form-label">Tipo de Usuario</label>
                    <select name="tipo" id="tipoSelect" class="form-select" disabled>
                        <option value="">—Seleccione—</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Directivo">Directivo</option>
                        <option value="Docente">Docente</option>
                        <option value="Trabajo Social">Trabajo Social</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" id="estatusSelect" class="form-select" disabled>
                        <option value="">—Seleccione—</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-5">
                    <label class="form-label">Privilegios</label>
                    <table class="privilegios-table">
                        <thead>
                            <tr>
                                <th>↓ Nivel \ Permiso →</th>
                                <th>Restringido</th>
                                <th>Consulta</th>
                                <th>Gestión</th>
                            </tr>
                        </thead>
                        <tbody id="privilegiosTableBody">
                            @php
                                $niveles = [
                                    0 => 'Administración',
                                    1 => 'General',
                                    2 => 'Grado',
                                    3 => 'Grupo',
                                    4 => 'Individual'
                                ];
                            @endphp
                            @foreach($niveles as $index => $nivel)
                            <tr>
                                <td>{{ $nivel }}</td>
                                <td>
                                    <input type="radio" 
                                           name="privilegio_{{ $index }}" 
                                           value="N"
                                           data-nivel="{{ $index }}"
                                           class="privilegio-radio"
                                           disabled>
                                </td>
                                <td>
                                    <input type="radio" 
                                           name="privilegio_{{ $index }}" 
                                           value="C"
                                           data-nivel="{{ $index }}"
                                           class="privilegio-radio"
                                           disabled>
                                </td>
                                <td>
                                    <input type="radio" 
                                           name="privilegio_{{ $index }}" 
                                           value="G"
                                           data-nivel="{{ $index }}"
                                           class="privilegio-radio"
                                           disabled>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <input type="hidden" name="privilegios" id="privilegiosHidden">
                </div>
            </div>

            <h5 class="mb-4"><i class="fa-solid fa-key"></i> ACTIVACIÓN DE CONTRASEÑA TEMPORAL</h5>
        
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="generarPasswordSwitch" name="generar_password_temporal" value="1" disabled>
                        <label class="form-check-label" for="generarPasswordSwitch">
                            Habilitar nueva contraseña temporal
                        </label>
                    </div>
                </div>
            </div>

            <div class="row mb-4" id="emailSection" style="display: none;">
                <div class="col-md-6 position-relative">
                    <label class="form-label">Email destinatario</label>
                    <input type="email" 
                           id="emailDestino"
                           name="email_destino" 
                           class="form-control"
                           placeholder="Ingrese el email para enviar la contraseña"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')"
                           disabled>
                    <div id="emailSuggestions" class="email-suggestions"></div>
                    <small class="text-muted">Puede seleccionar de los emails del usuario o ingresar uno nuevo</small>
                </div>
            </div>

            <!-- Panel de contraseña de admin y guardado -->
            <div class="row mt-4 pt-4 border-top">
                <div class="col-md-4">
                    <label for="current_password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Contraseña de Administrador *
                    </label>
                    <input type="password" 
                           class="form-control" 
                           id="currentPassword" 
                           placeholder="Ingrese su contraseña para autorizar"
                           required
                           disabled>
                    <small class="text-muted">Requerida para confirmar la operación</small>
                </div>
                <div class="col-md-8 d-flex align-items-end justify-content-end">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning" id="btnGuardar" disabled>
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCancelar" disabled>
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                        </a>
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
    // Elementos del DOM
    const busquedaInput = document.getElementById('busquedaInput');
    const btnBuscar = document.getElementById('btnBuscar');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const searchResults = document.getElementById('searchResults');
    const usuarioSeleccionado = document.getElementById('usuarioSeleccionado');
    const userId = document.getElementById('userId');
    const tipoSelect = document.getElementById('tipoSelect');
    const estatusSelect = document.getElementById('estatusSelect');
    const privilegiosHidden = document.getElementById('privilegiosHidden');
    const generarPasswordSwitch = document.getElementById('generarPasswordSwitch');
    const emailSection = document.getElementById('emailSection');
    const emailDestino = document.getElementById('emailDestino');
    const emailSuggestions = document.getElementById('emailSuggestions');
    const currentPassword = document.getElementById('currentPassword');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnCancelar = document.getElementById('btnCancelar');
    const privilegioRadios = document.querySelectorAll('.privilegio-radio');

    let usuarioActual = null;
    let privilegiosOriginales = null;
    let timeoutId = null;
    let resultadosBusqueda = [];
    let selectedResultIndex = -1;
    let selectedEmailIndex = -1;
    let emailSuggestionsList = [];

    // Estado inicial: todo deshabilitado
    function deshabilitarTodo() {
        tipoSelect.disabled = true;
        estatusSelect.disabled = true;
        privilegioRadios.forEach(radio => radio.disabled = true);
        generarPasswordSwitch.disabled = true;
        emailDestino.disabled = true;
        currentPassword.disabled = true;
        btnGuardar.disabled = true;
        btnCancelar.disabled = true;
        
        // Limpiar valores
        tipoSelect.value = '';
        estatusSelect.value = '';
        privilegioRadios.forEach(radio => radio.checked = false);
        generarPasswordSwitch.checked = false;
        emailDestino.value = '';
        currentPassword.value = '';
        emailSection.style.display = 'none';
    }

    function habilitarTodo() {
        tipoSelect.disabled = false;
        estatusSelect.disabled = false;
        privilegioRadios.forEach(radio => radio.disabled = false);
        generarPasswordSwitch.disabled = false;
        emailDestino.disabled = false;
        currentPassword.disabled = false;
        btnGuardar.disabled = false;
        btnCancelar.disabled = false;
    }

    // Estado inicial
    deshabilitarTodo();

    // Búsqueda en tiempo real
    busquedaInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => realizarBusqueda(query), 300);
    });

    btnBuscar.addEventListener('click', function() {
        const query = busquedaInput.value.trim();
        if (query.length >= 2) {
            realizarBusqueda(query);
        }
    });

    function realizarBusqueda(query) {
        fetch('{{ route("admin.usuarios.buscar-seguridad") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.usuarios.length > 0) {
                resultadosBusqueda = data.usuarios;
                mostrarResultados(resultadosBusqueda);
            } else {
                resultadosBusqueda = [];
                searchResults.innerHTML = '<div class="search-result-item">No se encontraron resultados</div>';
                searchResults.style.display = 'block';
            }
        });
    }

    function mostrarResultados(usuarios) {
        searchResults.innerHTML = '';
        usuarios.forEach((user, index) => {
            const div = document.createElement('div');
            div.className = 'search-result-item';
            div.dataset.index = index;
            div.innerHTML = `<strong>${user.nombre_completo}</strong>`;
            div.addEventListener('click', () => seleccionarUsuario(user.id));
            searchResults.appendChild(div);
        });
        searchResults.style.display = 'block';
        selectedResultIndex = -1;
    }

    // Navegación con teclas para resultados de búsqueda
    busquedaInput.addEventListener('keydown', function(e) {
        if (searchResults.style.display !== 'block' || resultadosBusqueda.length === 0) return;

        const items = document.querySelectorAll('.search-result-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedResultIndex = (selectedResultIndex + 1) % items.length;
            updateHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedResultIndex = (selectedResultIndex - 1 + items.length) % items.length;
            updateHighlight(items);
        } else if (e.key === 'Enter' && selectedResultIndex >= 0) {
            e.preventDefault();
            const selectedId = resultadosBusqueda[selectedResultIndex].id;
            seleccionarUsuario(selectedId);
        }
    });

    function updateHighlight(items) {
        items.forEach((item, index) => {
            if (index === selectedResultIndex) {
                item.classList.add('highlighted');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('highlighted');
            }
        });
    }

    function seleccionarUsuario(id) {
        fetch(`{{ url('admin/usuarios') }}/${id}/obtener-seguridad`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                usuarioActual = data.usuario;
                habilitarTodo();
                cargarUsuarioEnFormulario(usuarioActual);
                searchResults.style.display = 'none';
                busquedaInput.value = usuarioActual.nombre_completo;
                btnLimpiar.style.display = 'inline-block';
            }
        });
    }

    function cargarUsuarioEnFormulario(user) {
        userId.value = user.id;
        document.getElementById('displayNombreCompleto').textContent = user.nombre_completo;
        document.getElementById('displayNumEmpleado').textContent = user.num_empleado;
        
        tipoSelect.value = user.tipo;
        estatusSelect.value = user.estatus ? '1' : '0';
        
        // Cargar privilegios
        const privilegios = user.privilegios || 'NNNNN';
        privilegiosOriginales = privilegios;
        
        for (let i = 0; i < 5; i++) {
            const radios = document.getElementsByName(`privilegio_${i}`);
            radios.forEach(radio => {
                if (radio.value === privilegios[i]) {
                    radio.checked = true;
                }
            });
        }
        
        actualizarCadenaPrivilegios();
        usuarioSeleccionado.style.display = 'block';
    }

    // Actualizar campo oculto con la cadena de privilegios
    function actualizarCadenaPrivilegios() {
        let cadena = '';
        for (let i = 0; i < 5; i++) {
            const radios = document.getElementsByName(`privilegio_${i}`);
            let seleccionado = 'N';
            radios.forEach(radio => {
                if (radio.checked) {
                    seleccionado = radio.value;
                }
            });
            cadena += seleccionado;
        }
        privilegiosHidden.value = cadena;
    }

    // Función para propagar cambios a niveles inferiores
    function propagarCambios(nivelInicio, valor) {
        for (let i = nivelInicio + 1; i <= 4; i++) {
            const radiosInferiores = document.getElementsByName(`privilegio_${i}`);
            radiosInferiores.forEach(radio => {
                if (radio.value === valor) {
                    radio.checked = true;
                }
            });
        }
    }

    // Event listeners para los radios
    privilegioRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const nivel = parseInt(this.dataset.nivel);
            const valor = this.value;
            
            // Propagar a niveles inferiores SIEMPRE
            propagarCambios(nivel, valor);
            
            actualizarCadenaPrivilegios();
        });
    });

    // Cuando cambia el tipo, actualizar privilegios
    tipoSelect.addEventListener('change', function() {
        if (!usuarioActual) return;
        
        const nuevoTipo = this.value;
        if (!nuevoTipo) return;
        
        const privilegiosDefault = {
            'Administrador': 'GNNNN',
            'Directivo': 'NCCCC',
            'Docente': 'NNNCC',
            'Trabajo Social': 'NNGGG'
        };
        
        const nuevaCadena = privilegiosDefault[nuevoTipo];
        for (let i = 0; i < 5; i++) {
            const radios = document.getElementsByName(`privilegio_${i}`);
            radios.forEach(radio => {
                if (radio.value === nuevaCadena[i]) {
                    radio.checked = true;
                }
            });
        }
        actualizarCadenaPrivilegios();
    });

    // Switch de contraseña temporal
    generarPasswordSwitch.addEventListener('change', function() {
        if (this.checked && usuarioActual) {
            emailSection.style.display = 'block';
            emailDestino.value = usuarioActual.email_personal;
            mostrarSugerenciasEmail('');
        } else {
            emailSection.style.display = 'none';
            emailDestino.value = '';
        }
    });

    // Función para mostrar sugerencias de email
    function mostrarSugerenciasEmail(valor) {
        if (!usuarioActual) return;
        
        const emails = [
            usuarioActual.email_personal,
            usuarioActual.email_institucional
        ];
        
        emailSuggestionsList = emails.filter(e => e.toLowerCase().includes(valor.toLowerCase()));
        
        if (emailSuggestionsList.length > 0) {
            emailSuggestions.innerHTML = '';
            emailSuggestionsList.forEach((email, index) => {
                const div = document.createElement('div');
                div.className = 'email-suggestion-item';
                div.dataset.index = index;
                div.textContent = email;
                div.addEventListener('click', () => {
                    emailDestino.value = email;
                    emailSuggestions.style.display = 'none';
                });
                emailSuggestions.appendChild(div);
            });
            emailSuggestions.style.display = 'block';
            selectedEmailIndex = -1;
        } else {
            emailSuggestions.style.display = 'none';
        }
    }

    // Sugerencias de email
    emailDestino.addEventListener('input', function() {
        const valor = this.value;
        mostrarSugerenciasEmail(valor);
    });

    // Navegación con teclas para sugerencias de email
    emailDestino.addEventListener('keydown', function(e) {
        if (emailSuggestions.style.display !== 'block' || emailSuggestionsList.length === 0) return;

        const items = document.querySelectorAll('.email-suggestion-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedEmailIndex = (selectedEmailIndex + 1) % items.length;
            updateEmailHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedEmailIndex = (selectedEmailIndex - 1 + items.length) % items.length;
            updateEmailHighlight(items);
        } else if (e.key === 'Enter' && selectedEmailIndex >= 0) {
            e.preventDefault();
            emailDestino.value = emailSuggestionsList[selectedEmailIndex];
            emailSuggestions.style.display = 'none';
        }
    });

    function updateEmailHighlight(items) {
        items.forEach((item, index) => {
            if (index === selectedEmailIndex) {
                item.classList.add('highlighted');
            } else {
                item.classList.remove('highlighted');
            }
        });
    }

    // Guardar cambios
    btnGuardar.addEventListener('click', function() {
        if (!usuarioActual) {
            alert('Debe seleccionar un usuario primero.');
            return;
        }
        
        if (!currentPassword.value) {
            alert('Debe ingresar su contraseña de administrador.');
            currentPassword.focus();
            return;
        }

        const formData = {
            current_password: currentPassword.value,
            user_id: userId.value,
            tipo: tipoSelect.value || undefined,
            estatus: estatusSelect.value,
            privilegios: privilegiosHidden.value,
            generar_password_temporal: generarPasswordSwitch.checked ? 1 : 0,
            email_destino: emailDestino.value || undefined
        };

        // Remover undefined
        Object.keys(formData).forEach(key => 
            formData[key] === undefined && delete formData[key]
        );

        console.log('Enviando datos:', formData); // Para debugging

        fetch('{{ route("admin.usuarios.guardar-seguridad") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta:', data); // Para debugging
            if (data.success) {
                alert(data.message);
                if (data.detalles) {
                    data.detalles.forEach(msg => alert(msg));
                }
                // Actualizar datos mostrados
                if (data.usuario) {
                    usuarioActual.tipo = data.usuario.tipo;
                    usuarioActual.estatus = data.usuario.estatus;
                    usuarioActual.privilegios = data.usuario.privilegios;
                }
                currentPassword.value = '';
                generarPasswordSwitch.checked = false;
                emailSection.style.display = 'none';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al guardar.');
        });
    });

    // Limpiar selección
    btnLimpiar.addEventListener('click', function() {
        usuarioActual = null;
        userId.value = '';
        busquedaInput.value = '';
        usuarioSeleccionado.style.display = 'none';
        deshabilitarTodo();
        privilegiosHidden.value = '';
        btnLimpiar.style.display = 'none';
    });

    // Cancelar edición
    btnCancelar.addEventListener('click', function() {
        if (usuarioActual) {
            cargarUsuarioEnFormulario(usuarioActual);
        }
        currentPassword.value = '';
        generarPasswordSwitch.checked = false;
        emailSection.style.display = 'none';
    });

    // Ocultar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!emailDestino.contains(e.target) && !emailSuggestions.contains(e.target)) {
            emailSuggestions.style.display = 'none';
        }
        if (!busquedaInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});
</script>
@endpush