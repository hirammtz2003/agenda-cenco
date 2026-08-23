@extends('layouts.app')

@section('title', 'Administración de Usuarios - SADHCC')

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
            'name' => 'Administración de Usuarios',
            'url' => null,
            'icon' => 'fa-users-cog'
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
<div class="container">
    <!-- Breadcrumb -->
    @include('partials.breadcrumb')

    <!-- Título del menú -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="border-bottom pb-3">
                <i class="fas fa-users-cog me-2 text-primary"></i>
                ADMINISTRACIÓN DE USUARIOS
            </h2>
        </div>
    </div>
    
    <!-- Funciones disponibles (SOLO ADMIN) -->
    @if($esAdmin)
    <div class="module-section">
        <h5 class="mb-4"><i class="fas fa-cubes"></i> FUNCIONES DISPONIBLES:</h5>
        
        <div class="row g-4">                
            <!-- Registro de Nuevos Usuarios -->
            <div class="col-md-4">
                <div class="card module-card bg-danger text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h5>Registro de Nuevos Usuarios</h5>
                        <p class="small">Añade trabajadores del plantel autorizados</p>
                        <a href="{{ route('admin.usuarios.registro') }}" class="btn btn-light mt-2">
                            <i class="fas fa-arrow-right"></i> Acceder
                        </a>
                    </div>
                </div>
            </div>

            <!-- Consulta y Edición de Usuarios -->
            <div class="col-md-4">
                <div class="card module-card bg-secondary text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h5>Consulta y Edición de Usuarios</h5>
                        <p class="small">Visualiza y modifica registros</p>
                        <a href="{{ route('admin.usuarios.consulta') }}" class="btn btn-light mt-2">
                            <i class="fas fa-arrow-right"></i> Acceder
                        </a>
                    </div>
                </div>
            </div>

            <!-- Seguridad y Privilegios de Usuarios -->
            <div class="col-md-4">
                <div class="card module-card bg-warning text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5>Seguridad y Privilegios</h5>
                        <p class="small">Asigna privilegios y recuperación de cuenta</p>
                        <a href="{{ route('admin.usuarios.seguridad') }}" class="btn btn-light mt-2">
                            <i class="fas fa-arrow-right"></i> Acceder
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        No tienes permisos para acceder a esta sección. Solo los administradores pueden gestionar usuarios.
    </div>
    @endif
</div>   
@endsection