@extends('layouts.app')

@section('title', 'Registro de Nuevos Usuarios - SGGDI')

@php
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
            'name' => 'Registro de Nuevos Usuarios',
            'url' => null,
            'icon' => 'fa-user-plus'
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
    .form-control[readonly] {
        background-color: #e9ecef;
        opacity: 1;
    }
    .counter-badge {
        font-size: 0.8rem;
        margin-left: 5px;
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
                <i class="fas fa-user-plus me-2 text-danger"></i>
                REGISTRO DE NUEVOS USUARIOS
            </h2>
        </div>
    </div>
    
    <form method="POST" action="{{ route('admin.usuarios.store') }}" id="registroForm">
        @csrf
        
        <div class="module-section">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre(s) *</label>
                    <input type="text" 
                           class="form-control @error('nombre') is-invalid @enderror" 
                           name="nombre" 
                           value="{{ old('nombre') }}" 
                           maxlength="30"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')"
                           required
                           oninput="updateCounter(this, 'nombreCounter')">
                    <small class="text-muted"><span id="nombreCounter">0</span>/30</small>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Primer Apellido *</label>
                    <input type="text" 
                           class="form-control @error('apellido1') is-invalid @enderror" 
                           name="apellido1" 
                           value="{{ old('apellido1') }}" 
                           maxlength="20"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')"
                           required
                           oninput="updateCounter(this, 'apellido1Counter')">
                    <small class="text-muted"><span id="apellido1Counter">0</span>/20</small>
                    @error('apellido1')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Segundo Apellido</label>
                    <input type="text" 
                           class="form-control @error('apellido2') is-invalid @enderror" 
                           name="apellido2" 
                           value="{{ old('apellido2') }}"
                           maxlength="20"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')"
                           placeholder="Si no existe, omitir"
                           autocomplete="off"
                           oninput="updateCounter(this, 'apellido2Counter')">
                    <small class="text-muted"><span id="apellido2Counter">0</span>/20</small>
                    @error('apellido2')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Email Personal *</label>
                    <input type="email" 
                           class="form-control @error('email_personal') is-invalid @enderror" 
                           name="email_personal" 
                           value="{{ old('email_personal') }}" 
                           maxlength="50"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')"
                           required
                           oninput="updateCounter(this, 'emailPersonalCounter')">
                    <small class="text-muted"><span id="emailPersonalCounter">0</span>/50</small>
                    @error('email_personal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email Institucional *</label>
                    <input type="email" 
                           class="form-control @error('email_institucional') is-invalid @enderror" 
                           name="email_institucional" 
                           value="{{ old('email_institucional') }}" 
                           maxlength="50"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')"
                           required
                           oninput="updateCounter(this, 'emailInstCounter')">
                    <small class="text-muted"><span id="emailInstCounter">0</span>/50</small>
                    @error('email_institucional')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teléfono *</label>
                    <input type="tel" 
                           class="form-control @error('telefono') is-invalid @enderror" 
                           name="telefono" 
                           value="{{ old('telefono') }}" 
                           maxlength="10"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')"
                           pattern="[0-9]{10}"
                           placeholder="10 dígitos"
                           required
                           oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateCounter(this, 'telefonoCounter')">
                    <small class="text-muted"><span id="telefonoCounter">0</span>/10</small>
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-5">           
                <div class="col-md-4">
                    <label class="form-label">Número de Empleado *</label>
                    <input type="text" 
                           class="form-control @error('num_empleado') is-invalid @enderror" 
                           name="num_empleado" 
                           value="{{ old('num_empleado') }}"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')" 
                           required>
                    @error('num_empleado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de Usuario *</label>
                    <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                        <option value="">—Seleccione una opción—</option>
                        <option value="Administrador" {{ old('tipo') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="Directivo" {{ old('tipo') == 'Directivo' ? 'selected' : '' }}>Directivo</option>
                        <option value="Docente" {{ old('tipo') == 'Docente' ? 'selected' : '' }}>Docente</option>
                        <option value="Trabajo Social" {{ old('tipo') == 'Trabajo Social' ? 'selected' : '' }}>Trabajo Social</option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Privilegios</label>
                    <!-- Campo oculto para guardar la cadena de privilegios -->
                    <input type="hidden" name="privilegios" id="privilegios" value="{{ old('privilegios') }}">
                    
                    <!-- Vista previa legible de los privilegios -->
                    <div class="privilegios-preview" id="privilegiosPreview">
                        <div class="text-center text-muted" id="privilegiosPlaceholder">
                            <i class="fas fa-info-circle"></i> Seleccione un tipo de usuario
                        </div>
                    </div>
                    <small class="text-muted">Los privilegios se asignan automáticamente según el tipo</small>
                    @error('privilegios')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>               
            </div>
            <!-- Panel de contraseña y guardado -->
            <div class="row mt-4 pt-4 border-top">
                <div class="col-md-4">
                    <label for="current_password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Contraseña de Administrador *
                    </label>
                    <input type="password" 
                           class="form-control @error('current_password') is-invalid @enderror" 
                           id="current_password" 
                           name="current_password" 
                           placeholder="Ingrese su contraseña para autorizar"
                           required>
                    <small class="text-muted">Requerida para confirmar la operación</small>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-8 d-flex align-items-end justify-content-end">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save me-2"></i>Guardar Nuevo Usuario
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                        </a>
                    </div>
                </div>
            </div>     
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Contadores de caracteres (se mantienen igual)
function updateCounter(input, counterId) {
    document.getElementById(counterId).textContent = input.value.length;
}

// Mapeo de privilegios según tipo de usuario
const privilegiosMap = {
    'Administrador': {
        cadena: 'GNNNN',
        descripcion: '<span class="badge bg-primary">Administración general del Sistema</span>'
    },
    'Directivo': {
        cadena: 'NCCCC',
        descripcion: '<span class="badge bg-secondary">Sólo consulta general de Alumnos</span>'
    },
    'Docente': {
        cadena: 'NNNCC',
        descripcion: '<span class="badge bg-warning">Consulta por Grupo</span>'
    },
    'Trabajo Social': {
        cadena: 'NNGGG',
        descripcion: '<span class="badge bg-danger">Gestión por Grado</span>'
    }
};

// Actualizar vista previa de privilegios cuando cambia el tipo
document.getElementById('tipo').addEventListener('change', function() {
    const tipo = this.value;
    const privilegiosInput = document.getElementById('privilegios');
    const previewDiv = document.getElementById('privilegiosPreview');
    const placeholder = document.getElementById('privilegiosPlaceholder');
    
    if (tipo && privilegiosMap[tipo]) {
        // Actualizar campo oculto
        privilegiosInput.value = privilegiosMap[tipo].cadena;
        
        // Actualizar vista previa
        previewDiv.innerHTML = privilegiosMap[tipo].descripcion;
    } else {
        // Limpiar si no hay tipo seleccionado
        privilegiosInput.value = '';
        previewDiv.innerHTML = '<div class="text-center text-muted" id="privilegiosPlaceholder"><i class="fas fa-info-circle"></i> Seleccione un tipo de usuario</div>';
    }
});

// Si hay un valor antiguo (old) al cargar la página, mostrar la vista previa correspondiente
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo');
    const oldTipo = '{{ old('tipo') }}';
    const oldPrivilegios = '{{ old('privilegios') }}';
    
    if (oldTipo && privilegiosMap[oldTipo]) {
        tipoSelect.value = oldTipo;
        
        // Disparar evento change para actualizar vista previa
        const event = new Event('change');
        tipoSelect.dispatchEvent(event);
    }
    
    // Inicializar contadores
    const inputs = document.querySelectorAll('input[maxlength]');
    inputs.forEach(input => {
        const counterId = input.getAttribute('oninput')?.match(/'([^']+)'/)?.[1];
        if (counterId && input.value) {
            document.getElementById(counterId).textContent = input.value.length;
        }
    });
});

// Función para resetear el formulario
function resetForm() {
    if (confirm('¿Está seguro de que desea cancelar el registro? Se perderán todos los datos ingresados.')) {
        document.getElementById('registroForm').reset();
        // Resetear contadores
        document.querySelectorAll('input[maxlength]').forEach(input => {
            const counterId = input.getAttribute('oninput')?.match(/'([^']+)'/)?.[1];
            if (counterId) {
                document.getElementById(counterId).textContent = '0';
            }
        });
        // Resetear vista previa de privilegios
        document.getElementById('privilegiosPreview').innerHTML = '<div class="text-center text-muted" id="privilegiosPlaceholder"><i class="fas fa-info-circle"></i> Seleccione un tipo de usuario</div>';
        document.getElementById('privilegios').value = 'eleccione un tipo de usuario';
    }
}

// Validación de teléfono en tiempo real
document.querySelector('input[name="telefono"]')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endpush