@extends('layouts.app')

@section('title', 'Informes de Estadísticas - SGGDI')

@php
    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Datos y Estadísticas',
            'url' => route('datos-estadisticas.index'),
            'icon' => 'fa-chart-column'
        ],
        [
            'name' => 'Informes de Estadísticas',
            'url' => null,
            'icon' => 'fa-chart-pie'
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
                <i class="fas fa-chart-pie me-2 text-primary"></i>
                INFORMES DE ESTADÍSTICAS
            </h2>
        </div>
    </div>
    
    <!-- Funciones disponibles -->
    <div class="module-section">
        <div class="row g-4">

            <!-- Informe 1 -->
            <div class="col-md-4">
                <div class="card module-card bg-info text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5>Grupos y Carreras</h5>
                        <p class="small">Genera informe sobre la distribución de alumnos por grupo y carrera</p>
                        <button class="btn btn-light mt-2 btn-generar" data-tipo="grupos">
                            <i class="fas fa-download"></i> Generar y Descargar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informe 2 -->
            <div class="col-md-4">
                <div class="card module-card bg-warning text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <h5>Secundarias de Procedencia</h5>
                        <p class="small">Genera informe sobre la procedencia de los alumnos activos</p>
                        <button class="btn btn-light mt-2 btn-generar-secundarias">
                            <i class="fas fa-download"></i> Generar y Descargar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informe 3 -->
            <div class="col-md-4">
                <div class="card module-card bg-danger text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                        <h5>Asignaciones de Becas</h5>
                        <p class="small">Genera informe sobre la cantidad de alumnos con becas</p>
                        <button class="btn btn-light mt-2 btn-generar-becas">
                            <i class="fas fa-download"></i> Generar y Descargar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informe 4 -->
            <div class="col-md-4">
                <div class="card module-card bg-success text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fa-solid fa-bus"></i>
                        </div>
                        <h5>Domicilios y Traslados</h5>
                        <p class="small">Genera informe sobre los lugares de residencia de los alumnos y tiempo de traslado</p>
                        <button class="btn btn-light mt-2 btn-generar-domicilios">
                            <i class="fas fa-download"></i> Generar y Descargar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informe 5 -->
            <div class="col-md-4">
                <div class="card module-card bg-secondary text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fa-solid fa-person-shelter"></i>
                        </div>
                        <h5>Información Socioeconómica, Familia y Trabajo</h5>
                        <p class="small">Genera informe sobre frecuencias de datos domésticos y particulares de alumnos</p>
                        <button class="btn btn-light mt-2 btn-generar-socioeconomica">
                            <i class="fas fa-download"></i> Generar y Descargar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informe 6 -->
            <div class="col-md-4">
                <div class="card module-card bg-primary text-white">
                    <div class="card-body text-center">
                        <div class="module-icon">
                            <i class="fa-solid fa-file-medical"></i>
                        </div>
                        <h5>Salud y Problemas de Aprendizaje</h5>
                        <p class="small">Genera informe sobre frecuencias de padecimientos en alumnos</p>
                        <button class="btn btn-light mt-2 btn-generar-salud">
                            <i class="fas fa-download"></i> Generar y Descargar
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Panel de contraseña -->
    <div class="row mt-4 pt-3 border-top">
        <div class="col-md-4">
            <label class="form-label">
                <i class="fas fa-lock me-2"></i>Contraseña de Usuario *
            </label>
            <input type="password" 
                   class="form-control" 
                   id="current_password" 
                   placeholder="Ingrese su contraseña para autorizar">
            <small class="text-muted">Requerida para confirmar cualquier operación</small>
        </div>
        <div class="col-md-8 d-flex align-items-end justify-content-end">
            <a href="{{ route('datos-estadisticas.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Menú
            </a>
        </div>
    </div>
</div>   
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Informe de Grupos y Carreras
    const btnGrupos = document.querySelector('.btn-generar[data-tipo="grupos"]');
    if (btnGrupos) {
        btnGrupos.addEventListener('click', function() {
            const password = document.getElementById('current_password').value;
            if (!password) {
                alert('Ingrese su contraseña para generar el informe.');
                document.getElementById('current_password').focus();
                return;
            }
            
            // Deshabilitar botón mientras se genera
            btnGrupos.disabled = true;
            btnGrupos.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
            
            fetch('{{ route("datos-estadisticas.estadisticas.grupos") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_password: password
                })
            })
            .then(response => {
                if (response.status === 200) {
                    return response.blob();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error en el servidor');
                    });
                }
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `estadistica_grupos_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                alert('Informe generado correctamente');
                document.getElementById('current_password').value = '';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el informe: ' + error.message);
            })
            .finally(() => {
                btnGrupos.disabled = false;
                btnGrupos.innerHTML = '<i class="fas fa-download"></i> Generar y Descargar';
            });
        });
    }

    // Informe de Secundarias de Procedencia
    const btnSecundarias = document.querySelector('.btn-generar-secundarias');
    if (btnSecundarias) {
        btnSecundarias.addEventListener('click', function() {
            const password = document.getElementById('current_password').value;
            if (!password) {
                alert('Ingrese su contraseña para generar el informe.');
                document.getElementById('current_password').focus();
                return;
            }
            
            btnSecundarias.disabled = true;
            btnSecundarias.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
            
            fetch('{{ route("datos-estadisticas.estadisticas.secundarias") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ current_password: password })
            })
            .then(response => {
                if (response.status === 200) {
                    return response.blob();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error en el servidor');
                    });
                }
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `estadistica_secundarias_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                alert('Informe generado correctamente');
                document.getElementById('current_password').value = '';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el informe: ' + error.message);
            })
            .finally(() => {
                btnSecundarias.disabled = false;
                btnSecundarias.innerHTML = '<i class="fas fa-download"></i> Generar y Descargar';
            });
        });
    }

    // Informe de Asignaciones de Becas
    const btnBecas = document.querySelector('.btn-generar-becas');
    if (btnBecas) {
        btnBecas.addEventListener('click', function() {
            const password = document.getElementById('current_password').value;
            if (!password) {
                alert('Ingrese su contraseña para generar el informe.');
                document.getElementById('current_password').focus();
                return;
            }
            
            btnBecas.disabled = true;
            btnBecas.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
            
            fetch('{{ route("datos-estadisticas.estadisticas.becas") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ current_password: password })
            })
            .then(response => {
                if (response.status === 200) {
                    return response.blob();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error en el servidor');
                    });
                }
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `estadistica_becas_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                alert('Informe generado correctamente');
                document.getElementById('current_password').value = '';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el informe: ' + error.message);
            })
            .finally(() => {
                btnBecas.disabled = false;
                btnBecas.innerHTML = '<i class="fas fa-download"></i> Generar y Descargar';
            });
        });
    }

    // Informe de Domicilios y Traslados
    const btnDomicilios = document.querySelector('.btn-generar-domicilios');
    if (btnDomicilios) {
        btnDomicilios.addEventListener('click', function() {
            const password = document.getElementById('current_password').value;
            if (!password) {
                alert('Ingrese su contraseña para generar el informe.');
                document.getElementById('current_password').focus();
                return;
            }
            
            btnDomicilios.disabled = true;
            btnDomicilios.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
            
            fetch('{{ route("datos-estadisticas.estadisticas.domicilios") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ current_password: password })
            })
            .then(response => {
                if (response.status === 200) {
                    return response.blob();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error en el servidor');
                    });
                }
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `estadistica_domicilios_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                alert('Informe generado correctamente');
                document.getElementById('current_password').value = '';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el informe: ' + error.message);
            })
            .finally(() => {
                btnDomicilios.disabled = false;
                btnDomicilios.innerHTML = '<i class="fas fa-download"></i> Generar y Descargar';
            });
        });
    }

    // Informe Socioeconómico, Familia y Trabajo
    const btnSocioeconomica = document.querySelector('.btn-generar-socioeconomica');
    if (btnSocioeconomica) {
        btnSocioeconomica.addEventListener('click', function() {
            const password = document.getElementById('current_password').value;
            if (!password) {
                alert('Ingrese su contraseña para generar el informe.');
                document.getElementById('current_password').focus();
                return;
            }
            
            btnSocioeconomica.disabled = true;
            btnSocioeconomica.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
            
            fetch('{{ route("datos-estadisticas.estadisticas.socioeconomica") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ current_password: password })
            })
            .then(response => {
                if (response.status === 200) {
                    return response.blob();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error en el servidor');
                    });
                }
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `estadistica_socioeconomica_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                alert('Informe generado correctamente');
                document.getElementById('current_password').value = '';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el informe: ' + error.message);
            })
            .finally(() => {
                btnSocioeconomica.disabled = false;
                btnSocioeconomica.innerHTML = '<i class="fas fa-download"></i> Generar y Descargar';
            });
        });
    }

    // Informe de Salud y Problemas de Aprendizaje
    const btnSalud = document.querySelector('.btn-generar-salud');
    if (btnSalud) {
        btnSalud.addEventListener('click', function() {
            const password = document.getElementById('current_password').value;
            if (!password) {
                alert('Ingrese su contraseña para generar el informe.');
                document.getElementById('current_password').focus();
                return;
            }
            
            btnSalud.disabled = true;
            btnSalud.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
            
            fetch('{{ route("datos-estadisticas.estadisticas.salud") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ current_password: password })
            })
            .then(response => {
                if (response.status === 200) {
                    return response.blob();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error en el servidor');
                    });
                }
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `estadistica_salud_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                alert('Informe generado correctamente');
                document.getElementById('current_password').value = '';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el informe: ' + error.message);
            })
            .finally(() => {
                btnSalud.disabled = false;
                btnSalud.innerHTML = '<i class="fas fa-download"></i> Generar y Descargar';
            });
        });
    }
});
</script>
@endpush