@extends('layouts.app')

@section('title', 'Inicio - SADHCC')

@php
    $usuario = Auth::user();
    $esAdmin = $usuario && $usuario->tipo === 'Administrador';
@endphp

@push('styles')
<style>
    .welcome-section {
        background: linear-gradient(135deg, #4e0d0dff 0%, #a01508 100%);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
        border-radius: 10px;
    }
    .module-section {
        background-color: #f8f9fa;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .module-card {
        transition: transform 0.2s;
        height: 100%;
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .module-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .module-card .card-body {
        padding: 1.5rem;
    }
    .module-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
    <!--Welcome Section -->
    <div class="welcome-section">
        <div class="container text-center">
            <h1>Sistema de Agendado Digital de Horarios del Centro de Cómputo</h1>
            <p class="lead">Plataforma Oficial de Reservación</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Mensaje de bienvenida -->
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h5><i class="fas fa-info-circle"></i> ¡Bienvenido(a) al sistema! Te encuentras en la página de INICIO</h5>
            <p class="mb-0">
                Seleccione alguna de las siguientes opciones. Los módulos y funciones disponibles dependen del tipo de usuario 
                y privilegios del sistema asignados.
            </p>
        </div>

        <!-- Módulos disponibles -->
        <div class="module-section">
            <h5 class="mb-4"><i class="fas fa-cubes"></i> MÓDULOS DISPONIBLES:</h5>
            
            <div class="row g-4">
                <!-- Módulo de Administración de Usuarios -->
                @if($esAdmin)
                <div class="col-md-4">
                    <div class="card module-card bg-primary text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <h5>Administración de Usuarios</h5>
                            <p class="small">Gestiona los usuarios del sistema</p>
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Módulo de Configuración de Grupos -->
                <div class="col-md-4">
                    <div class="card module-card bg-success text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Configuración de Grupos</h5>
                            <p class="small">Administra los grupos y clases</p>
                            <a href="{{ route('admin.grupos.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>                

                <!-- Módulo de Configuración de Materias y Laboratorios -->
                <div class="col-md-4">
                    <div class="card module-card bg-danger text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fa-solid fa-laptop-file"></i>
                            </div>
                            <h5>Configuración de Materias y Laboratorios</h5>
                            <p class="small">Gestiona datos de materias y lugares de trabajo</p>
                            <a href="{{ route('admin.materias.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Módulo de Carga de Horarios -->
                <div class="col-md-4">
                    <div class="card module-card bg-secondary text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <h5>Carga de Horarios</h5>
                            <p class="small">Gestiona horarios y días inhábiles</p>
                            <a href="{{ route('admin.horarios.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Módulo de Solicitud de Reserva de Horas -->
                <div class="col-md-4">
                    <div class="card module-card bg-warning text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </div>
                            <h5>Solicitud de Reserva de Horas</h5>
                            <p class="small">Formulario para apartados</p>
                            <a href="{{ route('solicitud.index') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Módulo de * -->
                <div class="col-md-4">
                    <div class="card module-card bg-info text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                
                            </div>
                            <h5>*</h5>
                            <p class="small">*</p>
                            <a href="#" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection