@extends('layouts.app')

@section('title', 'Consultas y Estadísticas - SGGDI')

@php
    use App\Helpers\PrivilegiosHelper;
    $usuario = Auth::user();
    
    // Permisos para mostrar tarjetas
    $permisoGeneral = PrivilegiosHelper::consultaGeneral($usuario);  // Posición 1
    $permisoGrado = PrivilegiosHelper::consultaGrado($usuario);      // Posición 2
    $permisoGrupo = PrivilegiosHelper::consultaGrupo($usuario);      // Posición 3
    $permisoIndividual = PrivilegiosHelper::consultaIndividual($usuario); // Posición 4
    
    // Para "Consulta de Datos Prioritarios" (cualquier permiso 1-4)
    $puedeConsultarPrioritarios = $permisoGeneral || $permisoGrado || $permisoGrupo || $permisoIndividual;
    
    // Para "Informes de Estadísticas" (solo posición 1 - General)
    $puedeVerInformes = $permisoGeneral;
    
    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Datos y Estadísticas',
            'url' => null,
            'icon' => 'fa-chart-column'
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
                <i class="fa-solid fa-chart-column me-2 text-info"></i>
                DATOS Y ESTADÍSTICAS
            </h2>
        </div>
    </div>
    
    <!-- Funciones disponibles -->
    <div class="module-section">
        <h5 class="mb-4"><i class="fas fa-cubes"></i> FUNCIONES DISPONIBLES:</h5>
        
        <div class="row g-4">
            <!-- Función de Consulta de Datos Prioritarios -->
            @if($puedeConsultarPrioritarios)
            <div class="col-md-4">
                <div class="card module-card bg-success text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fa-solid fa-file-circle-exclamation"></i>
                        </div>
                        <h5>Consulta de Datos Prioritarios</h5>
                        <p class="small">Descarga rápida de datos</p>
                        <a href="{{ route('datos-estadisticas.datos-prioritarios') }}" class="btn btn-light mt-2">
                            <i class="fas fa-arrow-right"></i> Acceder
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Informes de Estadísticas -->
            @if($puedeVerInformes)
            <div class="col-md-4">
                <div class="card module-card bg-primary text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <h5>Informes de Estadísticas</h5>
                        <p class="small">Genera estadísticas de la situación actual</p>
                        <a href="{{ route('datos-estadisticas.estadisticas') }}" class="btn btn-light mt-2">
                            <i class="fas fa-arrow-right"></i> Acceder
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Mensaje si no hay funciones disponibles -->
            @if(!$puedeConsultarPrioritarios && !$puedeVerInformes)
            <div class="col-12">
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No tienes permisos para acceder a ninguna función de este módulo.
                </div>
            </div>
            @endif
        </div>
    </div>
</div>   
@endsection