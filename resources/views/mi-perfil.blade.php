

@extends('layouts.app')

@section('title', 'Mi Perfil - SGGDI')

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
            'name' => 'Mi Perfil',
            'url' => null,
            'icon' => 'fa-id-card'
        ]
    ];
@endphp

@push('styles')
<style>
    .profile-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    .profile-header {
        background: linear-gradient(135deg, #4e0d0dff 0%, #a01508 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1.5rem;
    }
    .profile-header h4 {
        margin: 0;
        font-weight: 600;
    }
    .edit-header {
        background: linear-gradient(135deg, #0d4e2bff 0%, #08a03cff 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1.5rem;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
        padding: 0.5rem;
        border-radius: 5px;
    }
    .info-value {
        padding: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    .badge-custom {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    .form-section {
        padding: 1rem;
    }
    .instruction-box {
        background-color: #e7f3ff;
        border-left: 4px solid #0d6efd;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    @include('partials.breadcrumb')

    @if(session('force_password_change') || (isset($forceChange) && $forceChange))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Contraseña temporal detectada!</h5>
            <p>Debes cambiar tu contraseña antes de poder acceder al sistema. Todos los demás módulos estarán bloqueados hasta que completes este paso.</p>
            <hr>
            <p class="mb-0">Por favor, ingresa tu contraseña temporal actual y luego establece una nueva contraseña.</p>
        </div>
    @endif

    @if(session('force_password_change') || (isset($forceChange) && $forceChange))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prevenir el evento beforeunload SOLO cuando hay cambios sin guardar
                let formChanged = false;
                
                // Detectar cambios en el formulario
                document.querySelectorAll('#email, #new_password, #new_password_confirmation').forEach(input => {
                    input.addEventListener('input', function() {
                        formChanged = true;
                    });
                });
                
                /*window.addEventListener('beforeunload', function(e) {
                    if (formChanged) {
                        e.preventDefault();
                        e.returnValue = 'Tienes cambios sin guardar. ¿Estás seguro de que deseas salir?';
                    }
                });*/
                
                // Deshabilitar enlaces de navegación excepto logout
                document.querySelectorAll('a:not([href*="logout"])').forEach(link => {
                    if (!link.href.includes('logout') && !link.href.includes('mi-perfil')) {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            alert('Debes cambiar tu contraseña antes de acceder a otras secciones.');
                        });
                    }
                });
                
                // El formulario se enviará normalmente, no lo bloqueamos
            });
        </script>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Tarjeta de Información del Perfil -->
            <div class="card profile-card">
                <div class="profile-header">
                    <h4 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>Mi Perfil
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="info-label">
                                <i class="fas fa-user me-2"></i>Nombre completo:
                            </span>
                        </div>
                        <div class="col-md-9 info-value">
                            {{ $user->getNombreCompletoAttribute() }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="info-label">
                                <i class="fas fa-envelope me-2"></i>Email personal:
                            </span>
                        </div>
                        <div class="col-md-9 info-value">
                            {{ $user->email_personal }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="info-label">
                                <i class="fas fa-envelope me-2"></i>Email institucional:
                            </span>
                        </div>
                        <div class="col-md-4 info-value">
                            {{ $user->email_institucional }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="info-label">
                                <i class="fas fa-phone me-2"></i>Teléfono:
                            </span>
                        </div>
                        <div class="col-md-4 info-value">
                            {{ substr($user->telefono, 0, 3) }} {{ substr($user->telefono, 3, 3) }} {{ substr($user->telefono, 6) }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="info-label">
                                <i class="fas fa-id-badge me-2"></i>Número de empleado:
                            </span>
                        </div>
                        <div class="col-md-9 info-value">
                            {{ $user->num_empleado }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="info-label">
                                <i class="fas fa-tag me-2"></i>Tipo de usuario:
                            </span>
                        </div>
                        <div class="col-md-9 info-value">
                            @php
                                $badgeClass = $user->tipo === 'Administrador' ? 'bg-danger' : 'bg-primary';
                            @endphp
                            <span class="badge {{ $badgeClass }} badge-custom">
                                {{ $user->tipo }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="info-label">
                                <i class="fas fa-shield-alt me-2"></i>Privilegios asignados:
                            </span>
                        </div>
                        <div class="col-md-9 info-value">
                            @if($user->tipo == "Administrador")
                                <span class="badge bg-primary">Administración general del Sistema</span>
                            @elseif($user->tipo == "Docente")
                                <span class="badge bg-warning">Reservación de horas en el Centro de Cómputo</span>    
                            @endif
                        </div>                        
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Edición de Perfil -->
            <div class="card profile-card">
                <div class="edit-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Perfil
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf

                        @method('PUT')

                        <!-- Nota de instrucciones -->
                        <div class="instruction-box">
                            <h6><i class="fas fa-info-circle me-2 text-primary"></i>Instrucciones:</h6>
                            <p class="mb-0 small">
                                Para cambiar tu email personal no es necesario ingresar la contraseña actual. 
                                La contraseña actual solo es requerida si deseas cambiar tu contraseña. 
                                Si cambias ambos, necesitarás tu contraseña actual.
                            </p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Nuevo email personal
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email_personal) }}"
                                       placeholder="Ingresa tu nuevo email"
                                       autocomplete="off"
                                       readonly
                                       onfocus="this.removeAttribute('readonly')">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>       
                            <div class="col-md-6">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Contraseña actual *
                                </label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Solo si deseas cambiar contraseña">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-key me-2"></i>Nueva contraseña
                                </label>
                                <input type="password" 
                                       class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" 
                                       name="new_password" 
                                       placeholder="Mínimo 6 caracteres">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label">
                                    <i class="fas fa-check-circle me-2"></i>Confirmar nueva contraseña
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       placeholder="Repite la nueva contraseña">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" id="btnGuardarPerfil">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('mi-perfil') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    document.getElementById('btnGuardarPerfil')?.addEventListener('click', function() {
        console.log('Botón Guardar clickeado');
    });
</script>