@extends('layouts.app')

@section('title', 'Gestión de Información de Alumnos - SGGDI')

@php
    use App\Helpers\PrivilegiosHelper;
    $usuarioActual = Auth::user();
    $puedeRegistrar = $puedeRegistrar ?? false;
    $puedeConsultar = $puedeConsultar ?? false;

    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Gestión de Información de Alumnos',
            'url' => null,
            'icon' => 'fa-folder-open'
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
@auth
    <div class="container">
        <!-- Breadcrumb -->
        @include('partials.breadcrumb')

        <!-- Título del menú -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="border-bottom pb-3">
                    <i class="fa-solid fa-folder-open me-2 text-danger"></i>
                    GESTIÓN DE INFORMACIÓN DE ALUMNOS
                </h2>
            </div>
        </div>
        
        <!-- Funciones disponibles -->
        <div class="module-section">
            <h5 class="mb-4"><i class="fas fa-cubes"></i> FUNCIONES DISPONIBLES:</h5>
            
            <div class="row g-4">
                <!-- Función de Registro de Nuevos Alumnos (solo para usuarios con gestión) -->
                @if($puedeRegistrar)
                <div class="col-md-4">
                    <div class="card module-card bg-info text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h5>Registro de Nuevos Alumnos</h5>
                            <p class="small">Añade datos de nuevos alumnos inscritos</p>
                            <a href="{{ route('alumnos.registro') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Función de Consulta y Edición General (visible para todos los que acceden) -->
                @if($puedeConsultar)
                <div class="col-md-4">
                    <div class="card module-card bg-warning text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fa-solid fa-users-between-lines"></i>
                            </div>
                            <h5>Consulta y Edición General</h5>
                            <p class="small">Gestiona datos básicos de alumnos</p>
                            <a href="{{ route('alumnos.consulta-general') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Función de Consulta y Edición Individual (visible para todos los que acceden) -->
                @if($puedeConsultar)
                <div class="col-md-4">
                    <div class="card module-card bg-success text-white">
                        <div class="card-body text-center">
                            <div class="module-icon">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <h5>Consulta y Edición Individual</h5>
                            <p class="small">Gestiona datos complejos de alumnos</p>
                            <a href="{{ route('alumnos.consulta-individual') }}" class="btn btn-light mt-2">
                                <i class="fas fa-arrow-right"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>   
@endauth
@endsection