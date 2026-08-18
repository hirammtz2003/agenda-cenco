@extends('layouts.app')

@section('title', 'Consulta y Edición Individual de Alumnos - SGGDI')

@php
    use App\Helpers\PrivilegiosHelper;
    $usuarioActual = Auth::user();
    $puedeEditar = $puedeEditar ?? false;
    $puedeConsultar = $puedeConsultar ?? false;
    $permisoGeneral = $permisoGeneral ?? false;
    $permisoGrado = $permisoGrado ?? false;
    $permisoGrupo = $permisoGrupo ?? false;
    $permisoIndividual = $permisoIndividual ?? false;
    $puedeEditarGeneral = $puedeEditarGeneral ?? false;
    $puedeEditarGrado = $puedeEditarGrado ?? false;
    $puedeEditarGrupo = $puedeEditarGrupo ?? false;
    $puedeEditarIndividual = $puedeEditarIndividual ?? false;

    $breadcrumbs = [
        [
            'name' => 'Inicio',
            'url' => route('welcome'),
            'icon' => 'fa-home'
        ],
        [
            'name' => 'Gestión de Información de Alumnos',
            'url' => route('alumnos.index'),
            'icon' => 'fa-users-cog'
        ],
        [
            'name' => 'Consulta y Edición Individual de Alumnos',
            'url' => null,
            'icon' => 'fa-user-edit'
        ]
    ];
@endphp

@push('styles')
<style>
    /* Estilos básicos para asegurar que el contenido se muestre correctamente */
    * {
        box-sizing: border-box;
    }
    
    body {
        font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        line-height: 1.5;
        margin: 0;
        padding: 0;
    }
    
    .container {
        max-width: 1320px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    /* Estilos para el contenedor de búsqueda */
    .search-wrapper {
        position: relative;
    }
    
    /* Estilos para el menú de resultados */
    #resultadosBusqueda {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        background: white;
        border: 1px solid #ccc;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-height: 300px;
        overflow-y: auto;
        margin-top: 2px;
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
    
    .search-result-item.highlighted {
        background-color: #ffc107 !important;
        color: #212529;
    }
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
    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 0 0 8px 8px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .search-result-item {
        padding: 8px 15px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }
    .search-result-item:hover {
        background-color: #f0f0f0;
    }
    .search-result-item.highlighted {
        background-color: #ffc107 !important;
        color: #212529;
    }
    .instruction-box {
        background-color: #e7f3ff;
        border-left: 4px solid #0d6efd;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 5px;
    }

    /* Estilos para acordeones */
    .accordion-section {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 1rem;
        background: white;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .accordion-header {
        background-color: #f8f9fa;
        padding: 1rem 1.25rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.2s;
    }

    .accordion-header:hover {
        background-color: #e9ecef;
    }

    .accordion-header h5, 
    .accordion-header h6 {
        margin: 0;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .accordion-header .accordion-icon {
        transition: transform 0.3s ease;
    }

    .accordion-section.expanded .accordion-header .accordion-icon {
        transform: rotate(180deg);
    }

    .accordion-content {
        display: none;
        padding: 1.25rem;
        border-top: 1px solid #dee2e6;
        background: white;
    }

    .accordion-section.expanded .accordion-content {
        display: block;
    }

    /* Sticky para campos fijos */
    .sticky-top-custom {
        position: sticky;
        top: 0;
        z-index: 100;
        background-color: white;
        padding: 0.75rem 0;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 1rem;
    }

    .sticky-bottom-custom {
        position: sticky;
        bottom: 0;
        z-index: 100;
        background-color: white;
        padding: 0.75rem 0;
        border-top: 1px solid #dee2e6;
        margin-top: 1rem;
    }

    /* Botón flotante para ir al final */
    .floating-bottom-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #fd410d;
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        cursor: pointer;
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        opacity: 0;
        visibility: hidden;
    }

    .floating-bottom-btn:hover {
        background-color: #fd410d;
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    .floating-bottom-btn.visible {
        opacity: 1;
        visibility: visible;
    }

    .floating-bottom-btn i {
        font-size: 1.5rem;
    }

    @media (max-width: 768px) {
        .floating-bottom-btn, .floating-top-btn {
            width: 40px;
            height: 40px;
        }
        .floating-bottom-btn i, .floating-top-btn i {
            font-size: 1.2rem;
        }
        .floating-top-btn {
            right: 80px;
        }
    }

    /* Asegurar que .accordion-content se muestre solo cuando está expandido */
    .accordion-section .accordion-content {
        display: none;
    }

    .accordion-section.expanded .accordion-content {
        display: block;
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
                <i class="fas fa-user-edit me-2 text-success"></i>
                CONSULTA Y EDICIÓN INDIVIDUAL DE ALUMNOS
            </h2>
        </div>
    </div>

    <!-- SECCIÓN 0.-Controles de Consulta y primeros datos -->
        <div class="module-section">
            <div class="row mb-4">
                <div class="col-md-5">
                    <label class="form-label"><i class="fas fa-search"></i> Buscar Alumno:</label>
                    <div class="search-wrapper">
                        <div class="input-group">
                            <input type="text" name="busqueda" id="busqueda_alumno" class="form-control" 
                                placeholder="Núm. control, nombre, apellidos, CURP..." 
                                value="{{ request('busqueda') }}"
                                autocomplete="off"
                                autocorrect="off"
                                autocapitalize="off"
                                spellcheck="false">
                            <input type="hidden" name="alumno_id" id="alumno_id_selected" value="{{ $alumnoConsultado->id ?? '' }}">
                            <button type="button" id="btnBuscarAlumno" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('alumnos.consulta-individual') }}" 
                                id="btnLimpiarBusqueda" 
                                class="btn btn-outline-secondary align-items-center {{ request('busqueda') || request('alumno_id') ? 'd-inline-flex' : '' }}" 
                                style="{{ request('busqueda') || request('alumno_id') ? '' : 'display:none;' }}">
                                <i class="fas fa-times me-2"></i> Limpiar
                            </a>
                        </div>
                        <div id="resultadosBusqueda" class="search-results-dropdown" style="display: none;"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" id="toggle_all_sections" 
                            {{ session('toggle_all_sections') ? 'checked' : '' }}
                            onchange="toggleAllSectionsMode()">
                        <label class="form-check-label" for="toggle_all_sections">
                            <i class="fas fa-layer-group"></i> Permitir desplegar todas las secciones
                        </label>
                    </div>
                </div>
                @if($puedeEditar)
                <div class="col-md-3">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" id="toggle_edit_mode" 
                            onchange="toggleEditMode()">
                        <label class="form-check-label" for="toggle_edit_mode">
                            <i class="fas fa-pencil-alt"></i> Activar edición de información
                        </label>
                    </div>
                </div>
                @else
                <!-- Si no puede editar, mostrar un espacio vacío o mensaje -->
                <div class="col-md-3">
                    <div class="mt-4 text-muted small">
                        <i class="fas fa-lock"></i> Modo solo lectura
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Mostrar mensaje de error si existe -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Foto, número de control, estatus, etc. -->
            <div class="row mt-4 pt-4 border-top align-items-end">
                <div class="col-md-2">
                    <div class="text-center">
                        <div id="fotoContainer" class="d-flex justify-content-center">
                            <img id="fotoAlumno" 
                                src="{{ $alumnoConsultado && $alumnoConsultado->foto && file_exists(public_path('fotos/' . $alumnoConsultado->foto . '.jpg')) ? asset('fotos/' . $alumnoConsultado->foto . '.jpg') : asset('imagenes/sin-foto.jpg') }}" 
                                alt="Foto del alumno" 
                                class="img-fluid rounded border"
                                style="max-width: 150px; max-height: 150px; object-fit: cover;">
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Nombre de la foto:</small>
                            <br>
                            <small class="text-muted fw-semibold" id="nombreFoto">{{ $alumnoConsultado->foto ?? '—' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Número de Control</label>
                    <input type="text" 
                        id="num_control"
                        class="form-control" 
                        maxlength="14"
                        placeholder="Ingresar si ya se asignó"
                        autocomplete="off"
                        value="{{ $alumnoConsultado->num_control ?? '' }}"
                        {{ !$puedeEditar ? 'disabled' : '' }}
                        data-original="{{ $alumnoConsultado->num_control ?? '' }}">
                    <small class="text-muted"><span id="numControlCounter">{{ strlen($alumnoConsultado->num_control ?? '') }}</span>/14</small>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" id="estatusSelect" class="form-select" 
                            {{ !$puedeEditar ? 'disabled' : '' }}
                            data-original="{{ $alumnoConsultado->estatus ?? '1' }}">
                        <option value="1" {{ ($alumnoConsultado && $alumnoConsultado->estatus) ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ ($alumnoConsultado && !$alumnoConsultado->estatus) ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <div id="fecha_nacimiento" class="form-control-plaintext fw-bold">
                        {{ $alumnoConsultado && $alumnoConsultado->curp ? substr($alumnoConsultado->curp, 4, 2) . '/' . substr($alumnoConsultado->curp, 6, 2) . '/' . substr($alumnoConsultado->curp, 8, 4) : '—' }}
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Edad Actual</label>
                    <div id="edad_actual" class="form-control-plaintext fw-bold">
                        @php
                            $edad = null;
                            if ($alumnoConsultado && $alumnoConsultado->curp) {
                                $fechaNac = substr($alumnoConsultado->curp, 4, 2) . '/' . substr($alumnoConsultado->curp, 6, 2) . '/' . substr($alumnoConsultado->curp, 8, 4);
                                $fechaNacimiento = \DateTime::createFromFormat('d/m/Y', $fechaNac);
                                if ($fechaNacimiento) {
                                    $hoy = new \DateTime();
                                    $edad = $hoy->diff($fechaNacimiento)->y;
                                }
                            }
                        @endphp
                        {{ $edad ?? '—' }} años
                    </div>
                </div>
            </div>
        </div>
    
    <form id="registroForm" method="POST" action="{{ route('alumnos.actualizar-alumno-completo') }}">
        @csrf        

        <!-- SECCIÓN 1.-DATOS PERSONALES DEL ALUMNO -->
        <div class="accordion-section" id="section1">
            <div class="accordion-header" data-section="1">
                <h5><i class="fa-solid fa-user-circle"></i> 1.-DATOS PERSONALES DEL ALUMNO</h5>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
            </div>
            <div class="accordion-content">
                <!-- Subsección 1.1-Nombre completo -->
                <h6 class="mb-4"><i class="fa-solid fa-user-pen"></i> 1.1-NOMBRE COMPLETO</h6>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Nombre(s) *</label>
                        <input type="text" 
                            id="alumno_nombre"
                            class="form-control" 
                            maxlength="30"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'alumnoNombreCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="alumnoNombreCounter">0</span>/30</small>
                    </div>
                    <div class="col-md-4 position-relative">
                        <label class="form-label">Primer Apellido *</label>
                        <input type="text" 
                            id="alumno_apellido1"
                            class="form-control" 
                            maxlength="20"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'alumnoApellido1Counter'); buscarApellido('apellido1', this.value)"
                            data-required="true">
                        <small class="text-muted"><span id="alumnoApellido1Counter">0</span>/20</small>
                        <div id="apellido1Results" class="search-results-dropdown"></div>
                    </div>
                    <div class="col-md-4 position-relative">
                        <label class="form-label">Segundo Apellido</label>
                        <input type="text" 
                            id="alumno_apellido2"
                            class="form-control" 
                            maxlength="20"
                            placeholder="Omitir si no existe"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'alumnoApellido2Counter'); buscarApellido('apellido2', this.value)">
                        <small class="text-muted"><span id="alumnoApellido2Counter">0</span>/20</small>
                        <div id="apellido2Results" class="search-results-dropdown"></div>
                    </div>
                </div>
                <input type="hidden" id="alumnoId" value="">

                <!-- Subsección 1.2-Lugar de nacimiento -->
                <h6 class="mb-4"><i class="fa-solid fa-map-marker-alt"></i> 1.2-LUGAR DE NACIMIENTO</h6>
                <div class="row mb-3">
                    <div class="col-md-3 position-relative">
                        <label class="form-label">Localidad *</label>
                        <input type="text" 
                            id="localidad"
                            class="form-control" 
                            placeholder="Ej: Río Grande"
                            maxlength="30"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'localidadCounter'); buscarLugares(this.value)"
                            data-required="true">
                        <small class="text-muted"><span id="localidadCounter">0</span>/30</small>
                        <div id="localidadResults" class="search-results-dropdown"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Municipio *</label>
                        <input type="text" 
                            id="municipio"
                            class="form-control" 
                            placeholder="Ej: Río Grande"
                            maxlength="30"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'municipioCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="municipioCounter">0</span>/30</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado *</label>
                        <input type="text" 
                            id="estado"
                            class="form-control" 
                            placeholder="Ej: Zacatecas"
                            maxlength="20"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'estadoCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="estadoCounter">0</span>/20</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">País *</label>
                        <input type="text" 
                            id="pais"
                            class="form-control" 
                            placeholder="Ej: México"
                            maxlength="15"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'paisCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="paisCounter">0</span>/15</small>
                    </div>
                </div>
                <input type="hidden" id="lugarNacimientoId" value="">

                <!-- Subsección 1.3-Domicilio actual -->
                <h6 class="mb-4"><i class="fa-solid fa-home"></i> 1.3-DOMICILIO ACTUAL</h6>
                <!-- Calle, num. exterior, num. interior, colonia, localidad -->
                <div class="row mb-3">
                    <div class="col-md-3 position-relative">
                        <label class="form-label">Calle *</label>
                        <input type="text" 
                            id="calle"
                            class="form-control" 
                            maxlength="40"
                            placeholder="Sólo nombre propio; omitir 'C.', 'Av.'..."
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'calleCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="calleCounter">0</span>/40</small>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">No. Ext *</label>
                        <input type="text" 
                            id="num_ext"
                            class="form-control" 
                            maxlength="10"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'num_extCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="num_extCounter">0</span>/10</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No. Int</label>
                        <input type="text" 
                            id="num_int"
                            class="form-control" 
                            maxlength="10"
                            placeholder="Opcional"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'num_intCounter')">
                        <small class="text-muted"><span id="num_intCounter">0</span>/10</small>
                    </div>
                    <div class="col-md-3 position-relative">
                        <label class="form-label">Colonia *</label>
                        <input type="text" 
                            id="colonia"
                            class="form-control" 
                            maxlength="40"
                            placeholder="Buscar o ingresar nueva colonia"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'coloniaCounter'); buscarColonia(this.value)"
                            data-required="true">
                        <small class="text-muted"><span id="coloniaCounter">0</span>/40</small>
                        <div id="coloniaResults" class="search-results-dropdown"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Localidad *</label>
                        <input type="text" 
                            id="domicilio_localidad"
                            class="form-control" 
                            maxlength="30"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'domicilioLocalidadCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="domicilioLocalidadCounter">0</span>/30</small>
                    </div>
                </div>
                <!-- Calle, num. exterior, num. interior, colonia, localidad -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Municipio *</label>
                        <input type="text" 
                            id="domicilio_municipio"
                            class="form-control" 
                            maxlength="30"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'domicilioMunicipioCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="domicilioMunicipioCounter">0</span>/30</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">C.P. *</label>
                        <input type="text" 
                            id="cp"
                            class="form-control" 
                            maxlength="5"
                            placeholder="5 dígitos"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateCounter(this, 'cpCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="cpCounter">0</span>/5</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado *</label>
                        <input type="text" 
                            id="domicilio_estado"
                            class="form-control" 
                            maxlength="20"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'domicilioEstadoCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="domicilioEstadoCounter">0</span>/20</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Teléfono Domicilio</label>
                        <input type="tel" 
                            id="telefono_domicilio"
                            class="form-control" 
                            maxlength="10"
                            placeholder="10 dígitos"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateCounter(this, 'telefonoDomicilioCounter')">
                        <small class="text-muted"><span id="telefonoDomicilioCounter">0</span>/10</small>
                    </div>
                </div>
                <input type="hidden" id="domicilioId" value="">

                <!-- Subsección 1.4-Secundaria de procedencia -->
                <h6 class="mb-4"><i class="fa-solid fa-school"></i> 1.4-SECUNDARIA DE PROCEDENCIA</h6>
                <!-- Nombre del plantel, tipo de secundaria, localidad -->
                <div class="row mb-3">
                    <div class="col-md-4 position-relative">
                        <label class="form-label">Nombre del Plantel *</label>
                        <input type="text" 
                            id="secundaria_nombre"
                            class="form-control" 
                            maxlength="100"
                            placeholder="Buscar o ingresar nombre de la secundaria"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'secundariaNombreCounter'); buscarSecundaria(this.value)"
                            data-required="true">
                        <small class="text-muted"><span id="secundariaNombreCounter">0</span>/100</small>
                        <div id="secundariaResults" class="search-results-dropdown"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Secundaria *</label>
                        <select id="secundaria_tipo" class="form-select" data-required="true">
                            <option value="">—Seleccione una opción—</option>
                            <option value="General">General</option>
                            <option value="Técnica">Técnica</option>
                            <option value="Telesecundaria">Telesecundaria</option>
                            <option value="Abierta">Abierta</option>
                            <option value="Privada">Privada</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Localidad *</label>
                        <input type="text" 
                            id="secundaria_localidad"
                            class="form-control" 
                            maxlength="30"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'secundariaLocalidadCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="secundariaLocalidadCounter">0</span>/30</small>
                    </div>
                </div>
                <!-- Municipio, estado, país -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Municipio *</label>
                        <input type="text" 
                            id="secundaria_municipio"
                            class="form-control" 
                            maxlength="30"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'secundariaMunicipioCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="secundariaMunicipioCounter">0</span>/30</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado *</label>
                        <input type="text" 
                            id="secundaria_estado"
                            class="form-control" 
                            maxlength="20"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'secundariaEstadoCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="secundariaEstadoCounter">0</span>/20</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">País *</label>
                        <input type="text" 
                            id="secundaria_pais"
                            class="form-control" 
                            maxlength="15"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'secundariaPaisCounter')"
                            data-required="true">
                        <small class="text-muted"><span id="secundariaPaisCounter">0</span>/15</small>
                    </div>
                </div>
                <input type="hidden" id="secundariaId" value="">

                <!-- Subsección 1.5-Datos particulares del alumno -->
                <h6 class="mb-4"><i class="fa-solid fa-id-card"></i> 1.5-DATOS PARTICULARES DEL ALUMNO</h6>
                <!-- Teléfono celular, email personal, email institucional -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Teléfono Celular</label>
                        <input type="tel" 
                            id="telefono_celular"
                            class="form-control" 
                            maxlength="10"
                            placeholder="10 dígitos"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateCounter(this, 'telefonoCelularCounter')">
                        <small class="text-muted"><span id="telefonoCelularCounter">0</span>/10</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email Personal</label>
                        <input type="email" 
                            id="email_personal"
                            class="form-control" 
                            maxlength="50"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'emailPersonalCounter')">
                        <small class="text-muted"><span id="emailPersonalCounter">0</span>/50</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email Institucional</label>
                        <input type="email" 
                            id="email_institucional"
                            class="form-control" 
                            maxlength="50"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'emailInstitucionalCounter')">
                        <small class="text-muted"><span id="emailInstitucionalCounter">0</span>/50</small>
                    </div>
                </div>
                <!-- CURP, número de seguro social -->
                <div class="row mb-3">
                    
                    <div class="col-md-4">
                        <label class="form-label">CURP</label>
                        <input type="text" 
                            id="curp"
                            class="form-control" 
                            maxlength="18"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'curpCounter')">
                        <small class="text-muted"><span id="curpCounter">0</span>/18</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Número de Seguro Social</label>
                        <input type="text" 
                            id="nss"
                            class="form-control" 
                            maxlength="11"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'nssCounter')">
                        <small class="text-muted"><span id="nssCounter">0</span>/11</small>
                    </div>
                </div>

                <!-- Subsección 1.6-Grupo -->
                <h6 class="mb-4"><i class="fa-solid fa-users"></i> 1.6-GRUPO</h6>
                <div class="row mb-3">
                    <div class="col-md-6 position-relative">
                        <label class="form-label">Buscar Grupo *</label>
                        <input type="text" 
                            id="buscar_grupo"
                            class="form-control" 
                            placeholder="Semestre, grupo o carrera..."
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="buscarGrupos(this.value)"
                            data-required="true">
                        <div id="grupoResults" class="search-results-dropdown"></div>
                    </div>
                </div>
                <input type="hidden" id="id_grupo" value="">

                <!-- Subsección 1.7-Becas -->
                <h6 class="mb-4"><i class="fa-solid fa-hand-holding-dollar"></i> 1.7-BECAS</h6>
                <!-- Pregunta inicial -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label d-block">¿El alumno posee alguna beca activa por añadir? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_beca" id="becaSi" value="si" {{ $puedeEditar ? '' : 'disabled' }}>
                            <label class="form-check-label" for="becaSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_beca" id="becaNo" value="no" checked {{ $puedeEditar ? '' : 'disabled' }}>
                            <label class="form-check-label" for="becaNo">No</label>
                        </div>
                    </div>
                </div>
                <!-- Panel de becas (visible solo cuando selecciona "Sí") -->
                <div id="panelBecas" style="display: none;">
                    <!-- Barra de búsqueda y botones -->
                    <div class="row mb-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label">Buscar beca</label>
                            <div class="input-group">
                                <input type="text" 
                                    id="buscarBeca"
                                    class="form-control" 
                                    placeholder="Escriba el nombre de la beca..."
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="buscarBecas(this.value)">
                                <button type="button" class="btn btn-warning" id="btnAgregarBeca" disabled>
                                    <i class="fas fa-plus"></i> Añadir Beca
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btnCancelarEdicionBeca" style="display: none;">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                            <div id="becaResults" class="search-results-dropdown"></div>
                            <small class="text-muted">Seleccione una beca de la lista y luego presione "Añadir Beca"</small>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estatus</label>
                            <select id="estatusBecaSelect" class="form-select" {{ !$puedeEditar ? 'disabled' : '' }}>
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>
                        </div>
                        <div class="col-md-auto ms-auto d-flex align-items-end">
                            <a href="{{ route('becas.index') }}" 
                            class="btn btn-outline-warning" 
                            target="_blank"
                            rel="noopener noreferrer">
                                <i class="fas fa-external-link-alt me-2"></i>Ir a CONFIGURACIÓN DE BECAS
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Becas añadidas (tags) -->
                <div id="panelTags">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Becas asignadas:</label>
                            <div id="becasAsignadas" class="d-flex flex-wrap gap-2 p-3 bg-light rounded" style="min-height: 80px;">
                                <span class="text-muted">No hay becas asignadas</span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="becaSeleccionadaId" value="">
                <input type="hidden" id="becaEnEdicionId" value="">

                <!-- Subsección 1.8-Trabajo del Alumno -->
                <h6 class="mb-4"><i class="fa-solid fa-briefcase"></i> 1.8-TRABAJO DEL ALUMNO</h6>
                <!-- Pregunta inicial -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label d-block">¿El alumno posee algún empleo actualmente? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_trabajo" id="trabajoSi" value="si">
                            <label class="form-check-label" for="trabajoSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_trabajo" id="trabajoNo" value="no" checked>
                            <label class="form-check-label" for="trabajoNo">No</label>
                        </div>
                    </div>
                </div>
                <!-- Panel de trabajo (visible solo cuando selecciona "Sí") -->
                <div id="panelTrabajo" style="display; none">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Lugar de trabajo</label>
                            <input type="text" 
                                id="lugar_trabajo"
                                class="form-control" 
                                maxlength="30"
                                placeholder="Nombre de la empresa o institución"
                                autocomplete="off"
                                {{ !$puedeEditar ? 'readonly' : '' }}
                                onfocus="this.removeAttribute('readonly')"
                                oninput="updateCounter(this, 'lugarTrabajoCounter')">
                            <small class="text-muted"><span id="lugarTrabajoCounter">0</span>/30</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Horario laboral</label>
                            <input type="text" 
                                id="horario_laboral"
                                class="form-control" 
                                maxlength="50"
                                placeholder="Ej: 9:00 - 18:00, Tiempo completo, etc."
                                autocomplete="off"
                                {{ !$puedeEditar ? 'readonly' : '' }}
                                onfocus="this.removeAttribute('readonly')"
                                oninput="updateCounter(this, 'horarioLaboralCounter')">
                            <small class="text-muted"><span id="horarioLaboralCounter">0</span>/50</small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Domicilio del trabajo</label>
                            <input type="text" 
                                id="domicilio_trabajo"
                                class="form-control" 
                                maxlength="100"
                                placeholder="Calle, número, colonia, ciudad"
                                autocomplete="off"
                                {{ !$puedeEditar ? 'readonly' : '' }}
                                onfocus="this.removeAttribute('readonly')"
                                oninput="updateCounter(this, 'domicilioTrabajoCounter')">
                            <small class="text-muted"><span id="domicilioTrabajoCounter">0</span>/100</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Teléfono del trabajo</label>
                            <input type="tel" 
                                id="telefono_trabajo"
                                class="form-control" 
                                maxlength="10"
                                placeholder="10 dígitos"
                                autocomplete="off"
                                {{ !$puedeEditar ? 'readonly' : '' }}
                                onfocus="this.removeAttribute('readonly')"
                                oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateCounter(this, 'telefonoTrabajoCounter')">
                            <small class="text-muted"><span id="telefonoTrabajoCounter">0</span>/10</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2.-DATOS FAMILIARES -->
        <div class="accordion-section" id="section2">
            <div class="accordion-header" data-section="2">
                <h5><i class="fa-solid fa-people-roof"></i> 2.-DATOS FAMILIARES</h5>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
            </div>
            <div class="accordion-content">
                <!-- Subsección 2.1-Familiares y Relacionados -->
                <h6 class="mb-4"><i class="fa-solid fa-people-arrows"></i> 2.1-FAMILIARES Y RELACIONADOS</h6>               
                <!-- Nota de instrucciones -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Añadir la información de los padres del(la) alumno(a), de las demás personas con quienes comparta hogar, 
                    y de los demás familiares e individuos que el(la) interesado(a) solicite añadir.
                </div>
                <!-- Formulario para Agregar Familiar -->
                <div class="card mb-4">
                    <div class="card-body">
                        <!-- Subsección 2.1.1-Datos Personales -->
                        <h6 class="mb-3"><i class="fa-solid fa-user-plus"></i> 2.1.1-Datos Personales</h6>
                        <!-- Parentesco, estatus de vida, nombre completo -->
                        <div class="row mb-3">                            
                            <div class="col-md-2 position-relative">
                                <label class="form-label">Parentesco *</label>
                                <input type="text" 
                                    id="parentesco"
                                    class="form-control" 
                                    maxlength="20"
                                    placeholder="Ej: Padre, Madre, Tío..."
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="updateCounter(this, 'parentescoCounter'); buscarParentesco(this.value)"
                                    data-required="true">
                                <small class="text-muted"><span id="parentescoCounter">0</span>/20</small>
                                <div id="parentescoResults" class="search-results-dropdown"></div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label d-block">¿Vive? *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="familiar_vive" id="familiarViveSi" value="1" checked>
                                    <label class="form-check-label" for="familiarViveSi">Sí</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="familiar_vive" id="familiarViveNo" value="0">
                                    <label class="form-check-label" for="familiarViveNo">No</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Nombre(s) *</label>
                                <input type="text" 
                                    id="familiar_nombre"
                                    class="form-control" 
                                    maxlength="30"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="updateCounter(this, 'familiarNombreCounter')"
                                    data-required="true">
                                <small class="text-muted"><span id="familiarNombreCounter">0</span>/30</small>
                            </div>
                            <div class="col-md-3 position-relative">
                                <label class="form-label">Primer Apellido *</label>
                                <input type="text" 
                                    id="familiar_apellido1"
                                    class="form-control" 
                                    maxlength="20"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="updateCounter(this, 'familiarApellido1Counter'); buscarApellidoFamiliar('apellido1', this.value)"
                                    data-required="true">
                                <small class="text-muted"><span id="familiarApellido1Counter">0</span>/20</small>
                                <div id="familiarApellido1Results" class="search-results-dropdown"></div>
                            </div>
                            <div class="col-md-3 position-relative">
                                <label class="form-label">Segundo Apellido</label>
                                <input type="text" 
                                    id="familiar_apellido2"
                                    class="form-control" 
                                    maxlength="20"
                                    placeholder="Omitir si no existe"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="updateCounter(this, 'familiarApellido2Counter'); buscarApellidoFamiliar('apellido2', this.value)">
                                <small class="text-muted"><span id="familiarApellido2Counter">0</span>/20</small>
                                <div id="familiarApellido2Results" class="search-results-dropdown"></div>
                            </div>
                        </div>
                        <!-- Fecha de nacimiento, teléfono celular, escolaridad -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label d-block">Fecha de Nacimiento *</label>                                
                                <!-- Día, mes, año -->
                                <div class="d-flex gap-1"> 
                                    <select id="familiar_dia" class="form-select" style="width: 85px;">
                                        <option value="">Día</option>
                                        @for($i=1; $i<=31; $i++)
                                            <option value="{{ str_pad($i,2,'0',STR_PAD_LEFT) }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select id="familiar_mes" class="form-select" style="width: 125px;">
                                        <option value="">Mes</option>
                                        <option value="01">Enero</option>
                                        <option value="02">Febrero</option>
                                        <option value="03">Marzo</option>
                                        <option value="04">Abril</option>
                                        <option value="05">Mayo</option>
                                        <option value="06">Junio</option>
                                        <option value="07">Julio</option>
                                        <option value="08">Agosto</option>
                                        <option value="09">Septiembre</option>
                                        <option value="10">Octubre</option>
                                        <option value="11">Noviembre</option>
                                        <option value="12">Diciembre</option>
                                    </select>
                                    <select id="familiar_anio" class="form-select" style="width: 105px;">
                                        <option value="">Año</option>
                                        @for($i=date('Y'); $i>=1950; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>                                
                                <input type="hidden" id="familiar_fecha_nacimiento" name="fecha_nacimiento">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Teléfono Celular</label>
                                <input type="tel" 
                                    id="familiar_telefono"
                                    class="form-control" 
                                    maxlength="10"
                                    placeholder="10 dígitos"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateCounter(this, 'familiarTelefonoCounter')">
                                <small class="text-muted"><span id="familiarTelefonoCounter">0</span>/10</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Escolaridad *</label>
                                <select id="familiar_escolaridad" class="form-select" data-required="true">
                                    <option value="">—Seleccione una opción—</option>
                                    <option value="No tiene">No tiene</option>
                                    <option value="Primaria">Primaria</option>
                                    <option value="Secundaria">Secundaria</option>
                                    <option value="Bachillerato">Bachillerato</option>
                                    <option value="Educación Superior">Educación Superior</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <small class="text-muted">Seleccione el nivel que tenga terminado</small>
                            </div>
                        </div>

                        <!-- Subsección 2.1.2-Domicilio -->
                        <h6 class="mb-3 mt-4"><i class="fa-solid fa-location-dot"></i> 2.1.2-Domicilio</h6>
                        <!-- Estatus de domicilio actual -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label d-block">¿Comparte domicilio con el alumno? *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="comparte_domicilio" id="comparteSi" value="1" checked>
                                    <label class="form-check-label" for="comparteSi">Sí</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="comparte_domicilio" id="comparteNo" value="0">
                                    <label class="form-check-label" for="comparteNo">No</label>
                                </div>
                            </div>
                        </div>
                        <!-- Panel de domicilio diferente -->
                        <div id="panelDomicilioDiferente" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Calle *</label>
                                    <input type="text" 
                                        id="familiar_calle" 
                                        class="form-control" 
                                        maxlength="40"
                                        placeholder="Sólo nombre propio; omitir 'C.', 'Av.'..."
                                        autocomplete="off"
                                        {{ !$puedeEditar ? 'readonly' : '' }}
                                        onfocus="this.removeAttribute('readonly')"
                                        oninput="updateCounter(this, 'familiar_calleCounter')"
                                        data-required="true">
                                    <small class="text-muted"><span id="familiar_calleCounter">0</span>/40</small>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">No. Ext *</label>
                                    <input type="text" 
                                        id="familiar_num_ext" 
                                        class="form-control" 
                                        maxlength="10"
                                        autocomplete="off"
                                        {{ !$puedeEditar ? 'readonly' : '' }}
                                        onfocus="this.removeAttribute('readonly')"
                                        oninput="updateCounter(this, 'familiar_num_extCounter')"
                                        data-required="true">
                                    <small class="text-muted"><span id="familiar_num_extCounter">0</span>/10</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">No. Int</label>
                                    <input type="text" 
                                        id="familiar_num_int" 
                                        class="form-control" 
                                        maxlength="10" 
                                        placeholder="Opcional"
                                        autocomplete="off"
                                        {{ !$puedeEditar ? 'readonly' : '' }}
                                        onfocus="this.removeAttribute('readonly')"
                                        oninput="updateCounter(this, 'familiar_num_intCounter')">
                                    <small class="text-muted"><span id="familiar_num_intCounter">0</span>/10</small>
                                </div>
                                <div class="col-md-3 position-relative">
                                    <label class="form-label">Colonia *</label>
                                    <input type="text" 
                                        id="familiar_colonia" 
                                        class="form-control" 
                                        maxlength="40"
                                        maxlength="40"
                                        placeholder="Buscar o ingresar nueva colonia"
                                        autocomplete="off"
                                        {{ !$puedeEditar ? 'readonly' : '' }}
                                        onfocus="this.removeAttribute('readonly')" 
                                        oninput="updateCounter(this, 'familiar_coloniaCounter'); buscarColoniaFamiliar(this.value)"
                                        data-required="true">
                                    <small class="text-muted"><span id="familiar_coloniaCounter">0</span>/40</small>
                                    <div id="familiarColoniaResults" class="search-results-dropdown"></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Localidad *</label>
                                    <input type="text" id="familiar_localidad" class="form-control" maxlength="30"
                                        autocomplete="off"
                                        {{ !$puedeEditar ? 'readonly' : '' }}
                                        onfocus="this.removeAttribute('readonly')"
                                        oninput="updateCounter(this, 'familiar_localidadCounter')"
                                        data-required="true">
                                    <small class="text-muted"><span id="familiar_localidadCounter">0</span>/30</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Municipio *</label>
                                    <input type="text" id="familiar_municipio" class="form-control" maxlength="30"
                                        autocomplete="off"
                                        {{ !$puedeEditar ? 'readonly' : '' }}
                                        onfocus="this.removeAttribute('readonly')"
                                        oninput="updateCounter(this, 'familiar_municipioCounter')"
                                        data-required="true">
                                    <small class="text-muted"><span id="familiar_municipioCounter">0</span>/30</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">C.P. *</label>
                                    <input type="text" 
                                        id="familiar_cp" 
                                        class="form-control" 
                                        maxlength="5" 
                                        placeholder="5 dígitos"
                                        autocomplete="off"
                                        {{ !$puedeEditar ? 'readonly' : '' }}
                                        onfocus="this.removeAttribute('readonly')"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateCounter(this, 'familiar_cpCounter')"
                                        data-required="true">
                                    <small class="text-muted"><span id="familiar_cpCounter">0</span>/5</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Estado *</label>
                                    <input type="text" id="familiar_estado" class="form-control" maxlength="20"
                                        autocomplete="off"
                                        {{ !$puedeEditar ? 'readonly' : '' }}
                                        onfocus="this.removeAttribute('readonly')"
                                        oninput="updateCounter(this, 'familiar_estadoCounter')"
                                        data-required="true">
                                    <small class="text-muted"><span id="familiar_estadoCounter">0</span>/20</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Teléfono Domicilio</label>
                                    <input type="tel" id="familiar_telefono_domicilio" class="form-control" maxlength="10"
                                    placeholder="10 dígitos"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateCounter(this, 'familiar_telefono_domicilioCounter')">
                                <small class="text-muted"><span id="familiar_telefono_domicilioCounter">0</span>/10</small>
                                </div>
                            </div>
                            <input type="hidden" id="familiar_domicilio_id">
                        </div>

                        <!-- Subsección 2.1.3-Trabajo y Ocupación -->
                        <h6 class="mb-3 mt-4"><i class="fa-solid fa-briefcase"></i> 2.1.3-Trabajo y Ocupación</h6>
                        <!-- Ocupacion, lugar de trabajo, horario -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Ocupación *</label>
                                <input type="text" id="familiar_ocupacion" class="form-control" maxlength="30"
                                    placeholder="¿A qué se dedica?"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="updateCounter(this, 'familiar_ocupacionCounter')"
                                    data-required="true">
                                <small class="text-muted"><span id="familiar_ocupacionCounter">0</span>/30</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lugar de trabajo</label>
                                <input type="text" id="familiar_lugar_trabajo" class="form-control" maxlength="30"
                                    placeholder="Nombre de la empresa o institución"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="updateCounter(this, 'familiar_lugar_trabajoCounter')">
                                <small class="text-muted"><span id="familiar_lugar_trabajoCounter">0</span>/30</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Horario laboral</label>
                                <input type="text" id="familiar_horario_laboral" class="form-control" maxlength="50"
                                    placeholder="Ej: 9:00 - 18:00, Tiempo completo, etc."
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="updateCounter(this, 'familiar_horario_laboralCounter')">
                                <small class="text-muted"><span id="familiar_horario_laboralCounter">0</span>/50</small>
                            </div>
                        </div>
                        <!-- Domicilio del trabajo, teléfino -->
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Domicilio del trabajo</label>
                                <input type="text" id="familiar_domicilio_trabajo" class="form-control" maxlength="100"
                                    placeholder="Calle, número, colonia, ciudad"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="updateCounter(this, 'familiar_domicilio_trabajoCounter')">
                                <small class="text-muted"><span id="familiar_domicilio_trabajoCounter">0</span>/100</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Teléfono del trabajo</label>
                                <input type="tel" id="familiar_telefono_trabajo" class="form-control" maxlength="10" 
                                    placeholder="10 dígitos"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateCounter(this, 'familiar_telefono_trabajoCounter')">
                                <small class="text-muted"><span id="familiar_telefono_trabajoCounter">0</span>/10</small>
                            </div>
                        </div>

                        <!-- Subsección 2.1.4-Asignaciones -->
                        <h6 class="mb-3 mt-4"><i class="fa-solid fa-clipboard-list"></i> 2.1.4-Asignaciones</h6>
                        <!-- Nota de instrucciones -->
                        <div class="alert alert-warning small">
                            <i class="fas fa-info-circle"></i> Dar preferencia como primeros dos contactos de emergencia al padre y la madre, a menos que se
                        solicite lo contrario. Autorizar, como mínimo, un tercer y cuarto contactos de emergencia.
                        </div>
                        <!-- Autorización de tutor, autorización de contacto, prioridad de contacto -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label d-block">¿Autorizar como Tutor? *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="familiar_tutor" id="tutorSi" value="1">
                                    <label class="form-check-label" for="tutorSi">Sí</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="familiar_tutor" id="tutorNo" value="0" checked>
                                    <label class="form-check-label" for="tutorNo">No</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-block">¿Autorizar como Contacto de Emergencia? *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="familiar_contacto" id="contactoSi" value="1">
                                    <label class="form-check-label" for="contactoSi">Sí</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="familiar_contacto" id="contactoNo" value="0" checked>
                                    <label class="form-check-label" for="contactoNo">No</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="prioridadContainer" style="display: none;">
                                <label class="form-label">Prioridad como Contacto</label>
                                <input type="number" id="familiar_prioridad" class="form-control" min="-1" placeholder="1, 2, 3..." data-required="true">
                            </div>
                        </div>
                        <!-- Botones de acción para familiares -->
                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <!-- Botón para AÑADIR (solo en modo edición) -->
                                <button type="button" class="btn btn-primary" id="btnAgregarFamiliar" style="display: none;">
                                    <i class="fas fa-plus"></i> Añadir Familiar
                                </button>
                                
                                <!-- Botón para ACTUALIZAR (se muestra cuando se edita un familiar existente) -->
                                <button type="button" class="btn btn-primary" id="btnActualizarFamiliar" style="display: none;">
                                    <i class="fas fa-save"></i> Actualizar Familiar
                                </button>
                                
                                <!-- Botón para CANCELAR EDICIÓN (durante edición de familiar existente) -->
                                <button type="button" class="btn btn-outline-secondary" id="btnCancelarEdicionFamiliar" style="display: none;">
                                    <i class="fas fa-times me-2"></i>Cancelar Edición
                                </button>
                                
                                <!-- Botón para CANCELAR REGISTRO (limpia formulario, solo para nuevos) -->
                                <button type="button" class="btn btn-outline-secondary" id="btnCancelarRegistroFamiliar" style="display: none;">
                                    <i class="fas fa-times me-2"></i>Cancelar Registro
                                </button>
                                
                                <!-- Botón para CANCELAR CONSULTA (modo consulta) -->
                                <button type="button" class="btn btn-outline-secondary" id="btnCancelarConsultaFamiliar" style="display: none;">
                                    <i class="fas fa-times me-2"></i>Cancelar Consulta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Familiares añadidos -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">Familiares añadidos:</label>
                        <div id="familiaresAsignados" class="d-flex flex-wrap gap-2 p-3 bg-light rounded" style="min-height: 80px;">
                            <span class="text-muted">No hay familiares añadidos</span>
                        </div>
                    </div>
                </div>

                <!-- Subsección 2.2-Información Familiar -->
                <h6 class="mb-4"><i class="fa-solid fa-chart-simple"></i> 2.2-INFORMACIÓN FAMILIAR</h6>
                <!-- Estado Civil de los padre, gasto aproximado, ingreso aproximado -->
                <div class="row mb-3">
                    <div class="col-md-4 position-relative">
                        <label class="form-label">Estado Civil Actual de los Padres *</label>
                        <input type="text" 
                            id="estado_civil_padres"
                            class="form-control" 
                            maxlength="15"
                            placeholder="Ej: Casados, Divorciados, Unión Libre..."
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'estadoCivilCounter'); buscarEstadoCivil(this.value)"
                            data-required="true">
                        <small class="text-muted"><span id="estadoCivilCounter">0</span>/15</small>
                        <div id="estadoCivilResults" class="search-results-dropdown"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ingreso Familiar Aproximado</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                id="ingreso_familiar"
                                class="form-control" 
                                placeholder="0"
                                min="0"
                                step="1"
                                autocomplete="off"
                                {{ !$puedeEditar ? 'readonly' : '' }}
                                onfocus="this.removeAttribute('readonly')"
                                oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                        </div>
                        <small class="text-muted">Monto en pesos mexicanos (sin decimales)</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gasto Familiar Aproximado</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                id="gasto_familiar"
                                class="form-control" 
                                placeholder="0"
                                min="0"
                                step="1"
                                autocomplete="off"
                                {{ !$puedeEditar ? 'readonly' : '' }}
                                onfocus="this.removeAttribute('readonly')"
                                oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                        </div>
                        <small class="text-muted">Monto en pesos mexicanos (sin decimales)</small>
                    </div>
                </div>
                <!-- Casa propia, automóvil propio, servicios casa -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label d-block">¿Cuenta la familia con casa propia? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="casa_propia" id="casaPropiaSi" value="1">
                            <label class="form-check-label" for="casaPropiaSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="casa_propia" id="casaPropiaNo" value="0" checked>
                            <label class="form-check-label" for="casaPropiaNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block">¿Dispone la familia de automóvil propio? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="auto_propio" id="autoPropioSi" value="1">
                            <label class="form-check-label" for="autoPropioSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="auto_propio" id="autoPropioNo" value="0" checked>
                            <label class="form-check-label" for="autoPropioNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-block">¿Con cuáles servicios cuenta la casa familiar?</label>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="servicioLuz">
                                <label class="form-check-label" for="servicioLuz">Energía eléctrica</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="2" id="servicioAgua">
                                <label class="form-check-label" for="servicioAgua">Agua corriente</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="3" id="servicioDrenaje">
                                <label class="form-check-label" for="servicioDrenaje">Drenaje</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="4" id="servicioAlumbrado">
                                <label class="form-check-label" for="servicioAlumbrado">Alumbrado público</label>
                            </div>
                        </div>
                        <small class="text-muted">Seleccione todos los servicios con los que cuenta el hogar</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 3.-INFORMACIÓN SOCIOECONÓMICA PERSONAL -->
        <div class="accordion-section" id="section3">
            <div class="accordion-header" data-section="3">
                <h5><i class="fa-solid fa-chart-line"></i> 3.-INFORMACIÓN SOCIOECONÓMICA PERSONAL</h5>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
            </div>
            <div class="accordion-content">
                <!-- Automóvil propio -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label d-block">¿Dispone el alumno de automóvil propio? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="auto_propio_alumno" id="autoPropioAlumnoSi" value="1">
                            <label class="form-check-label" for="autoPropioAlumnoSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="auto_propio_alumno" id="autoPropioAlumnoNo" value="0" checked>
                            <label class="form-check-label" for="autoPropioAlumnoNo">No</label>
                        </div>
                    </div>
                </div>
                <!-- Transporte -->
                <div class="row mb-3">
                    <div class="col-md-5 position-relative">
                        <label class="form-label">¿En qué se transporta a la escuela? *</label>
                        <div class="input-group">
                            <input type="text" 
                                id="buscarTransporte"
                                class="form-control" 
                                placeholder="Ej: Automóvil, Autobús, Bicicleta, etc."
                                autocomplete="off"
                                {{ !$puedeEditar ? 'readonly' : '' }}
                                onfocus="this.removeAttribute('readonly')"
                                oninput="buscarTransportes(this.value)">
                            <button type="button" class="btn btn-success" id="btnAgregarTransporte" disabled>
                                <i class="fas fa-plus"></i> Añadir Transporte
                            </button>
                        </div>
                        <div id="transporteResults" class="search-results-dropdown"></div>
                        <small class="text-muted">Seleccione un transporte de la lista y luego presione "Añadir Transporte"</small>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Transportes añadidos *:</label>
                        <div id="transportesAsignados" class="d-flex flex-wrap gap-2 p-3 bg-light rounded" style="min-height: 80px;">
                            <span class="text-muted">No hay transportes añadidos</span>
                        </div>
                    </div>
                    <input type="hidden" id="transporteSeleccionadoId" value="">
                </div>
                <!-- Tiempo de traslado, Estado Civil del Alumno -->
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label class="form-label">Tiempo de traslado de ida y vuelta al Plantel *</label>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-1">
                                <input type="number" 
                                    id="traslado_horas"
                                    class="form-control" 
                                    style="width: 80px;"
                                    min="0"
                                    max="23"
                                    step="1"
                                    placeholder="0"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                                <span class="mx-2">horas y</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="number" 
                                    id="traslado_minutos"
                                    class="form-control" 
                                    style="width: 80px;"
                                    min="0"
                                    max="59"
                                    step="1"
                                    placeholder="0"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                                <span class="mx-2">minutos</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 position-relative">
                        <label class="form-label">Estado Civil Actual del Alumno *</label>
                        <input type="text" 
                            id="estado_civil_alumno"
                            class="form-control" 
                            maxlength="15"
                            placeholder="Ej: Soltero(a), Casado(a), Unión Libre..."
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'estadoCivilAlumnoCounter'); buscarEstadoCivilAlumno(this.value)"
                            data-required="true">
                        <small class="text-muted"><span id="estadoCivilAlumnoCounter">0</span>/15</small>
                        <div id="estadoCivilAlumnoResults" class="search-results-dropdown"></div>
                    </div>
                </div>
                <!-- Hijos del alumno -->
                <div class="row mb-3 align-items-start">
                    <!-- Pregunta inicial de hijos -->
                    <div class="col-md-3">
                        <label class="form-label d-block">¿Tiene hijos el(la) alumno(a)? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_hijos" id="hijosSi" value="1">
                            <label class="form-check-label" for="hijosSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_hijos" id="hijosNo" value="0" checked>
                            <label class="form-check-label" for="hijosNo">No</label>
                        </div>
                    </div>
                    <!-- Cantidad de hijos, edades de los hijos -->
                    <div id="panelHijos" class="col-md-9" style="display: none;">
                        <div class="row">
                            <div class="col-md-7">
                                <label class="form-label">Cantidad de hijos</label>
                                <input type="number" 
                                    id="num_hijos"
                                    class="form-control" 
                                    min="1"
                                    max="20"
                                    step="1"
                                    placeholder="Ej: 1"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                                <small class="text-muted">Ingrese la cantidad de hijos</small>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Edades de los hijos</label>
                                <input type="text" 
                                    id="edades_hijos"
                                    class="form-control" 
                                    maxlength="20"
                                    placeholder="Ej: 6, 4, 2"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="validarEdades(this.value)">
                                <small class="text-muted">Separe con comas (ej: 6, 4, 2)</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Apoyo económico -->
                <div class="row mb-3 align-items-start">
                    <!-- Pregunta inicial de apoyo econmico -->
                    <div class="col-md-4">
                        <label class="form-label d-block">¿Recibe apoyo económico de su familia? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="recibe_apoyo" id="apoyoSi" value="1">
                            <label class="form-check-label" for="apoyoSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="recibe_apoyo" id="apoyoNo" value="0" checked>
                            <label class="form-check-label" for="apoyoNo">No</label>
                        </div>
                    </div>
                    <div id="panelMontoApoyo" class="col-md-8" style="display: none;">
                        <div class="col-md-5">
                            <label class="form-label">Monto Aproximado</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                    id="monto_apoyo"
                                    class="form-control" 
                                    placeholder="0"
                                    min="0"
                                    step="1"
                                    autocomplete="off"
                                    {{ !$puedeEditar ? 'readonly' : '' }}
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                            </div>
                            <small class="text-muted">Monto en pesos mexicanos (sin decimales)</small>
                        </div>
                    </div>
                </div>
                <!-- Gasto en comida y transporte -->
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label class="form-label">Aproximadamente, ¿cuánto gasta en comida y transporte al día?</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                id="gasto_comida_transporte"
                                class="form-control" 
                                placeholder="0"
                                min="0"
                                step="1"
                                autocomplete="off"
                                {{ !$puedeEditar ? 'readonly' : '' }}
                                onfocus="this.removeAttribute('readonly')"
                                oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                        </div>
                        <small class="text-muted">Monto en pesos mexicanos (sin decimales)</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">¿Cuántas comidas hace al día?</label>
                        <input type="number" 
                            id="comidas_diarias"
                            class="form-control" 
                            min="1"
                            max="10"
                            step="1"
                            value="3"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="this.value = Math.abs(parseInt(this.value)) || 3">
                        <small class="text-muted">Por defecto: 3 comidas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 4.-DATOS ACADÉMICOS -->
        <div class="accordion-section" id="section4">
            <div class="accordion-header" data-section="4">
                <h5><i class="fa-solid fa-graduation-cap"></i> 4.-DATOS ACADÉMICOS</h5>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
            </div>
            <div class="accordion-content">
                <!-- Subsección 4.1-Herramientas -->
                <h6 class="mb-4"><i class="fa-solid fa-laptop"></i> 4.1-HERRAMIENTAS</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label d-block">Para llevar a cabo sus tareas y clases cuenta con:</label>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="dispositivoCelular">
                                <label class="form-check-label" for="dispositivoCelular">Celular</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="2" id="dispositivoComputadora">
                                <label class="form-check-label" for="dispositivoComputadora">Computadora</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="3" id="dispositivoInternet">
                                <label class="form-check-label" for="dispositivoInternet">Internet</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="4" id="dispositivoTablet">
                                <label class="form-check-label" for="dispositivoTablet">Tablet</label>
                            </div>
                        </div>
                        <small class="text-muted">Seleccione todas las herramientas con las que cuenta el alumno</small>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Otros dispositivos (escríbalos de ser necesario):</label>
                        <input type="text" 
                            id="otro_dispositivo"
                            class="form-control" 
                            maxlength="30"
                            placeholder="Ej: Impresora, Escáner, etc."
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'otroDispositivoCounter')">
                        <small class="text-muted"><span id="otroDispositivoCounter">0</span>/30</small>
                    </div>
                </div>
                
                <!-- Subsección 4.2-Problemas de Aprendizaje -->
                <h6 class="mb-4 mt-4"><i class="fa-solid fa-brain"></i> 4.2-PROBLEMAS DE APRENDIZAJE</h6>              
                <!-- Pregunta inicial -->
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label class="form-label d-block">¿Tiene diagnosticado algún problema de aprendizaje? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_problema" id="problemaSi" value="1">
                            <label class="form-check-label" for="problemaSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_problema" id="problemaNo" value="0" checked>
                            <label class="form-check-label" for="problemaNo">No</label>
                        </div>
                    </div>
                </div>                
                <!-- Panel de problemas (visible solo cuando selecciona "Sí") -->
                <div id="panelProblemas" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label d-block">En caso de identificar alguna característica específica, marque las casillas necesarias:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="1" id="problemaDislexia">
                                        <label class="form-check-label" for="problemaDislexia">1.-Dislexia (trastorno del aprendizaje de la lectoescritura).</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="2" id="problemaVisuales">
                                        <label class="form-check-label" for="problemaVisuales">2.-Visuales (miopía, hipermetropía, astigmatismo, presbicia).</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="3" id="problemaAuditivo">
                                        <label class="form-check-label" for="problemaAuditivo">3.-Auditivos (en general).</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="4" id="problemaNoEntender">
                                        <label class="form-check-label" for="problemaNoEntender">4.-No entender instrucciones y órdenes verbales.</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="5" id="problemaLentitud">
                                        <label class="form-check-label" for="problemaLentitud">5.-Lentitud en las respuestas.</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="6" id="problemaLenguaje">
                                        <label class="form-check-label" for="problemaLenguaje">6.-Problemas de lenguaje y dicción diferentes a la dislexia.</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="7" id="problemaComunicacion">
                                        <label class="form-check-label" for="problemaComunicacion">7.-Problemas de comunicación.</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="8" id="problemaDiscriminacion">
                                        <label class="form-check-label" for="problemaDiscriminacion">8.-Problemas de discriminación auditiva.</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="9" id="problemaGestos">
                                        <label class="form-check-label" for="problemaGestos">9.-Sustituir la expresión verbal por gestos.</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="10" id="problemaDistraccion">
                                        <label class="form-check-label" for="problemaDistraccion">10.-Distracción fácil con sonidos externos.</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="11" id="problemaTDAH">
                                        <label class="form-check-label" for="problemaTDAH">11.-Trastorno por déficit de atención e hiperactividad (TDAH).</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="12" id="problemaDiscapacidad">
                                        <label class="form-check-label" for="problemaDiscapacidad">12.-Discapacidad intelectual.</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Otros problemas -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Otros problemas (especifique):</label>
                            <textarea 
                                id="otro_problema"
                                class="form-control"
                                placeholder="Describa otros problemas de aprendizaje que presente el alumno y no estén contemplados en la lista anterior."
                                maxlength="255"
                                rows="1"
                                autocomplete="off"
                                {{ !$puedeEditar ? 'readonly' : '' }}
                                onfocus="this.removeAttribute('readonly')"
                                oninput="updateCounter(this, 'otroProblemaCounter')"></textarea>
                            <small class="text-muted"><span id="otroProblemaCounter">0</span>/255</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 5.-DATOS GENERALES DE SALUD -->
        <div class="accordion-section" id="section5">
            <div class="accordion-header" data-section="5">
                <h5><i class="fa-solid fa-heartbeat"></i> 5.-DATOS GENERALES DE SALUD</h5>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
            </div>
            <div class="accordion-content">
                <!-- Subsección 5.1-Datos Básicos -->
                <h6 class="mb-4"><i class="fa-solid fa-ruler"></i> 5.1-DATOS BÁSICOS</h6>
                <!-- Estatura, peso, tipo de sangre -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label">Estatura (cm) *</label>
                        <input type="number" 
                            id="estatura"
                            class="form-control" 
                            min="50"
                            max="250"
                            step="1"
                            placeholder="Ej: 165"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="this.value = Math.abs(parseInt(this.value)) || ''"
                            data-required="true">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Peso (kg) *</label>
                        <input type="number" 
                            id="peso"
                            class="form-control" 
                            min="10"
                            max="300"
                            step="0.1"
                            placeholder="Ej: 65.5"
                            lang="en-US" 
                            oninput="if(this.value < 0) this.value = Math.abs(this.value)"
                            data-required="true">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Sangre *</label>
                        <select id="tipo_sangre" class="form-select" data-required="true">
                            <option value="">—Seleccione una opción—</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                </div>
                <!-- Uso de anteojos, graduación de anteojos, cuadro básico de vacunas -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label d-block">¿Cuenta con el cuadro básico de vacunas? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cuadro_vacunas" id="vacunasSi" value="1">
                            <label class="form-check-label" for="vacunasSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cuadro_vacunas" id="vacunasNo" value="0" checked>
                            <label class="form-check-label" for="vacunasNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-block">¿Usa anteojos? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="usa_anteojos" id="anteojosSi" value="1">
                            <label class="form-check-label" for="anteojosSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="usa_anteojos" id="anteojosNo" value="0" checked>
                            <label class="form-check-label" for="anteojosNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-3" id="graduacionContainer" style="display: none;">
                        <label class="form-label">Graduación de Anteojos</label>
                        <input type="number" id="graduacion_anteojos" class="form-control" step="0.25" min="0" max="10" placeholder="Ej: 1.5">
                    </div>
                </div>

                <!-- Subsección 5.2-Padecimientos Físicos -->
                <h6 class="mb-4"><i class="fa-solid fa-bone"></i> 5.2-PADECIMIENTOS FÍSICOS</h6>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label d-block">Si es afirmativa la respuesta a alguna de las preguntas siguientes, especifique el padecimiento. ¿Algún tipo de...</label>
                    </div>
                </div>                
                <!-- Cirugía -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label d-block">...cirugía? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_cirugia" id="cirugiaSi" value="1">
                            <label class="form-check-label" for="cirugiaSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_cirugia" id="cirugiaNo" value="0" checked>
                            <label class="form-check-label" for="cirugiaNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-10" id="cirugiaContainer" style="display: none;">
                        <label class="form-label">Especifique</label>
                        <textarea id="cirugia" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'cirugiaCounter')"></textarea>
                        <small class="text-muted"><span id="cirugiaCounter">0</span>/255</small>
                    </div>
                </div>                
                <!-- Alergia -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label d-block">...alergia? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_alergia" id="alergiaSi" value="1">
                            <label class="form-check-label" for="alergiaSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_alergia" id="alergiaNo" value="0" checked>
                            <label class="form-check-label" for="alergiaNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-10" id="alergiaContainer" style="display: none;">
                        <label class="form-label">Especifique</label>
                        <textarea id="alergia" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'alergiaCounter')"></textarea>
                        <small class="text-muted"><span id="alergiaCounter">0</span>/255</small>
                    </div>
                </div>                
                <!-- Limitante físico -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label d-block">...limitante físico? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_limitante" id="limitanteSi" value="1">
                            <label class="form-check-label" for="limitanteSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_limitante" id="limitanteNo" value="0" checked>
                            <label class="form-check-label" for="limitanteNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-10" id="limitanteContainer" style="display: none;">
                        <label class="form-label">Especifique</label>
                        <textarea id="limitante_fisico" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'limitanteCounter')"></textarea>
                        <small class="text-muted"><span id="limitanteCounter">0</span>/255</small>
                    </div>
                </div>                
                <!-- Problema auditivo -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label d-block">...problema auditivo? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_auditivo" id="auditivoSi" value="1">
                            <label class="form-check-label" for="auditivoSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_auditivo" id="auditivoNo" value="0" checked>
                            <label class="form-check-label" for="auditivoNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-10" id="auditivoContainer" style="display: none;">
                        <label class="form-label">Especifique</label>
                        <textarea id="problema_auditivo" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'auditivoCounter')"></textarea>
                        <small class="text-muted"><span id="auditivoCounter">0</span>/255</small>
                    </div>
                </div>                
                <!-- Adicción -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label d-block">...adicción? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_adiccion" id="adiccionSi" value="1">
                            <label class="form-check-label" for="adiccionSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_adiccion" id="adiccionNo" value="0" checked>
                            <label class="form-check-label" for="adiccionNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-10" id="adiccionContainer" style="display: none;">
                        <label class="form-label">Especifique</label>
                        <textarea id="adiccion" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'adiccionCounter')"></textarea>
                        <small class="text-muted"><span id="adiccionCounter">0</span>/255</small>
                    </div>
                </div>                
                <!-- Padecimiento emocional -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label d-block">...padecimiento emocional? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_emocional" id="emocionalSi" value="1">
                            <label class="form-check-label" for="emocionalSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_emocional" id="emocionalNo" value="0" checked>
                            <label class="form-check-label" for="emocionalNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-10" id="emocionalContainer" style="display: none;">
                        <label class="form-label">Especifique</label>
                        <textarea id="padecimiento_emocional" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'emocionalCounter')"></textarea>
                        <small class="text-muted"><span id="emocionalCounter">0</span>/255</small>
                    </div>
                </div>                
                <!-- Enfermedad actual -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">¿Qué tipo de enfermedad padece actualmente?</label>
                        <textarea id="enfermedad_actual" class="form-control" maxlength="255" rows="1" placeholder="Describa en caso de haber." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'enfermedadCounter')"></textarea>
                        <small class="text-muted"><span id="enfermedadCounter">0</span>/255</small>
                    </div>
                </div>

                <!-- Subsección 5.3-Salud Mental -->
                <h6 class="mb-4"><i class="fa-solid fa-face-frown"></i> 5.3-SALUD MENTAL</h6>
                <!-- Pregunta inicial -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label d-block">¿Ha mostrado síntomas que afecten su salud mental y desempeño en clases? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_sintomas" id="sintomasSi" value="1">
                            <label class="form-check-label" for="sintomasSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_sintomas" id="sintomasNo" value="0" checked>
                            <label class="form-check-label" for="sintomasNo">No</label>
                        </div>
                    </div>
                </div>
                <!-- Panel de problemas (visible solo cuando selecciona "Sí") -->
                <div id="panelSintomas" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label d-block">En caso de identificar algún síntoma específico, marque las casillas necesarias:</label>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="sintomaAnsiedad">
                                    <label class="form-check-label" for="sintomaAnsiedad">Ansiedad</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="2" id="sintomaEstres">
                                    <label class="form-check-label" for="sintomaEstres">Estrés</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="3" id="sintomaDepresion">
                                    <label class="form-check-label" for="sintomaDepresion">Depresión</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="4" id="sintomaCulpa">
                                    <label class="form-check-label" for="sintomaCulpa">Síntomas de culpa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="5" id="sintomaTristeza">
                                    <label class="form-check-label" for="sintomaTristeza">Tristeza</label>
                                </div>
                            </div>
                            <small class="text-muted">Seleccione todos los síntomas que padezca el alumno</small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Otros síntomas (especifique):</label>
                            <textarea id="otro_sintoma" class="form-control" maxlength="255" rows="1" placeholder="Describa otros síntomas de salud mental que presente el alumno y no estén contemplados en la lista anterior." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'otroSintomaCounter')"></textarea>
                            <small class="text-muted"><span id="otroSintomaCounter">0</span>/255</small>
                        </div>
                    </div>
                </div>

                <!-- Subsección 5.4-Datos Específicos -->
                <h6 class="mb-4"><i class="fa-solid fa-pills"></i> 5.4-DATOS ESPECÍFICOS</h6>                
                <!-- Medicamento controlado -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label d-block">¿Toma algún medicamento controlado? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_medicamento" id="medicamentoSi" value="1">
                            <label class="form-check-label" for="medicamentoSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_medicamento" id="medicamentoNo" value="0" checked>
                            <label class="form-check-label" for="medicamentoNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-9" id="medicamentoContainer" style="display: none;">
                        <label class="form-label">¿Cuál?</label>
                        <textarea id="medicamento_controlado" class="form-control" maxlength="255" rows="1" placeholder="Nombre el o los medicamentos." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'medicamentoCounter')"></textarea>
                        <small class="text-muted"><span id="medicamentoCounter">0</span>/255</small>
                    </div>
                </div>                
                <!-- Alergia a medicamento -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label d-block">¿Alergia a algún medicamento? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_alergia_med" id="alergiaMedSi" value="1">
                            <label class="form-check-label" for="alergiaMedSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_alergia_med" id="alergiaMedNo" value="0" checked>
                            <label class="form-check-label" for="alergiaMedNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-9" id="alergiaMedContainer" style="display: none;">
                        <label class="form-label">¿Cuál?</label>
                        <textarea id="alergia_medicamento" class="form-control" maxlength="255" rows="1" placeholder="Nombre el o los medicamentos." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'alergiaMedCounter')"></textarea>
                        <small class="text-muted"><span id="alergiaMedCounter">0</span>/255</small>
                    </div>
                </div>                
                <!-- Hospitalización -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label d-block">¿Lo han hospitalizado en alguna ocasión? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_hospitalizacion" id="hospitalizacionSi" value="1">
                            <label class="form-check-label" for="hospitalizacionSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_hospitalizacion" id="hospitalizacionNo" value="0" checked>
                            <label class="form-check-label" for="hospitalizacionNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-9" id="hospitalizacionContainer" style="display: none;">
                        <label class="form-label">¿Por qué motivo?</label>
                        <textarea id="motivo_hospitalizacion" class="form-control" maxlength="255" rows="1" placeholder="Mencione los motivos." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'hospitalizacionCounter')"></textarea>
                        <small class="text-muted"><span id="hospitalizacionCounter">0</span>/255</small>
                    </div>
                </div>
                <!-- Diabetes, hipertensión, dolores de cabeza, dolores de estómago -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label d-block">¿Padece diabetes? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="diabetes" id="diabetesSi" value="1">
                            <label class="form-check-label" for="diabetesSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="diabetes" id="diabetesNo" value="0" checked>
                            <label class="form-check-label" for="diabetesNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-block">¿Padece hipertensión? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="hipertension" id="hipertensionSi" value="1">
                            <label class="form-check-label" for="hipertensionSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="hipertension" id="hipertensionNo" value="0" checked>
                            <label class="form-check-label" for="hipertensionNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">¿Padece dolores de cabeza constantes? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="dolores_cabeza" id="cabezaSi" value="1">
                            <label class="form-check-label" for="cabezaSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="dolores_cabeza" id="cabezaNo" value="0" checked>
                            <label class="form-check-label" for="cabezaNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">¿Padece dolores de estómago constantes? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="dolores_estomago" id="estomagoSi" value="1">
                            <label class="form-check-label" for="estomagoSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="dolores_estomago" id="estomagoNo" value="0" checked>
                            <label class="form-check-label" for="estomagoNo">No</label>
                        </div>
                    </div>
                </div>
                <!-- Pregunta de frecuencia -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label d-block">¿Con qué frecuencia acude...</label>
                    </div>
                </div>                
                <!-- Frecuencia médico -->
                <div class="row mb-3">
                    <div class="col-12 d-flex align-items-center flex-wrap gap-2">
                        <label class="form-label mb-0 me-4">...al médico?</label>
                        <label class="form-label mb-0">Cada</label>
                        <select id="medico_anios" class="form-select w-auto">
                            <option value="">0</option>
                            @for($i=1; $i<=10; $i++)
                                <option value="{{ $i*12 }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <span>año(s) y</span>
                        <select id="medico_meses" class="form-select w-auto">
                            <option value="">0</option>
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <span>mes(es)</span>
                        <div class="ms-3 d-flex gap-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="radio" name="frecuencia_medico_opcion" id="medicoNunca" value="0">
                                <label class="form-check-label" for="medicoNunca">Nunca</label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="radio" name="frecuencia_medico_opcion" id="medicoSoloEnfermo" value="-1" checked>
                                <label class="form-check-label" for="medicoSoloEnfermo">Sólo cuando se enferma</label>
                            </div>
                        </div>
                    </div>
                </div>                
                <!-- Frecuencia dentista -->
                <div class="row mb-3">
                    <div class="col-12 d-flex align-items-center flex-wrap gap-2">
                        <label class="form-label mb-0 me-4">...al dentista?</label>
                        <label class="form-label mb-0">Cada</label>
                        <select id="dentista_anios" class="form-select w-auto">
                            <option value="">0</option>
                            @for($i=1; $i<=10; $i++)
                                <option value="{{ $i*12 }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <span>año(s) y</span>
                        <select id="dentista_meses" class="form-select w-auto">
                            <option value="">0</option>
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <span>mes(es)</span>
                        <div class="ms-3 d-flex gap-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="radio" name="frecuencia_dentista_opcion" id="dentistaNunca" value="0">
                                <label class="form-check-label" for="dentistaNunca">Nunca</label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="radio" name="frecuencia_dentista_opcion" id="dentistaSoloEnfermo" value="-1" checked>
                                <label class="form-check-label" for="dentistaSoloEnfermo">Sólo cuando se enferma</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Campos ocultos para almacenar los valores de frecuencia -->
                <input type="hidden" id="frecuencia_medico" name="frecuencia_medico">
                <input type="hidden" id="frecuencia_dentista" name="frecuencia_dentista">
            </div>
        </div>

        <!-- SECCIÓN 6.-ACTIVIDADES RECREATIVAS -->
        <div class="accordion-section mb-5" id="section6">
            <div class="accordion-header" data-section="6">
                <h5><i class="fa-regular fa-futbol"></i> 6.-ACTIVIDADES RECREATIVAS</h5>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
            </div>
            <div class="accordion-content">
                <!-- Pasatiempo favorito -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">¿Pasatiempo Favorito del Alumno?</label>
                        <input type="text" 
                            id="pasatiempo_favorito"
                            class="form-control" 
                            maxlength="100"
                            placeholder="Ej: Dibujar, Leer, Ajedrez..."
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="updateCounter(this, 'pasatiempoCounter')">
                        <small class="text-muted"><span id="pasatiempoCounter">0</span>/100</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">¿Cuántas horas le dedica por semana?</label>
                        <input type="number" 
                            id="horas_pasatiempo"
                            class="form-control"
                            min="0"
                            max="168"
                            step="1"
                            placeholder="0"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                    </div>
                </div>
                <!-- Deportes que practica -->
                <div class="row mb-3">
                    <div class="col-md-5 position-relative">
                        <label class="form-label">Deportes que practica</label>
                        <div class="input-group">
                            <input type="text" 
                                id="buscarDeporte"
                                class="form-control" 
                                placeholder="Ej: Fútbol, Básquetbol, Natación, etc."
                                autocomplete="off"
                                {{ !$puedeEditar ? 'readonly' : '' }}
                                onfocus="this.removeAttribute('readonly')"
                                oninput="buscarDeportes(this.value)">
                            <button type="button" class="btn btn-danger" id="btnAgregarDeporte" disabled>
                                <i class="fas fa-plus"></i> Añadir Deporte
                            </button>
                        </div>
                        <div id="deporteResults" class="search-results-dropdown"></div>
                        <small class="text-muted">Seleccione un deporte de la lista y luego presione "Añadir Deporte"</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">¿Cuántas horas les dedica por semana?</label>
                        <input type="number" 
                            id="horas_deporte"
                            class="form-control"
                            min="0"
                            max="168"
                            step="1"
                            placeholder="0"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                    </div>
                </div>
                <!-- Deportes añadidos (tags) -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">Deportes añadidos:</label>
                        <div id="deportesAsignados" class="d-flex flex-wrap gap-2 p-3 bg-light rounded" style="min-height: 80px;">
                            <span class="text-muted">No hay deportes añadidos</span>
                        </div>
                    </div>
                </div>
                <!-- Horas dedicadas a TV y computadora -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label d-block">¿Cuántas horas dedica por día a...</label>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-auto d-flex align-items-center gap-2 me-4">
                        <label class="form-label mb-0">...ver televisión?</label>
                        <input type="number" 
                            id="horas_tv"
                            class="form-control" 
                            style="width: 80px;" 
                            min="0"
                            max="24"
                            step="1"
                            placeholder="0"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                        <span>horas</span>
                    </div>
                    <div class="col-auto d-flex align-items-center gap-2">
                        <label class="form-label mb-0">...estar frente a una computadora?</label>
                        <input type="number" 
                            id="horas_compu"
                            class="form-control" 
                            style="width: 80px;" 
                            min="0"
                            max="24"
                            step="1"
                            placeholder="0"
                            autocomplete="off"
                            {{ !$puedeEditar ? 'readonly' : '' }}
                            onfocus="this.removeAttribute('readonly')"
                            oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                        <span>horas</span>
                    </div>
                </div>
                <!-- Uso frecuente de computadora -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">¿Cuál es el uso más frecuente que le da a la computadora?</label>
                        <textarea id="uso_compu" class="form-control" maxlength="50" rows="1" placeholder="Ej: Tareas escolares, Juegos, Redes sociales..." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'usoCompuCounter')"></textarea>
                        <small class="text-muted"><span id="usoCompuCounter">0</span>/50</small>
                    </div>
                </div>
                <!-- Chatear -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label d-block">¿Le gusta chatear? *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="chatea" id="chatearSi" value="1">
                            <label class="form-check-label" for="chatearSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="chatea" id="chatearNo" value="0" checked>
                            <label class="form-check-label" for="chatearNo">No</label>
                        </div>
                    </div>
                    <div class="col-md-10" id="temasChatContainer" style="display: none;">
                        <label class="form-label">Si su respuesta es afirmativa, explicar sobre qué temas chatea y con quién lo hace</label>
                        <textarea id="temas_chat" class="form-control" maxlength="255" rows="2" placeholder="Ej: Habla de videojuegos con sus amigos, temas escolares con compañeros..." autocomplete="off" {{ !$puedeEditar ? 'readonly' : '' }} onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'temasChatCounter')"></textarea>
                        <small class="text-muted"><span id="temasChatCounter">0</span>/255</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de guardado - MODIFICADO -->
        <div class="row mt-4 pt-4 border-top">
            @if($puedeEditar)
            <div class="col-md-4">
                <label for="current_password_global" class="form-label">
                    <i class="fas fa-lock me-2"></i>Contraseña de Usuario *
                </label>
                <input type="password" 
                    class="form-control" 
                    id="current_password_global" 
                    placeholder="Ingrese su contraseña para guardar cambios"
                    required>
                <small class="text-muted">Requerida para confirmar cualquier modificación</small>
            </div>
            <div class="col-md-8 d-flex align-items-end justify-content-end">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" id="guardarTodosBtn" disabled>
                        <i class="fas fa-save me-2"></i>Guardar Todos los Cambios
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="cancelarTodoBtn" disabled>
                        <i class="fas fa-times me-2"></i>Cancelar Todo
                    </button>
                    <a href="{{ route('alumnos.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                    </a>
                </div>
            </div>
            @else
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Estás en modo solo lectura. No puedes realizar modificaciones.
                </div>
                <div class="mt-3 text-end">
                    <a href="{{ route('alumnos.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                    </a>
                </div>
            </div>
            @endif
        </div>

    </form>

    <!-- Botón flotante -->
    <button class="floating-bottom-btn" id="btnGoToBottom" title="Ir al final de la página">
        <i class="fas fa-arrow-down"></i>
    </button>
</div>
@endsection

@push('scripts')
<script>
// ===========================================
// VARIABLES GLOBALES
// ===========================================
let timeoutId = null;
let resultadosLugares = [];
let selectedIndex = -1;
let domicilioTimeout = null;
let resultadosDomicilios = [];
let selectedDomicilioIndex = -1;
let secundariaTimeout = null;
let resultadosSecundarias = [];
let selectedSecundariaIndex = -1;
let apellidoTimeout = null;
let resultadosApellido1 = [];
let resultadosApellido2 = [];
let selectedApellido1Index = -1;
let selectedApellido2Index = -1;
let grupoTimeout = null;
let resultadosGrupos = [];
let selectedGrupoIndex = -1;

// Variables de secciones
let familiaresTemporales = [];
let transportesTemporales = [];
let deportesTemporales = [];
let becasTemporales = [];
let trabajoData = null;
let familiarEnEdicion = null;
let familiarEnEdicionIndex = null;
let modoEdicionFamiliarActivo = false;

// Variables de búsqueda
let parentescoTimeout = null;
let resultadosParentescos = [];
let selectedParentescoIndex = -1;
let familiarApellidoTimeout = null;
let resultadosFamiliarApellido1 = [];
let resultadosFamiliarApellido2 = [];
let selectedFamiliarApellido1Index = -1;
let selectedFamiliarApellido2Index = -1;
let familiarColoniaTimeout = null;
let resultadosFamiliarColonias = [];
let selectedFamiliarColoniaIndex = -1;

// Frecuencias
let resetFrecuenciaMedico = null;
let resetFrecuenciaDentista = null;

// Becas
let becaTimeout = null;
let resultadosBecas = [];
let selectedBecaIndex = -1;
let becaEnEdicion = null;

// Transportes
let transporteTimeout = null;
let resultadosTransportes = [];
let selectedTransporteIndex = -1;

// Deportes
let deporteTimeout = null;
let resultadosDeportes = [];
let selectedDeporteIndex = -1;

// Estado civil
let estadoCivilTimeout = null;
let resultadosEstadoCivil = [];
let selectedEstadoCivilIndex = -1;
let estadoCivilAlumnoTimeout = null;
let resultadosEstadoCivilAlumno = [];
let selectedEstadoCivilAlumnoIndex = -1;

const conditionalRequiredFields = {
    familiar_prioridad: { dependsOn: () => document.getElementById('contactoSi')?.checked === true }
};

// ===========================================
// FUNCIONES DE UTILIDAD
// ===========================================

function updateCounter(input, counterId) {
    const counter = document.getElementById(counterId);
    if (counter && input) {
        counter.textContent = input.value.length;
    }
}

function updateHighlight(items, index) {
    items.forEach((item, i) => {
        if (i === index) {
            item.classList.add('highlighted');
            item.scrollIntoView({ block: 'nearest' });
        } else {
            item.classList.remove('highlighted');
        }
    });
}

function obtenerFechaFamiliar() {
    const dia = document.getElementById('familiar_dia')?.value;
    const mes = document.getElementById('familiar_mes')?.value;
    const anio = document.getElementById('familiar_anio')?.value;
    return (dia && mes && anio) ? `${anio}-${mes}-${dia}` : null;
}

function esCampoValido(elemento) {
    if (!elemento) return true;
    const tagName = elemento.tagName?.toLowerCase();
    const type = elemento.type;
    
    if (tagName === 'select') return elemento.value !== null && elemento.value !== '';
    if (type === 'radio') {
        const name = elemento.name;
        if (name) {
            const radioGroup = document.querySelectorAll(`input[name="${name}"]`);
            return Array.from(radioGroup).some(radio => radio.checked);
        }
        return elemento.checked;
    }
    if (type === 'checkbox') return true;
    return elemento.value !== null && elemento.value.trim() !== '';
}

function resaltarCampo(elemento) {
    if (!elemento) return;
    const originalBorder = elemento.style.border;
    const originalBackground = elemento.style.backgroundColor;
    elemento.style.border = '2px solid #dc3545';
    elemento.style.backgroundColor = '#fff0f0';
    setTimeout(() => {
        elemento.style.border = originalBorder;
        elemento.style.backgroundColor = originalBackground;
    }, 3000);
    const label = document.querySelector(`label[for="${elemento.id}"]`);
    if (label) {
        const originalColor = label.style.color;
        label.style.color = '#dc3545';
        setTimeout(() => {
            label.style.color = originalColor;
        }, 3000);
    }
}

// ===========================================
// VALIDACIÓN PRINCIPAL
// ===========================================

function getFirstInvalidFieldInSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) return null;

    if (sectionId === 'section1') {
        const nombreCampos = ['alumno_nombre', 'alumno_apellido1'];
        for (const campoId of nombreCampos) {
            const elemento = document.getElementById(campoId);
            if (elemento && (!elemento.value || elemento.value.trim() === '')) {
                return elemento;
            }
        }
        
        const lugarCampos = ['localidad', 'municipio', 'estado', 'pais'];
        for (const campoId of lugarCampos) {
            const elemento = document.getElementById(campoId);
            if (elemento && (!elemento.value || elemento.value.trim() === '')) {
                return elemento;
            }
        }
        
        const domicilioCampos = ['calle', 'num_ext', 'colonia', 'domicilio_localidad', 'domicilio_municipio', 'cp', 'domicilio_estado'];
        for (const campoId of domicilioCampos) {
            const elemento = document.getElementById(campoId);
            if (elemento && (!elemento.value || elemento.value.trim() === '')) {
                return elemento;
            }
        }
        
        const secundariaCampos = ['secundaria_nombre', 'secundaria_tipo', 'secundaria_localidad', 'secundaria_municipio', 'secundaria_estado', 'secundaria_pais'];
        for (const campoId of secundariaCampos) {
            const elemento = document.getElementById(campoId);
            if (elemento && (!elemento.value || elemento.value.trim() === '')) {
                return elemento;
            }
        }
        
        const grupoInput = document.getElementById('id_grupo');
        if (!grupoInput || !grupoInput.value) {
            return document.getElementById('buscar_grupo');
        }
        
        const otrosRequired = section.querySelectorAll('[data-required="true"]');
        for (const element of otrosRequired) {
            if (nombreCampos.includes(element.id) || 
                lugarCampos.includes(element.id) || 
                domicilioCampos.includes(element.id) || 
                secundariaCampos.includes(element.id)) {
                continue;
            }
            if (conditionalRequiredFields[element.id] && !conditionalRequiredFields[element.id].dependsOn()) continue;
            if (!esCampoValido(element)) return element;
        }
        
        return null;
    }
    
    let requiredElements = [];
    if (sectionId === 'section2') {
        const allRequired = section.querySelectorAll('[data-required="true"]');
        for (const el of allRequired) {
            if (!el.closest('.card-body')) {
                requiredElements.push(el);
            }
        }
        if (familiaresTemporales.length === 0) {
            return { isSectionHeader: true, message: 'Debe añadir al menos un familiar.' };
        }
        const tieneFamiliarVivo = familiaresTemporales.some(f => f.familiar.vive === true || f.familiar.vive === 1 || f.familiar.vive === '1');
        if (!tieneFamiliarVivo) {
            return { isSectionHeader: true, message: 'Debe añadir al menos un familiar vivo.' };
        }
    } else {
        requiredElements = section.querySelectorAll('[data-required="true"]');
    }
    
    if (sectionId === 'section3') {
        if (transportesTemporales.length === 0) {
            const campoTransporte = document.getElementById('buscarTransporte');
            campoTransporte.message = 'Debe añadir al menos un transporte.';
            return campoTransporte;
        }
        const horas = document.getElementById('traslado_horas')?.value;
        const minutos = document.getElementById('traslado_minutos')?.value;
        if ((!horas || horas === '0') && (!minutos || minutos === '0')) {
            return { isSectionHeader: true, message: 'Complete el tiempo de traslado (horas y/o minutos).' };
        }
    }
    
    for (const element of requiredElements) {
        if (conditionalRequiredFields[element.id] && !conditionalRequiredFields[element.id].dependsOn()) continue;
        if (!esCampoValido(element)) return element;
    }
    
    for (const [fieldId, config] of Object.entries(conditionalRequiredFields)) {
        if (config.dependsOn()) {
            const elemento = document.getElementById(fieldId);
            if (elemento && (!elemento.value || elemento.value.trim() === '')) {
                if (!elemento.closest('.card-body')) return elemento;
            }
            if (fieldId === 'familiar_prioridad' && elemento && parseInt(elemento.value) <= 0) {
                if (!elemento.closest('.card-body')) return elemento;
            }
        }
    }
    
    return null;
}

function validarFormularioCompleto() {
    const sections = ['section1', 'section2', 'section3', 'section4', 'section5', 'section6'];
    
    for (const sectionId of sections) {
        const invalidField = getFirstInvalidFieldInSection(sectionId);
        
        if (invalidField) {
            const section = document.getElementById(sectionId);
            if (section && !section.classList.contains('expanded')) {
                expandSectionWithScroll(sectionId);
            }
            
            setTimeout(() => {
                if (invalidField.message) {
                    scrollToSectionHeader(sectionId);
                    alert(invalidField.message);
                } else if (invalidField.isSectionHeader) {
                    scrollToSectionHeader(sectionId);
                    alert(invalidField.message);
                } else {
                    invalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    resaltarCampo(invalidField);
                    if (invalidField.message) alert(invalidField.message);
                }
            }, 200);
            return false;
        }
    }
    return true;
}

// ===========================================
// ACORDEONES
// ===========================================

let modoMultiplesSecciones = false;

function scrollToSectionHeader(sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) return;
    const header = section.querySelector('.accordion-header');
    if (header) {
        const offset = 80;
        const elementPosition = header.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;
        window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
    }
}

function initAccordions() {
    document.querySelectorAll('.accordion-section').forEach(section => {
        const header = section.querySelector('.accordion-header');
        header.addEventListener('click', function(e) {
            if (e.target.closest('.accordion-icon')) return;
            
            if (modoMultiplesSecciones) {
                section.classList.toggle('expanded');
            } else {
                if (section.classList.contains('expanded')) {
                    section.classList.remove('expanded');
                } else {
                    document.querySelectorAll('.accordion-section').forEach(other => {
                        other.classList.remove('expanded');
                    });
                    section.classList.add('expanded');
                    scrollToSectionHeader(section.id);
                }
            }
        });
    });
}

function toggleAllSectionsMode() {
    modoMultiplesSecciones = !modoMultiplesSecciones;
    
    if (!modoMultiplesSecciones) {
        document.querySelectorAll('.accordion-section').forEach(section => {
            section.classList.remove('expanded');
        });
    }
    
    fetch('{{ route("alumnos.toggle-sections-mode") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ modo_multiple: modoMultiplesSecciones })
    });
}

function toggleEditMode() {
    const isEditing = document.getElementById('toggle_edit_mode').checked;
    const formulario = document.getElementById('registroForm');
    
    if (!formulario) return;
    
    // 1. INPUTS de texto, email, teléfono, número, etc.
    const textInputs = formulario.querySelectorAll('input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]):not([type="button"]):not([type="submit"])');
    textInputs.forEach(input => {
        if (isEditing) {
            input.removeAttribute('readonly');
            input.disabled = false;
        } else {
            input.setAttribute('readonly', true);
            input.disabled = true;
        }
    });
    
    // 2. TEXTAREAS
    const textareas = formulario.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        if (isEditing) {
            textarea.removeAttribute('readonly');
            textarea.disabled = false;
        } else {
            textarea.setAttribute('readonly', true);
            textarea.disabled = true;
        }
    });
    
    // 3. SELECTS (menús desplegables)
    const selects = formulario.querySelectorAll('select');
    selects.forEach(select => {
        select.disabled = !isEditing;
    });
    
    // 4. CHECKBOXES
    const checkboxes = formulario.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.disabled = !isEditing;
    });
    
    // 5. RADIO BUTTONS
    const radios = formulario.querySelectorAll('input[type="radio"]');
    radios.forEach(radio => {
        radio.disabled = !isEditing;
    });
    
    // 6. BOTONES DE ACCIÓN (excepto los que deben estar siempre habilitados)
    const actionButtons = formulario.querySelectorAll('button:not(#btnBuscarAlumno):not(#btnLimpiarBusqueda):not(.floating-bottom-btn)');
    actionButtons.forEach(btn => {
        if (isEditing) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    });
    
    // 7. BOTONES ESPECIALES que deben comportarse diferente
    const btnAgregarBeca = document.getElementById('btnAgregarBeca');
    const btnAgregarTransporte = document.getElementById('btnAgregarTransporte');
    const btnAgregarDeporte = document.getElementById('btnAgregarDeporte');
    const btnAgregarFamiliar = document.getElementById('btnAgregarFamiliar');
    const guardarTodosBtn = document.getElementById('guardarTodosBtn');
    const cancelarTodoBtn = document.getElementById('cancelarTodoBtn');
    
    if (btnAgregarBeca) btnAgregarBeca.disabled = !isEditing;
    if (btnAgregarTransporte) btnAgregarTransporte.disabled = !isEditing;
    if (btnAgregarDeporte) btnAgregarDeporte.disabled = !isEditing;
    if (btnAgregarFamiliar) btnAgregarFamiliar.disabled = !isEditing;
    if (guardarTodosBtn) guardarTodosBtn.disabled = !isEditing;
    if (cancelarTodoBtn) cancelarTodoBtn.disabled = !isEditing;
    
    // 8. BOTONES DE ELIMINAR en los tags (como las x de becas, familiares, etc.)
    // Estos se manejan con CSS y eventos - los ocultamos visualmente cuando no se edita
    const closeButtons = document.querySelectorAll('.btn-close');
    closeButtons.forEach(btn => {
        if (isEditing) {
            btn.style.display = 'inline-block';
            btn.disabled = false;
        } else {
            btn.style.display = 'none';
            btn.disabled = true;
        }
    });
    
    // 9. INPUTS de búsqueda con autocompletado (deben mantener su funcionalidad de búsqueda pero no edición)
    const searchInputs = ['buscar_grupo', 'buscarBeca', 'buscarTransporte', 'buscarDeporte'];
    searchInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            if (isEditing) {
                input.removeAttribute('readonly');
                input.disabled = false;
            } else {
                input.setAttribute('readonly', true);
                input.disabled = true;
            }
        }
    });
    
    // 10. INPUTS de búsqueda de apellidos y lugares
    const autoCompleteInputs = ['alumno_apellido1', 'alumno_apellido2', 'localidad', 'colonia', 'secundaria_nombre', 'parentesco', 'familiar_apellido1', 'familiar_apellido2', 'familiar_colonia', 'estado_civil_padres', 'estado_civil_alumno'];
    autoCompleteInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            if (isEditing) {
                input.removeAttribute('readonly');
                input.disabled = false;
            } else {
                input.setAttribute('readonly', true);
                input.disabled = true;
            }
        }
    });
    
    // 11. SELECT de fecha del familiar (día, mes, año)
    const fechaSelects = ['familiar_dia', 'familiar_mes', 'familiar_anio', 'secundaria_tipo', 'familiar_escolaridad', 'tipo_sangre'];
    fechaSelects.forEach(id => {
        const select = document.getElementById(id);
        if (select) select.disabled = !isEditing;
    });
    
    // 12. CORREGIDO: Campo Número de Control - asegurar que se deshabilita completamente
    const numControl = document.getElementById('num_control');
    if (numControl) {
        if (isEditing) {
            numControl.removeAttribute('readonly');
            numControl.disabled = false;
        } else {
            numControl.setAttribute('readonly', true);
            numControl.disabled = true;
        }
    }
    
    // 13. CORREGIDO: Select de Estatus - asegurar que se deshabilita
    const estatusSelect = document.getElementById('estatusSelect');
    if (estatusSelect) {
        estatusSelect.disabled = !isEditing;
    }
    
    // 14. CORREGIDO: Radios de Tutor (sección 2.1) - forzar deshabilitación
    const tutorSiRadio = document.getElementById('tutorSi');
    const tutorNoRadio = document.getElementById('tutorNo');
    if (tutorSiRadio) {
        tutorSiRadio.disabled = !isEditing;
    }
    if (tutorNoRadio) {
        tutorNoRadio.disabled = !isEditing;
    }
    
    // 15. CORREGIDO: Input de prioridad de contacto
    const prioridadInput = document.getElementById('familiar_prioridad');
    if (prioridadInput) {
        if (isEditing) {
            prioridadInput.removeAttribute('readonly');
            prioridadInput.disabled = false;
        } else {
            prioridadInput.setAttribute('readonly', true);
            prioridadInput.disabled = true;
        }
    }
    
    // 16. CORREGIDO: El campo de contraseña
    const passwordInput = document.getElementById('current_password_global');
    if (passwordInput) {
        passwordInput.readOnly = !isEditing;
        passwordInput.disabled = !isEditing;
    }

    // Actualizar botones de familiares según el nuevo modo
    if (typeof actualizarBotonesFamiliares === 'function') {
        actualizarBotonesFamiliares();
    }

    // Forzar reinicio del toggle de contacto para actualizar estados
    if (typeof setupFamiliarContactoToggle === 'function') {
        // Remover event listeners viejos y volver a configurar
        const contactoSiRadio = document.getElementById('contactoSi');
        const contactoNoRadio = document.getElementById('contactoNo');
        if (contactoSiRadio && contactoNoRadio) {
            // Clonar y reemplazar para eliminar event listeners antiguos
            const newContactoSi = contactoSiRadio.cloneNode(true);
            const newContactoNo = contactoNoRadio.cloneNode(true);
            contactoSiRadio.parentNode.replaceChild(newContactoSi, contactoSiRadio);
            contactoNoRadio.parentNode.replaceChild(newContactoNo, contactoNoRadio);
            // Reasignar IDs
            newContactoSi.id = 'contactoSi';
            newContactoNo.id = 'contactoNo';
        }
        setupFamiliarContactoToggle();
    }
}

// ===========================================
// FUNCIÓN PRINCIPAL PARA ACTUALIZAR CONTADORES
// ===========================================

function actualizarTodosLosContadores() {
    const mapeoContadores = {
        'alumno_nombre': 'alumnoNombreCounter',
        'alumno_apellido1': 'alumnoApellido1Counter',
        'alumno_apellido2': 'alumnoApellido2Counter',
        'localidad': 'localidadCounter',
        'municipio': 'municipioCounter',
        'estado': 'estadoCounter',
        'pais': 'paisCounter',
        'calle': 'calleCounter',
        'num_ext': 'num_extCounter',
        'num_int': 'num_intCounter',
        'colonia': 'coloniaCounter',
        'domicilio_localidad': 'domicilioLocalidadCounter',
        'domicilio_municipio': 'domicilioMunicipioCounter',
        'cp': 'cpCounter',
        'domicilio_estado': 'domicilioEstadoCounter',
        'telefono_domicilio': 'telefonoDomicilioCounter',
        'secundaria_nombre': 'secundariaNombreCounter',
        'secundaria_localidad': 'secundariaLocalidadCounter',
        'secundaria_municipio': 'secundariaMunicipioCounter',
        'secundaria_estado': 'secundariaEstadoCounter',
        'secundaria_pais': 'secundariaPaisCounter',
        'telefono_celular': 'telefonoCelularCounter',
        'email_personal': 'emailPersonalCounter',
        'email_institucional': 'emailInstitucionalCounter',
        'curp': 'curpCounter',
        'nss': 'nssCounter',
        'lugar_trabajo': 'lugarTrabajoCounter',
        'horario_laboral': 'horarioLaboralCounter',
        'domicilio_trabajo': 'domicilioTrabajoCounter',
        'telefono_trabajo': 'telefonoTrabajoCounter',
        'parentesco': 'parentescoCounter',
        'familiar_nombre': 'familiarNombreCounter',
        'familiar_apellido1': 'familiarApellido1Counter',
        'familiar_apellido2': 'familiarApellido2Counter',
        'familiar_telefono': 'familiarTelefonoCounter',
        'familiar_ocupacion': 'familiar_ocupacionCounter',
        'familiar_lugar_trabajo': 'familiar_lugar_trabajoCounter',
        'familiar_horario_laboral': 'familiar_horario_laboralCounter',
        'familiar_domicilio_trabajo': 'familiar_domicilio_trabajoCounter',
        'familiar_telefono_trabajo': 'familiar_telefono_trabajoCounter',
        'familiar_calle': 'familiar_calleCounter',
        'familiar_num_ext': 'familiar_num_extCounter',
        'familiar_num_int': 'familiar_num_intCounter',
        'familiar_colonia': 'familiar_coloniaCounter',
        'familiar_localidad': 'familiar_localidadCounter',
        'familiar_municipio': 'familiar_municipioCounter',
        'familiar_cp': 'familiar_cpCounter',
        'familiar_estado': 'familiar_estadoCounter',
        'familiar_telefono_domicilio': 'familiar_telefono_domicilioCounter',
        'estado_civil_padres': 'estadoCivilCounter',
        'estado_civil_alumno': 'estadoCivilAlumnoCounter',
        'otro_dispositivo': 'otroDispositivoCounter',
        'otro_problema': 'otroProblemaCounter',
        'cirugia': 'cirugiaCounter',
        'alergia': 'alergiaCounter',
        'limitante_fisico': 'limitanteCounter',
        'problema_auditivo': 'auditivoCounter',
        'adiccion': 'adiccionCounter',
        'padecimiento_emocional': 'emocionalCounter',
        'enfermedad_actual': 'enfermedadCounter',
        'otro_sintoma': 'otroSintomaCounter',
        'medicamento_controlado': 'medicamentoCounter',
        'alergia_medicamento': 'alergiaMedCounter',
        'motivo_hospitalizacion': 'hospitalizacionCounter',
        'pasatiempo_favorito': 'pasatiempoCounter',
        'uso_compu': 'usoCompuCounter',
        'temas_chat': 'temasChatCounter'
    };
    
    for (const [inputId, counterId] of Object.entries(mapeoContadores)) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);
        if (input && counter) {
            counter.textContent = input.value ? input.value.length : 0;
        }
    }
    
    const numControl = document.getElementById('num_control');
    const numControlCounter = document.getElementById('numControlCounter');
    if (numControl && numControlCounter) {
        numControlCounter.textContent = numControl.value ? numControl.value.length : 0;
    }
}

// ===========================================
// BÚSQUEDA DE ALUMNOS
// ===========================================

let busquedaTimeout = null;
let resultadosAlumnos = [];
let selectedAlumnoIndex = -1;

function setupBusquedaAlumnos() {
    const busquedaInput = document.getElementById('busqueda_alumno');
    const resultsDiv = document.getElementById('resultadosBusqueda');
    const btnBuscar = document.getElementById('btnBuscarAlumno');
    const alumnoIdHidden = document.getElementById('alumno_id_selected');
    const btnLimpiar = document.getElementById('btnLimpiarBusqueda');
    
    if (!busquedaInput || !resultsDiv) return;
    
    // Variable para almacenar el ID del alumno seleccionado (sin cargar aún)
    let pendingAlumnoId = null;
    let pendingAlumnoText = null;
    
    // Limpiar pending cuando se modifica el input manualmente
    busquedaInput.addEventListener('input', function() {
        pendingAlumnoId = null;
        pendingAlumnoText = null;
        actualizarBotonLimpiar();
        
        const query = this.value.trim();
        
        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }
        
        clearTimeout(busquedaTimeout);
        busquedaTimeout = setTimeout(() => {
            fetch('{{ route("alumnos.buscar-alumnos-auto") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ busqueda: query, mostrar_inactivos: true })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.alumnos && data.alumnos.length > 0) {
                    resultsDiv.innerHTML = '';
                    resultadosAlumnos = data.alumnos;
                    
                    data.alumnos.forEach((alumno, index) => {
                        const div = document.createElement('div');
                        div.className = 'search-result-item';
                        div.dataset.index = index;
                        div.dataset.id = alumno.id;
                        div.dataset.text = `${alumno.num_control || ''} - ${alumno.nombre} ${alumno.apellido1}`;
                        div.innerHTML = `<strong>${alumno.num_control || 'Sin control'}</strong> - ${alumno.nombre} ${alumno.apellido1}<br>
                                        <small>Grupo: ${alumno.grupo || 'Sin asignar'} | Estatus: ${alumno.estatus ? 'Activo' : 'Inactivo'}</small>`;
                        div.addEventListener('click', () => {
                            // SOLO llenar el campo, NO cargar datos automáticamente
                            pendingAlumnoId = alumno.id;
                            pendingAlumnoText = div.dataset.text;
                            busquedaInput.value = pendingAlumnoText;
                            if (alumnoIdHidden) alumnoIdHidden.value = pendingAlumnoId;
                            resultsDiv.style.display = 'none';
                            actualizarBotonLimpiar();
                        });
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.style.display = 'block';
                    selectedAlumnoIndex = -1;
                } else {
                    resultsDiv.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
                resultsDiv.style.display = 'none';
            });
        }, 300);
    });
    
    // Navegación con teclado
    busquedaInput.addEventListener('keydown', function(e) {
        if (resultsDiv.style.display !== 'block' || resultadosAlumnos.length === 0) return;
        
        const items = resultsDiv.querySelectorAll('.search-result-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedAlumnoIndex = (selectedAlumnoIndex + 1) % items.length;
            updateHighlight(items, selectedAlumnoIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedAlumnoIndex = (selectedAlumnoIndex - 1 + items.length) % items.length;
            updateHighlight(items, selectedAlumnoIndex);
        } else if (e.key === 'Enter' && selectedAlumnoIndex >= 0) {
            e.preventDefault();
            const selectedItem = items[selectedAlumnoIndex];
            const alumnoId = selectedItem.dataset.id;
            const alumnoText = selectedItem.dataset.text;
            
            // SOLO llenar el campo, NO cargar datos automáticamente
            pendingAlumnoId = alumnoId;
            pendingAlumnoText = alumnoText;
            busquedaInput.value = pendingAlumnoText;
            if (alumnoIdHidden) alumnoIdHidden.value = pendingAlumnoId;
            resultsDiv.style.display = 'none';
            actualizarBotonLimpiar();
        }
    });
    
    // Botón de búsqueda - aquí es donde realmente se cargan los datos
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function() {
            const busquedaValue = busquedaInput.value;
            // Priorizar el ID pendiente si existe
            let idACargar = pendingAlumnoId;
            
            if (!idACargar && alumnoIdHidden && alumnoIdHidden.value) {
                idACargar = alumnoIdHidden.value;
            }
            
            if (idACargar) {
                cargarDatosAlumnoCompleto(idACargar);
                // Limpiar pending después de cargar
                pendingAlumnoId = null;
                pendingAlumnoText = null;
            } else if (busquedaValue) {
                // Si no hay ID, hacer una búsqueda por texto (redirección)
                window.location.href = '{{ route("alumnos.consulta-individual") }}?busqueda=' + encodeURIComponent(busquedaValue);
            }
        });
    }
    
    // Enter key en el input - ahora solo ejecuta la búsqueda si hay selección pendiente o ID
    busquedaInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (btnBuscar) btnBuscar.click();
        }
    });
    
    // Botón limpiar
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            pendingAlumnoId = null;
            pendingAlumnoText = null;
            if (alumnoIdHidden) alumnoIdHidden.value = '';
            busquedaInput.value = '';
            resultsDiv.style.display = 'none';
            actualizarBotonLimpiar();
            window.location.href = '{{ route("alumnos.consulta-individual") }}';
        });
    }
}

// Función para cargar todos los datos del alumno en el formulario
function cargarDatosAlumnoCompleto(alumnoId) {
    if (!alumnoId) {
        console.warn('No se proporcionó ID de alumno');
        return;
    }
    
    // Mostrar indicador de carga
    const btnBuscar = document.getElementById('btnBuscarAlumno');
    const originalText = btnBuscar ? btnBuscar.innerHTML : '';
    if (btnBuscar) {
        btnBuscar.disabled = true;
        btnBuscar.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    fetch(`{{ url("alumnos/obtener-alumno-completo") }}/${alumnoId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.alumno) {
            const alumno = data.alumno;
            
            // 1. DATOS DE LA SECCIÓN 0 (ENCABEZADO)
            // Número de control
            const numControlInput = document.getElementById('num_control');
            if (numControlInput) {
                numControlInput.value = alumno.num_control || '';
                const numControlCounter = document.getElementById('numControlCounter');
                if (numControlCounter) numControlCounter.textContent = (alumno.num_control || '').length;
            }
            
            // Estatus
            const estatusSelect = document.getElementById('estatusSelect');
            if (estatusSelect) {
                estatusSelect.value = alumno.estatus ? '1' : '0';
            }
            
            // Foto
            const fotoImg = document.getElementById('fotoAlumno');
            const nombreFotoSpan = document.getElementById('nombreFoto');
            if (fotoImg) {
                if (alumno.foto) {
                    fotoImg.src = `{{ asset('fotos') }}/${alumno.foto}.jpg`;
                    fotoImg.onerror = function() { 
                        this.src = '{{ asset('imagenes/sin-foto.jpg') }}';
                        if (nombreFotoSpan) nombreFotoSpan.textContent = alumno.foto + ' (no encontrada)';
                    };
                    if (nombreFotoSpan) nombreFotoSpan.textContent = alumno.foto;
                } else {
                    fotoImg.src = '{{ asset('imagenes/sin-foto.jpg') }}';
                    if (nombreFotoSpan) nombreFotoSpan.textContent = 'Ninguna';
                }
            }
            
            // Fecha y edad desde CURP
            if (alumno.curp) {
                actualizarFechaEdadDesdeCURPManual(alumno.curp);
            } else {
                const fechaDiv = document.getElementById('fecha_nacimiento');
                if (fechaDiv) fechaDiv.textContent = '—';
                const edadDiv = document.getElementById('edad_actual');
                if (edadDiv) edadDiv.textContent = '— años';
            }
            
            // 2. SECCIÓN 1 - DATOS PERSONALES
            // Nombre completo
            const nombreInput = document.getElementById('alumno_nombre');
            if (nombreInput) nombreInput.value = alumno.nombre || '';
            
            const apellido1Input = document.getElementById('alumno_apellido1');
            if (apellido1Input) apellido1Input.value = alumno.apellido1 || '';
            
            const apellido2Input = document.getElementById('alumno_apellido2');
            if (apellido2Input) apellido2Input.value = alumno.apellido2 || '';
            
            const alumnoIdHidden = document.getElementById('alumnoId');
            if (alumnoIdHidden) alumnoIdHidden.value = alumno.id;
            
            // Lugar de nacimiento
            if (alumno.lugar_nacimiento) {
                const localidadInput = document.getElementById('localidad');
                if (localidadInput) localidadInput.value = alumno.lugar_nacimiento.localidad || '';
                
                const municipioInput = document.getElementById('municipio');
                if (municipioInput) municipioInput.value = alumno.lugar_nacimiento.municipio || '';
                
                const estadoInput = document.getElementById('estado');
                if (estadoInput) estadoInput.value = alumno.lugar_nacimiento.estado || '';
                
                const paisInput = document.getElementById('pais');
                if (paisInput) paisInput.value = alumno.lugar_nacimiento.pais || '';
                
                const lugarNacimientoId = document.getElementById('lugarNacimientoId');
                if (lugarNacimientoId) lugarNacimientoId.value = alumno.lugar_nacimiento.id || '';
            }
            
            // Domicilio
            if (alumno.domicilio) {
                const calleInput = document.getElementById('calle');
                if (calleInput) calleInput.value = alumno.domicilio.calle || '';
                
                const numExtInput = document.getElementById('num_ext');
                if (numExtInput) numExtInput.value = alumno.domicilio.num_ext || '';
                
                const numIntInput = document.getElementById('num_int');
                if (numIntInput) numIntInput.value = alumno.domicilio.num_int || '';
                
                const coloniaInput = document.getElementById('colonia');
                if (coloniaInput) coloniaInput.value = alumno.domicilio.colonia || '';
                
                const domicilioLocalidadInput = document.getElementById('domicilio_localidad');
                if (domicilioLocalidadInput) domicilioLocalidadInput.value = alumno.domicilio.localidad || '';
                
                const domicilioMunicipioInput = document.getElementById('domicilio_municipio');
                if (domicilioMunicipioInput) domicilioMunicipioInput.value = alumno.domicilio.municipio || '';
                
                const cpInput = document.getElementById('cp');
                if (cpInput) cpInput.value = alumno.domicilio.cp || '';
                
                const domicilioEstadoInput = document.getElementById('domicilio_estado');
                if (domicilioEstadoInput) domicilioEstadoInput.value = alumno.domicilio.estado || '';
                
                const telefonoDomicilioInput = document.getElementById('telefono_domicilio');
                if (telefonoDomicilioInput) telefonoDomicilioInput.value = alumno.domicilio.telefono_domicilio || '';
                
                const domicilioIdHidden = document.getElementById('domicilioId');
                if (domicilioIdHidden) domicilioIdHidden.value = alumno.domicilio.id || '';
            }
            
            // Secundaria
            if (alumno.secundaria) {
                const secundariaNombreInput = document.getElementById('secundaria_nombre');
                if (secundariaNombreInput) secundariaNombreInput.value = alumno.secundaria.nombre || '';
                
                const secundariaTipoSelect = document.getElementById('secundaria_tipo');
                if (secundariaTipoSelect) secundariaTipoSelect.value = alumno.secundaria.tipo || '';
                
                const secundariaLocalidadInput = document.getElementById('secundaria_localidad');
                if (secundariaLocalidadInput) secundariaLocalidadInput.value = alumno.secundaria.localidad || '';
                
                const secundariaMunicipioInput = document.getElementById('secundaria_municipio');
                if (secundariaMunicipioInput) secundariaMunicipioInput.value = alumno.secundaria.municipio || '';
                
                const secundariaEstadoInput = document.getElementById('secundaria_estado');
                if (secundariaEstadoInput) secundariaEstadoInput.value = alumno.secundaria.estado || '';
                
                const secundariaPaisInput = document.getElementById('secundaria_pais');
                if (secundariaPaisInput) secundariaPaisInput.value = alumno.secundaria.pais || '';
                
                const secundariaIdHidden = document.getElementById('secundariaId');
                if (secundariaIdHidden) secundariaIdHidden.value = alumno.secundaria.id || '';
            }
            
            // Datos de contacto
            const telefonoCelularInput = document.getElementById('telefono_celular');
            if (telefonoCelularInput) telefonoCelularInput.value = alumno.telefono_celular || '';
            
            const emailPersonalInput = document.getElementById('email_personal');
            if (emailPersonalInput) emailPersonalInput.value = alumno.email_personal || '';
            
            const emailInstitucionalInput = document.getElementById('email_institucional');
            if (emailInstitucionalInput) emailInstitucionalInput.value = alumno.email_institucional || '';
            
            const curpInput = document.getElementById('curp');
            if (curpInput) curpInput.value = alumno.curp || '';
            
            const nssInput = document.getElementById('nss');
            if (nssInput) nssInput.value = alumno.nss || '';
            
            // Grupo
            const buscarGrupoInput = document.getElementById('buscar_grupo');
            const idGrupoHidden = document.getElementById('id_grupo');
            if (alumno.grupo) {
                if (buscarGrupoInput) buscarGrupoInput.value = `${alumno.grupo.semestre} ${alumno.grupo.grupo} - ${alumno.grupo.carrera}`;
                if (idGrupoHidden) idGrupoHidden.value = alumno.grupo.id;
            } else {
                if (buscarGrupoInput) buscarGrupoInput.value = '';
                if (idGrupoHidden) idGrupoHidden.value = '';
            }

            // SECCIÓN 1.7 - BECAS
            if (alumno.becas && alumno.becas.length > 0) {
                becasTemporales = alumno.becas.map(b => ({
                    id: String(b.id),
                    nombre: b.nombre,
                    activa: b.activa === true || b.activa === 1 || b.activa === '1'
                }));
                renderizarBecasAsignadas();
                
                const becaSiRadio = document.getElementById('becaSi');
                if (becaSiRadio) {
                    becaSiRadio.checked = true;
                    document.getElementById('panelBecas').style.display = 'block';
                }
            } else {
                becasTemporales = [];
                renderizarBecasAsignadas();
                document.getElementById('becaNo').checked = true;
                document.getElementById('panelBecas').style.display = 'none';
                document.getElementById('buscarBeca').value = '';
                document.getElementById('becaSeleccionadaId').value = '';
                document.getElementById('btnAgregarBeca').disabled = true;
            }

            // SECCIÓN 1.8 - TRABAJO DEL ALUMNO
            if (alumno.trabajo) {
                document.getElementById('lugar_trabajo').value = alumno.trabajo.lugar_trabajo || '';
                document.getElementById('horario_laboral').value = alumno.trabajo.horario_laboral || '';
                document.getElementById('domicilio_trabajo').value = alumno.trabajo.domicilio_trabajo || '';
                document.getElementById('telefono_trabajo').value = alumno.trabajo.telefono_trabajo || '';
                
                if (alumno.trabajo.lugar_trabajo || alumno.trabajo.horario_laboral) {
                    document.getElementById('trabajoSi').checked = true;
                    document.getElementById('panelTrabajo').style.display = 'block';
                }
            } else {
                document.getElementById('trabajoNo').checked = true;
                document.getElementById('panelTrabajo').style.display = 'none';
                document.getElementById('lugar_trabajo').value = '';
                document.getElementById('horario_laboral').value = '';
                document.getElementById('domicilio_trabajo').value = '';
                document.getElementById('telefono_trabajo').value = '';
            }

            // SECCIÓN 2.1 - FAMILIARES Y RELACIONADOS
            if (alumno.familiares && alumno.familiares.length > 0) {
                familiaresTemporales = alumno.familiares;
                renderizarFamiliaresAsignados();
            } else {
                familiaresTemporales = [];
                renderizarFamiliaresAsignados();
            }

            // SECCIÓN 2.2 - INFORMACIÓN FAMILIAR
            if (alumno.datos_familiares) {
                const df = alumno.datos_familiares;
                document.getElementById('estado_civil_padres').value = df.estado_civil_padres || '';
                document.getElementById('ingreso_familiar').value = df.ingreso_familiar_aprox || '';
                document.getElementById('gasto_familiar').value = df.gasto_familiar_aprox || '';
                
                // Casa propia
                if (df.casa_propia) {
                    document.getElementById('casaPropiaSi').checked = true;
                } else {
                    document.getElementById('casaPropiaNo').checked = true;
                }
                
                // Auto propio familia
                if (df.auto_propio_familia) {
                    document.getElementById('autoPropioSi').checked = true;
                } else {
                    document.getElementById('autoPropioNo').checked = true;
                }
                
                // Servicios (usando servicios_array que viene del backend)
                if (df.servicios_array && df.servicios_array.length > 0) {
                    const servicios = df.servicios_array;
                    document.getElementById('servicioLuz').checked = servicios.includes(1);
                    document.getElementById('servicioAgua').checked = servicios.includes(2);
                    document.getElementById('servicioDrenaje').checked = servicios.includes(3);
                    document.getElementById('servicioAlumbrado').checked = servicios.includes(4);
                } else {
                    document.getElementById('servicioLuz').checked = false;
                    document.getElementById('servicioAgua').checked = false;
                    document.getElementById('servicioDrenaje').checked = false;
                    document.getElementById('servicioAlumbrado').checked = false;
                }
            } else {
                // Resetear valores si no hay datos
                document.getElementById('estado_civil_padres').value = '';
                document.getElementById('ingreso_familiar').value = '';
                document.getElementById('gasto_familiar').value = '';
                document.getElementById('casaPropiaNo').checked = true;
                document.getElementById('autoPropioNo').checked = true;
                document.getElementById('servicioLuz').checked = false;
                document.getElementById('servicioAgua').checked = false;
                document.getElementById('servicioDrenaje').checked = false;
                document.getElementById('servicioAlumbrado').checked = false;
            }

            // SECCIÓN 3 - INFORMACIÓN SOCIOECONÓMICA
            if (alumno.info_socioeco) {
                const ise = alumno.info_socioeco;
                
                // Auto propio alumno
                if (ise.auto_propio_alumno) {
                    document.getElementById('autoPropioAlumnoSi').checked = true;
                } else {
                    document.getElementById('autoPropioAlumnoNo').checked = true;
                }
                
                // Transportes (ya viene como array desde el cast del modelo)
                if (ise.transporte && ise.transporte.length > 0) {
                    transportesTemporales = [...ise.transporte];
                    renderizarTransportesAsignados();
                } else {
                    transportesTemporales = [];
                    renderizarTransportesAsignados();
                }
                
                // Tiempo de traslado
                document.getElementById('traslado_horas').value = ise.traslado_horas || '';
                document.getElementById('traslado_minutos').value = ise.traslado_minutos || '';
                
                // Estado civil alumno
                document.getElementById('estado_civil_alumno').value = ise.estado_civil_alumno || '';
                
                // Hijos
                if (ise.num_hijos > 0) {
                    document.getElementById('hijosSi').checked = true;
                    document.getElementById('panelHijos').style.display = 'flex';
                    document.getElementById('num_hijos').value = ise.num_hijos;
                    document.getElementById('edades_hijos').value = ise.edades_hijos || '';
                } else {
                    document.getElementById('hijosNo').checked = true;
                    document.getElementById('panelHijos').style.display = 'none';
                    document.getElementById('num_hijos').value = '';
                    document.getElementById('edades_hijos').value = '';
                }
                
                // Apoyo económico
                if (ise.monto_apoyo && ise.monto_apoyo > 0) {
                    document.getElementById('apoyoSi').checked = true;
                    document.getElementById('panelMontoApoyo').style.display = 'block';
                    document.getElementById('monto_apoyo').value = ise.monto_apoyo;
                } else {
                    document.getElementById('apoyoNo').checked = true;
                    document.getElementById('panelMontoApoyo').style.display = 'none';
                    document.getElementById('monto_apoyo').value = '';
                }
                
                // Gasto en comida y transporte
                document.getElementById('gasto_comida_transporte').value = ise.gasto_comida_transporte || '';
                document.getElementById('comidas_diarias').value = ise.comidas_diarias || 3;
                
            } else {
                // Resetear valores
                document.getElementById('autoPropioAlumnoNo').checked = true;
                transportesTemporales = [];
                renderizarTransportesAsignados();
                document.getElementById('traslado_horas').value = '';
                document.getElementById('traslado_minutos').value = '';
                document.getElementById('estado_civil_alumno').value = '';
                document.getElementById('hijosNo').checked = true;
                document.getElementById('panelHijos').style.display = 'none';
                document.getElementById('apoyoNo').checked = true;
                document.getElementById('panelMontoApoyo').style.display = 'none';
                document.getElementById('gasto_comida_transporte').value = '';
                document.getElementById('comidas_diarias').value = '3';
            }

            // SECCIÓN 4 - DATOS ACADÉMICOS
            if (alumno.datos_academicos) {
                const da = alumno.datos_academicos;
                
                // Dispositivos (usando dispositivos_array del backend)
                if (da.dispositivos_array && da.dispositivos_array.length > 0) {
                    const dispositivos = da.dispositivos_array;
                    document.getElementById('dispositivoCelular').checked = dispositivos.includes(1);
                    document.getElementById('dispositivoComputadora').checked = dispositivos.includes(2);
                    document.getElementById('dispositivoInternet').checked = dispositivos.includes(3);
                    document.getElementById('dispositivoTablet').checked = dispositivos.includes(4);
                } else {
                    document.getElementById('dispositivoCelular').checked = false;
                    document.getElementById('dispositivoComputadora').checked = false;
                    document.getElementById('dispositivoInternet').checked = false;
                    document.getElementById('dispositivoTablet').checked = false;
                }
                
                document.getElementById('otro_dispositivo').value = da.otro_dispositivo || '';
                
            } else {
                // Resetear
                document.getElementById('dispositivoCelular').checked = false;
                document.getElementById('dispositivoComputadora').checked = false;
                document.getElementById('dispositivoInternet').checked = false;
                document.getElementById('dispositivoTablet').checked = false;
                document.getElementById('otro_dispositivo').value = '';
            }

            // PROBLEMAS DE APRENDIZAJE (dentro de sección 4)
            if (alumno.problema_aprendizaje) {
                const pa = alumno.problema_aprendizaje;
                
                // Verificar si tiene características específicas
                if (pa.caracteristicas_array && pa.caracteristicas_array.length > 0) {
                    document.getElementById('problemaSi').checked = true;
                    document.getElementById('panelProblemas').style.display = 'block';
                    
                    // IDs de los checkboxes en orden (1-12)
                    const problemasIds = [
                        'problemaDislexia', 'problemaVisuales', 'problemaAuditivo', 'problemaNoEntender',
                        'problemaLentitud', 'problemaLenguaje', 'problemaComunicacion', 'problemaDiscriminacion',
                        'problemaGestos', 'problemaDistraccion', 'problemaTDAH', 'problemaDiscapacidad'
                    ];
                    
                    // Limpiar todos primero
                    problemasIds.forEach(id => {
                        const cb = document.getElementById(id);
                        if (cb) cb.checked = false;
                    });
                    
                    // Marcar los que corresponden
                    pa.caracteristicas_array.forEach(c => {
                        if (c >= 1 && c <= 12 && problemasIds[c-1]) {
                            document.getElementById(problemasIds[c-1]).checked = true;
                        }
                    });
                    
                    document.getElementById('otro_problema').value = pa.otro_problema || '';
                    
                } else {
                    document.getElementById('problemaNo').checked = true;
                    document.getElementById('panelProblemas').style.display = 'none';
                    document.getElementById('otro_problema').value = '';
                    
                    // Limpiar checkboxes
                    const problemasIds = [
                        'problemaDislexia', 'problemaVisuales', 'problemaAuditivo', 'problemaNoEntender',
                        'problemaLentitud', 'problemaLenguaje', 'problemaComunicacion', 'problemaDiscriminacion',
                        'problemaGestos', 'problemaDistraccion', 'problemaTDAH', 'problemaDiscapacidad'
                    ];
                    problemasIds.forEach(id => {
                        const cb = document.getElementById(id);
                        if (cb) cb.checked = false;
                    });
                }
                
            } else {
                document.getElementById('problemaNo').checked = true;
                document.getElementById('panelProblemas').style.display = 'none';
                document.getElementById('otro_problema').value = '';
            }

            // SECCIÓN 5 - DATOS GENERALES DE SALUD
            if (alumno.datos_salud) {
                const ds = alumno.datos_salud;
                
                // 5.1 - Datos Básicos
                document.getElementById('estatura').value = ds.estatura || '';
                document.getElementById('peso').value = ds.peso || '';
                document.getElementById('tipo_sangre').value = ds.tipo_sangre || '';
                
                // Vacunas
                if (ds.cuadro_basico_vacunas) {
                    document.getElementById('vacunasSi').checked = true;
                } else {
                    document.getElementById('vacunasNo').checked = true;
                }
                
                // Anteojos (se determina por la presencia de graduacion_anteojos)
                if (ds.usa_anteojos) {
                    document.getElementById('anteojosSi').checked = true;
                    document.getElementById('graduacionContainer').style.display = 'block';
                    document.getElementById('graduacion_anteojos').value = ds.graduacion_anteojos || '';
                } else {
                    document.getElementById('anteojosNo').checked = true;
                    document.getElementById('graduacionContainer').style.display = 'none';
                    document.getElementById('graduacion_anteojos').value = '';
                }
                
                // 5.2 - Padecimientos Físicos
                // Cirugía
                if (ds.cirugia) {
                    document.getElementById('cirugiaSi').checked = true;
                    document.getElementById('cirugiaContainer').style.display = 'block';
                    document.getElementById('cirugia').value = ds.cirugia;
                } else {
                    document.getElementById('cirugiaNo').checked = true;
                    document.getElementById('cirugiaContainer').style.display = 'none';
                    document.getElementById('cirugia').value = '';
                }
                
                // Alergia
                if (ds.alergia) {
                    document.getElementById('alergiaSi').checked = true;
                    document.getElementById('alergiaContainer').style.display = 'block';
                    document.getElementById('alergia').value = ds.alergia;
                } else {
                    document.getElementById('alergiaNo').checked = true;
                    document.getElementById('alergiaContainer').style.display = 'none';
                    document.getElementById('alergia').value = '';
                }
                
                // Limitante físico
                if (ds.limitante_fisico) {
                    document.getElementById('limitanteSi').checked = true;
                    document.getElementById('limitanteContainer').style.display = 'block';
                    document.getElementById('limitante_fisico').value = ds.limitante_fisico;
                } else {
                    document.getElementById('limitanteNo').checked = true;
                    document.getElementById('limitanteContainer').style.display = 'none';
                    document.getElementById('limitante_fisico').value = '';
                }
                
                // Problema auditivo
                if (ds.problema_auditivo) {
                    document.getElementById('auditivoSi').checked = true;
                    document.getElementById('auditivoContainer').style.display = 'block';
                    document.getElementById('problema_auditivo').value = ds.problema_auditivo;
                } else {
                    document.getElementById('auditivoNo').checked = true;
                    document.getElementById('auditivoContainer').style.display = 'none';
                    document.getElementById('problema_auditivo').value = '';
                }
                
                // Adicción
                if (ds.adiccion) {
                    document.getElementById('adiccionSi').checked = true;
                    document.getElementById('adiccionContainer').style.display = 'block';
                    document.getElementById('adiccion').value = ds.adiccion;
                } else {
                    document.getElementById('adiccionNo').checked = true;
                    document.getElementById('adiccionContainer').style.display = 'none';
                    document.getElementById('adiccion').value = '';
                }
                
                // Padecimiento emocional
                if (ds.padecimiento_emocional) {
                    document.getElementById('emocionalSi').checked = true;
                    document.getElementById('emocionalContainer').style.display = 'block';
                    document.getElementById('padecimiento_emocional').value = ds.padecimiento_emocional;
                } else {
                    document.getElementById('emocionalNo').checked = true;
                    document.getElementById('emocionalContainer').style.display = 'none';
                    document.getElementById('padecimiento_emocional').value = '';
                }
                
                // Enfermedad actual
                document.getElementById('enfermedad_actual').value = ds.enfermedad_actual || '';
                
                // 5.3 - Salud Mental - Mostrar panel si hay síntomas o texto en otro_sintoma
                if ((ds.sintomas_array && ds.sintomas_array.length > 0) || (ds.otro_sintoma && ds.otro_sintoma.trim() !== '')) {
                    document.getElementById('sintomasSi').checked = true;
                    document.getElementById('panelSintomas').style.display = 'block';
                    if (ds.sintomas_array && ds.sintomas_array.length > 0) {
                        const sintomas = ds.sintomas_array;
                        document.getElementById('sintomaAnsiedad').checked = sintomas.includes(1);
                        document.getElementById('sintomaEstres').checked = sintomas.includes(2);
                        document.getElementById('sintomaDepresion').checked = sintomas.includes(3);
                        document.getElementById('sintomaCulpa').checked = sintomas.includes(4);
                        document.getElementById('sintomaTristeza').checked = sintomas.includes(5);
                    }
                    document.getElementById('otro_sintoma').value = ds.otro_sintoma || '';
                } else {
                    document.getElementById('sintomasNo').checked = true;
                    document.getElementById('panelSintomas').style.display = 'none';
                    document.getElementById('otro_sintoma').value = '';
                }
                
                // 5.4 - Datos Específicos
                // Medicamento controlado
                if (ds.medicamento_controlado) {
                    document.getElementById('medicamentoSi').checked = true;
                    document.getElementById('medicamentoContainer').style.display = 'block';
                    document.getElementById('medicamento_controlado').value = ds.medicamento_controlado;
                } else {
                    document.getElementById('medicamentoNo').checked = true;
                    document.getElementById('medicamentoContainer').style.display = 'none';
                    document.getElementById('medicamento_controlado').value = '';
                }
                
                // Alergia a medicamento
                if (ds.alergia_medicamento) {
                    document.getElementById('alergiaMedSi').checked = true;
                    document.getElementById('alergiaMedContainer').style.display = 'block';
                    document.getElementById('alergia_medicamento').value = ds.alergia_medicamento;
                } else {
                    document.getElementById('alergiaMedNo').checked = true;
                    document.getElementById('alergiaMedContainer').style.display = 'none';
                    document.getElementById('alergia_medicamento').value = '';
                }
                
                // Hospitalización
                if (ds.motivo_hospitalizacion) {
                    document.getElementById('hospitalizacionSi').checked = true;
                    document.getElementById('hospitalizacionContainer').style.display = 'block';
                    document.getElementById('motivo_hospitalizacion').value = ds.motivo_hospitalizacion;
                } else {
                    document.getElementById('hospitalizacionNo').checked = true;
                    document.getElementById('hospitalizacionContainer').style.display = 'none';
                    document.getElementById('motivo_hospitalizacion').value = '';
                }
                
                // Diabetes, hipertensión, dolores
                if (ds.diabetes) {
                    document.getElementById('diabetesSi').checked = true;
                } else {
                    document.getElementById('diabetesNo').checked = true;
                }
                
                if (ds.hipertension) {
                    document.getElementById('hipertensionSi').checked = true;
                } else {
                    document.getElementById('hipertensionNo').checked = true;
                }
                
                if (ds.dolores_cabeza) {
                    document.getElementById('cabezaSi').checked = true;
                } else {
                    document.getElementById('cabezaNo').checked = true;
                }
                
                if (ds.dolores_estomago) {
                    document.getElementById('estomagoSi').checked = true;
                } else {
                    document.getElementById('estomagoNo').checked = true;
                }
                
                // Frecuencias
                configurarFrecuenciaDesdeValor('medico', ds.frecuencia_medico);
                configurarFrecuenciaDesdeValor('dentista', ds.frecuencia_dentista);
                
            } else {
                resetearSeccionSalud();
            }

            // SECCIÓN 6 - ACTIVIDADES RECREATIVAS
            if (alumno.actividades_recreativas) {
                const ar = alumno.actividades_recreativas;
                
                // Pasatiempo favorito
                document.getElementById('pasatiempo_favorito').value = ar.pasatiempo_favorito || '';
                document.getElementById('horas_pasatiempo').value = ar.horas_pasatiempo_dedicadas || '';
                
                // Deportes (viene como array desde el cast JSON)
                if (ar.deporte_practicado && ar.deporte_practicado.length > 0) {
                    deportesTemporales = [...ar.deporte_practicado];
                    renderizarDeportesAsignados();
                    document.getElementById('horas_deporte').value = ar.horas_deporte_dedicadas || '';
                } else {
                    deportesTemporales = [];
                    renderizarDeportesAsignados();
                    document.getElementById('horas_deporte').value = '';
                }
                
                // Horas TV y computadora
                document.getElementById('horas_tv').value = ar.horas_dia_tv || '';
                document.getElementById('horas_compu').value = ar.horas_dia_compu || '';
                
                // Uso de computadora
                document.getElementById('uso_compu').value = ar.uso_frecuente_compu || '';
                
                // Chatear (se determina por la presencia de temas_quien_chat)
                if (ar.chatea) {
                    document.getElementById('chatearSi').checked = true;
                    document.getElementById('temasChatContainer').style.display = 'block';
                    document.getElementById('temas_chat').value = ar.temas_quien_chat || '';
                } else {
                    document.getElementById('chatearNo').checked = true;
                    document.getElementById('temasChatContainer').style.display = 'none';
                    document.getElementById('temas_chat').value = '';
                }
                
            } else {
                resetearSeccionActividades();
            }
            
            // Actualizar contadores
            // Al final de la función, después de actualizar contadores
            actualizarTodosLosContadores();
            actualizarContadoresDespuesDeCargar();
            sincronizarEstadoEditSwitch();

            // También actualizar contadores de familiares si hay
            if (familiaresTemporales.length > 0) {
                setTimeout(() => actualizarTodosLosContadores(), 100);
            }

            // Forzar actualización de toggles de salud
            const sintomasSiRadio = document.getElementById('sintomasSi');
            if (sintomasSiRadio && sintomasSiRadio.checked) {
                document.getElementById('panelSintomas').style.display = 'block';
            }

            // Forzar actualización de toggle de chat
            const chatearSiRadio = document.getElementById('chatearSi');
            if (chatearSiRadio && chatearSiRadio.checked) {
                document.getElementById('temasChatContainer').style.display = 'block';
            }

            // Habilitar el botón Guardar si el usuario tiene permisos de edición
            @if($puedeEditar)
            const guardarBtn = document.getElementById('guardarTodosBtn');
            if (guardarBtn) {
                guardarBtn.disabled = false;
            }
            @endif
            
            // Mostrar mensaje de éxito
            console.log('Datos del alumno cargados correctamente');
        } else {
            alert(data.message || 'Error al cargar los datos del alumno');
        }
    })
    .catch(error => {
        console.error('Error detallado:', error);
        alert('Error de conexión al cargar los datos: ' + error.message);
    })
    .finally(() => {
        if (btnBuscar) {
            btnBuscar.disabled = false;
            btnBuscar.innerHTML = originalText || '<i class="fas fa-search"></i>';
        }
    });
}

// Función para sincronizar el estado del switch después de cargar datos
function sincronizarEstadoEditSwitch() {
    const toggleEditMode = document.getElementById('toggle_edit_mode');
    if (toggleEditMode) {
        // Forzar la aplicación del estado actual del switch
        // Esto asegura que todos los campos queden como deben
        const currentState = toggleEditMode.checked;
        // Disparar el evento change para que toggleEditMode procese todo
        toggleEditMode.dispatchEvent(new Event('change'));
        // Verificar que el estado se mantuvo
        if (toggleEditMode.checked !== currentState) {
            toggleEditMode.checked = currentState;
            toggleEditMode.dispatchEvent(new Event('change'));
        }
    }
}

// ===========================================
// BOTÓN CANCELAR TODO (CORREGIDO)
// ===========================================

// Función para cancelar todo - debe asignarse a ambos botones
function cancelarTodosLosCambios() {
    const alumnoId = document.getElementById('alumnoId').value;
    const alumnoIdHidden = document.getElementById('alumno_id_selected');
    
    if (!alumnoId || !alumnoId.trim()) {
        if (confirm('No hay alumno seleccionado. ¿Deseas limpiar el formulario?')) {
            becasTemporales = [];
            familiaresTemporales = [];
            transportesTemporales = [];
            deportesTemporales = [];
            renderizarBecasAsignadas();
            renderizarFamiliaresAsignados();
            renderizarTransportesAsignados();
            renderizarDeportesAsignados();
            
            const formulario = document.getElementById('registroForm');
            if (formulario) {
                const inputs = formulario.querySelectorAll('input:not([type="hidden"]), select, textarea');
                inputs.forEach(input => {
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        if (input.id === 'buscar_grupo') {
                            input.value = '';
                        } else {
                            input.checked = false;
                        }
                    } else if (input.id === 'id_grupo') {
                        input.value = '';
                    } else {
                        input.value = '';
                    }
                });
            }
            
            document.querySelectorAll('.accordion-section').forEach(section => {
                section.classList.remove('expanded');
            });
            
            actualizarTodosLosContadores();
        }
        return;
    }
    
    if (!confirm('¿Estás seguro de que deseas cancelar TODOS los cambios no guardados? Los datos volverán a su estado original.')) {
        return;
    }
    
    if (alumnoIdHidden) {
        alumnoIdHidden.value = alumnoId;
    }
    
    cargarDatosAlumnoCompleto(alumnoId);
    
    setTimeout(() => {
        becasTemporales = [];
        familiaresTemporales = [];
        transportesTemporales = [];
        deportesTemporales = [];
        
        cancelarEdicionFamiliar();
        cancelarEdicionBeca();
        
        const toggleEditMode = document.getElementById('toggle_edit_mode');
        if (toggleEditMode && toggleEditMode.checked) {
            toggleEditMode.checked = false;
            toggleEditMode.dispatchEvent(new Event('change'));
        }
        
        const passwordInput = document.getElementById('current_password_global');
        if (passwordInput) passwordInput.value = '';
        
        alert('Todos los cambios han sido cancelados. El formulario ha sido restaurado.');
    }, 300);
}

// Función para actualizar fecha y edad desde CURP (versión corregida)
function actualizarFechaEdadDesdeCURPManual(curp) {
    if (!curp || curp.length < 10) return;
    
    // Extraer fecha de la CURP (posiciones 5-10)
    // Índices en JavaScript: 4-5 año, 6-7 mes, 8-9 día
    const anioStr = curp.substring(4, 6);   // "03"
    const mesStr = curp.substring(6, 8);    // "03"
    const diaStr = curp.substring(8, 10);   // "10"
    
    const anioNum = parseInt(anioStr);
    // Determinar siglo: años 0-24 son 2000+, años 25-99 son 1900+
    const anioCompleto = anioNum <= 24 ? 2000 + anioNum : 1900 + anioNum;
    const mesNum = parseInt(mesStr);
    const diaNum = parseInt(diaStr);
    
    // Validar fecha
    if (diaNum < 1 || diaNum > 31 || mesNum < 1 || mesNum > 12 || anioNum < 0 || anioNum > 99) {
        console.warn('Fecha inválida en CURP');
        return;
    }
    
    const fechaTexto = `${diaNum.toString().padStart(2, '0')}/${mesNum.toString().padStart(2, '0')}/${anioCompleto}`;
    const fechaDiv = document.getElementById('fecha_nacimiento');
    if (fechaDiv) fechaDiv.textContent = fechaTexto;
    
    const fechaNac = new Date(anioCompleto, mesNum - 1, diaNum);
    const hoy = new Date();
    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const m = hoy.getMonth() - fechaNac.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }
    const edadDiv = document.getElementById('edad_actual');
    if (edadDiv) edadDiv.textContent = edad >= 0 ? edad + ' años' : '— año(s)';
}

function cargarFamiliarEnFormulario(familiar, index) {
    if (!familiar) return;
    
    const isEditingMode = document.getElementById('toggle_edit_mode')?.checked || false;
    const puedeEditar = @json($puedeEditar);
    
    // Si no puede editar o no está en modo edición, solo carga en modo consulta
    if (!puedeEditar || !isEditingMode) {
        // Modo consulta - solo mostrar datos, no permitir edición
        modoEdicionFamiliarActivo = false;
        familiarEnEdicion = null;
        familiarEnEdicionIndex = null;
    } else {
        // Modo edición - activar edición
        modoEdicionFamiliarActivo = true;
        familiarEnEdicion = familiar;
        familiarEnEdicionIndex = index;
    }
    
    const f = familiar.familiar;
    const r = familiar.relacion;
    
    // Cargar datos en el formulario
    document.getElementById('parentesco').value = r.parentesco || '';
    document.getElementById('familiar_nombre').value = f.nombre || '';
    document.getElementById('familiar_apellido1').value = f.apellido1 || '';
    document.getElementById('familiar_apellido2').value = f.apellido2 || '';
    document.getElementById('familiar_telefono').value = f.telefono_celular || '';
    document.getElementById('familiar_escolaridad').value = f.escolaridad || '';
    document.getElementById('familiar_ocupacion').value = f.ocupacion || '';
    document.getElementById('familiar_lugar_trabajo').value = f.lugar_trabajo || '';
    document.getElementById('familiar_horario_laboral').value = f.horario_laboral || '';
    document.getElementById('familiar_domicilio_trabajo').value = f.domicilio_trabajo || '';
    document.getElementById('familiar_telefono_trabajo').value = f.telefono_trabajo || '';
    
    // Fecha de nacimiento
    if (f.fecha_nacimiento) {
        const fecha = new Date(f.fecha_nacimiento);
        document.getElementById('familiar_dia').value = String(fecha.getDate()).padStart(2, '0');
        document.getElementById('familiar_mes').value = String(fecha.getMonth() + 1).padStart(2, '0');
        document.getElementById('familiar_anio').value = fecha.getFullYear();
    } else {
        document.getElementById('familiar_dia').value = '';
        document.getElementById('familiar_mes').value = '';
        document.getElementById('familiar_anio').value = '';
    }
    
    // Estado de vida
    if (f.vive) {
        document.getElementById('familiarViveSi').checked = true;
        document.getElementById('familiarViveNo').checked = false;
    } else {
        document.getElementById('familiarViveSi').checked = false;
        document.getElementById('familiarViveNo').checked = true;
    }
    document.getElementById('familiarViveSi').dispatchEvent(new Event('change'));
    
    // Autorizaciones - Configurar contacto primero para habilitar tutor si es necesario
    const puedeEditarActual = puedeEditar && isEditingMode;

    if (r.contacto_emergencia > 0) {
        document.getElementById('contactoSi').checked = true;
        document.getElementById('contactoNo').checked = false;
        document.getElementById('prioridadContainer').style.display = 'block';
        document.getElementById('familiar_prioridad').value = r.contacto_emergencia;
        
        // Habilitar/deshabilitar según modo edición
        if (puedeEditarActual) {
            document.getElementById('familiar_prioridad').removeAttribute('readonly');
            document.getElementById('familiar_prioridad').disabled = false;
            document.getElementById('tutorSi').disabled = false;
            document.getElementById('tutorNo').disabled = false;
        } else {
            document.getElementById('familiar_prioridad').setAttribute('readonly', true);
            document.getElementById('familiar_prioridad').disabled = true;
            document.getElementById('tutorSi').disabled = true;
            document.getElementById('tutorNo').disabled = true;
        }
    } else {
        document.getElementById('contactoSi').checked = false;
        document.getElementById('contactoNo').checked = true;
        document.getElementById('prioridadContainer').style.display = 'none';
        document.getElementById('familiar_prioridad').value = '';
        
        // Si no es contacto, no puede ser tutor
        if (puedeEditarActual) {
            document.getElementById('tutorSi').disabled = true;
            document.getElementById('tutorSi').checked = false;
            document.getElementById('tutorNo').checked = true;
            document.getElementById('tutorNo').disabled = false;
        } else {
            document.getElementById('tutorSi').disabled = true;
            document.getElementById('tutorNo').disabled = true;
        }
    }
    
    if (r.tutor && r.contacto_emergencia > 0) {
        document.getElementById('tutorSi').checked = true;
        document.getElementById('tutorNo').checked = false;
    } else if (r.contacto_emergencia > 0) {
        document.getElementById('tutorSi').checked = false;
        document.getElementById('tutorNo').checked = true;
    }
    
    // Domicilio
    const comparteDomicilio = r.comparte_domicilio;
    const vive = f.vive;
    
    if (!vive) {
        const comparteDomicilioSection = document.querySelector('input[name="comparte_domicilio"]')?.closest('.row');
        if (comparteDomicilioSection) comparteDomicilioSection.style.display = 'none';
        document.getElementById('panelDomicilioDiferente').style.display = 'none';
        document.getElementById('comparteSi').checked = false;
        document.getElementById('comparteNo').checked = false;
        limpiarDomicilioFamiliar();
    } else {
        const comparteDomicilioSection = document.querySelector('input[name="comparte_domicilio"]')?.closest('.row');
        if (comparteDomicilioSection) comparteDomicilioSection.style.display = 'flex';
        
        if (comparteDomicilio) {
            document.getElementById('comparteSi').checked = true;
            document.getElementById('comparteNo').checked = false;
            document.getElementById('panelDomicilioDiferente').style.display = 'none';
            limpiarDomicilioFamiliar();
        } else {
            document.getElementById('comparteSi').checked = false;
            document.getElementById('comparteNo').checked = true;
            document.getElementById('panelDomicilioDiferente').style.display = 'block';
            
            if (f.domicilio) {
                document.getElementById('familiar_calle').value = f.domicilio.calle || '';
                document.getElementById('familiar_num_ext').value = f.domicilio.num_ext || '';
                document.getElementById('familiar_num_int').value = f.domicilio.num_int || '';
                document.getElementById('familiar_colonia').value = f.domicilio.colonia || '';
                document.getElementById('familiar_localidad').value = f.domicilio.localidad || '';
                document.getElementById('familiar_municipio').value = f.domicilio.municipio || '';
                document.getElementById('familiar_cp').value = f.domicilio.cp || '';
                document.getElementById('familiar_estado').value = f.domicilio.estado || '';
                document.getElementById('familiar_telefono_domicilio').value = f.domicilio.telefono_domicilio || '';
                document.getElementById('familiar_domicilio_id').value = f.id_domicilio || '';
            }
        }
    }
    
    // Actualizar contadores
    actualizarContadoresFamiliares();
    
    // Actualizar botones según el modo
    actualizarBotonesFamiliares();
    
    // Scroll al formulario
    document.querySelector('.card-body').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function actualizarContadoresDespuesDeCargar() {
    // Actualizar contadores de sección 1
    actualizarTodosLosContadores();
    
    // Actualizar contadores de familiares si hay
    // Después de renderizar familiares, actualizar contadores
    if (familiaresTemporales.length > 0) {
        setTimeout(() => actualizarTodosLosContadores(), 100);
    }
    
    // Actualizar contadores de trabajo
    const trabajoCampos = ['lugar_trabajo', 'horario_laboral', 'domicilio_trabajo', 'telefono_trabajo'];
    trabajoCampos.forEach(id => {
        const input = document.getElementById(id);
        const counterId = id + 'Counter';
        const counter = document.getElementById(counterId);
        if (input && counter) counter.textContent = input.value.length;
    });
    
    // Actualizar contador de estado civil padres
    const estadoCivilPadres = document.getElementById('estado_civil_padres');
    const estadoCivilCounter = document.getElementById('estadoCivilCounter');
    if (estadoCivilPadres && estadoCivilCounter) {
        estadoCivilCounter.textContent = estadoCivilPadres.value.length;
    }
    
    // Actualizar contador de estado civil alumno
    const estadoCivilAlumno = document.getElementById('estado_civil_alumno');
    const estadoCivilAlumnoCounter = document.getElementById('estadoCivilAlumnoCounter');
    if (estadoCivilAlumno && estadoCivilAlumnoCounter) {
        estadoCivilAlumnoCounter.textContent = estadoCivilAlumno.value.length;
    }
    
    // Actualizar contadores de otros dispositivos y otros problemas
    const otroDispositivo = document.getElementById('otro_dispositivo');
    const otroDispositivoCounter = document.getElementById('otroDispositivoCounter');
    if (otroDispositivo && otroDispositivoCounter) {
        otroDispositivoCounter.textContent = otroDispositivo.value.length;
    }
    
    const otroProblema = document.getElementById('otro_problema');
    const otroProblemaCounter = document.getElementById('otroProblemaCounter');
    if (otroProblema && otroProblemaCounter) {
        otroProblemaCounter.textContent = otroProblema.value.length;
    }
    
    // Actualizar contadores de salud específicos
    const saludTextareas = ['cirugia', 'alergia', 'limitante_fisico', 'problema_auditivo', 'adiccion', 'padecimiento_emocional', 'enfermedad_actual', 'otro_sintoma', 'medicamento_controlado', 'alergia_medicamento', 'motivo_hospitalizacion'];
    saludTextareas.forEach(id => {
        const input = document.getElementById(id);
        const counterId = id + 'Counter';
        const counter = document.getElementById(counterId);
        if (input && counter) counter.textContent = input.value.length;
    });
}

function actualizarContadoresFamiliares() {
    const contadores = [
        'parentescoCounter', 'familiarNombreCounter', 'familiarApellido1Counter', 
        'familiarApellido2Counter', 'familiarTelefonoCounter', 'familiar_ocupacionCounter',
        'familiar_lugar_trabajoCounter', 'familiar_horario_laboralCounter',
        'familiar_domicilio_trabajoCounter', 'familiar_telefono_trabajoCounter',
        // Agregar contadores de domicilio del familiar
        'familiar_calleCounter', 'familiar_num_extCounter', 'familiar_num_intCounter',
        'familiar_coloniaCounter', 'familiar_localidadCounter', 'familiar_municipioCounter',
        'familiar_cpCounter', 'familiar_estadoCounter', 'familiar_telefono_domicilioCounter'
    ];
    
    contadores.forEach(counterId => {
        const span = document.getElementById(counterId);
        if (span) {
            const inputId = counterId.replace('Counter', '');
            const input = document.getElementById(inputId);
            if (input) span.textContent = input.value.length;
        }
    });
}

// Función para configurar frecuencias médico/dentista desde valor numérico
function configurarFrecuenciaDesdeValor(prefix, valor) {
    if (valor === undefined || valor === null) return;
    
    const opcionNunca = document.getElementById(`${prefix}Nunca`);
    const opcionSoloEnfermo = document.getElementById(`${prefix}SoloEnfermo`);
    const aniosSelect = document.getElementById(`${prefix}_anios`);
    const mesesSelect = document.getElementById(`${prefix}_meses`);
    
    // Limpiar primero
    if (opcionNunca) opcionNunca.checked = false;
    if (opcionSoloEnfermo) opcionSoloEnfermo.checked = false;
    if (aniosSelect) aniosSelect.value = '';
    if (mesesSelect) mesesSelect.value = '';
    
    if (valor === 0) {
        if (opcionNunca) opcionNunca.checked = true;
    } else if (valor === -1) {
        if (opcionSoloEnfermo) opcionSoloEnfermo.checked = true;
    } else if (valor > 0) {
        // Convertir meses a años y meses
        const años = Math.floor(valor / 12);
        const meses = valor % 12;
        
        if (años > 0 && aniosSelect) {
            // Buscar la opción que coincida con años*12
            let encontrado = false;
            for (let i = 0; i < aniosSelect.options.length; i++) {
                if (parseInt(aniosSelect.options[i].value) === años * 12) {
                    aniosSelect.selectedIndex = i;
                    encontrado = true;
                    break;
                }
            }
            if (!encontrado && aniosSelect.options.length > 0) {
                aniosSelect.selectedIndex = 0;
            }
        }
        if (meses > 0 && mesesSelect) {
            for (let i = 0; i < mesesSelect.options.length; i++) {
                if (parseInt(mesesSelect.options[i].value) === meses) {
                    mesesSelect.selectedIndex = i;
                    break;
                }
            }
        }
    }
}

// Función para resetear la sección de salud
function resetearSeccionSalud() {
    // 5.1 - Datos Básicos
    document.getElementById('estatura').value = '';
    document.getElementById('peso').value = '';
    document.getElementById('tipo_sangre').value = '';
    document.getElementById('vacunasNo').checked = true;
    document.getElementById('anteojosNo').checked = true;
    document.getElementById('graduacionContainer').style.display = 'none';
    document.getElementById('graduacion_anteojos').value = '';
    
    // 5.2 - Padecimientos Físicos
    const toggles = ['cirugia', 'alergia', 'limitante', 'auditivo', 'adiccion', 'emocional'];
    toggles.forEach(t => {
        const radioNo = document.getElementById(`${t}No`);
        const radioSi = document.getElementById(`${t}Si`);
        const container = document.getElementById(`${t}Container`);
        let textareaId = t;
        if (t === 'limitante') textareaId = 'limitante_fisico';
        if (t === 'auditivo') textareaId = 'problema_auditivo';
        if (t === 'emocional') textareaId = 'padecimiento_emocional';
        const textarea = document.getElementById(textareaId);
        
        if (radioNo) radioNo.checked = true;
        if (radioSi) radioSi.checked = false;
        if (container) container.style.display = 'none';
        if (textarea) {
            textarea.value = '';
            const counterId = t + 'Counter';
            const counter = document.getElementById(counterId);
            if (counter) counter.textContent = '0';
        }
    });
    
    // Enfermedad actual
    document.getElementById('enfermedad_actual').value = '';
    const enfermedadCounter = document.getElementById('enfermedadCounter');
    if (enfermedadCounter) enfermedadCounter.textContent = '0';
    
    // 5.3 - Salud Mental
    const sintomasNo = document.getElementById('sintomasNo');
    const sintomasSi = document.getElementById('sintomasSi');
    const panelSintomas = document.getElementById('panelSintomas');
    const otroSintoma = document.getElementById('otro_sintoma');
    const otroSintomaCounter = document.getElementById('otroSintomaCounter');
    
    if (sintomasNo) sintomasNo.checked = true;
    if (sintomasSi) sintomasSi.checked = false;
    if (panelSintomas) panelSintomas.style.display = 'none';
    if (otroSintoma) otroSintoma.value = '';
    if (otroSintomaCounter) otroSintomaCounter.textContent = '0';
    
    const sintomasCheckboxes = ['sintomaAnsiedad', 'sintomaEstres', 'sintomaDepresion', 'sintomaCulpa', 'sintomaTristeza'];
    sintomasCheckboxes.forEach(id => {
        const cb = document.getElementById(id);
        if (cb) cb.checked = false;
    });
    
    // 5.4 - Datos Específicos
    const especificos = ['medicamento', 'alergiaMed', 'hospitalizacion'];
    especificos.forEach(t => {
        const radioNo = document.getElementById(`${t}No`);
        const radioSi = document.getElementById(`${t}Si`);
        const container = document.getElementById(`${t}Container`);
        let textareaId = t === 'medicamento' ? 'medicamento_controlado' : (t === 'alergiaMed' ? 'alergia_medicamento' : 'motivo_hospitalizacion');
        const textarea = document.getElementById(textareaId);
        
        if (radioNo) radioNo.checked = true;
        if (radioSi) radioSi.checked = false;
        if (container) container.style.display = 'none';
        if (textarea) {
            textarea.value = '';
            const counterId = t + 'Counter';
            const counter = document.getElementById(counterId);
            if (counter) counter.textContent = '0';
        }
    });
    
    // Diabetes, hipertensión, dolores
    document.getElementById('diabetesNo').checked = true;
    document.getElementById('hipertensionNo').checked = true;
    document.getElementById('cabezaNo').checked = true;
    document.getElementById('estomagoNo').checked = true;
    
    // Resetear frecuencias
    configurarFrecuenciaDesdeValor('medico', -1);
    configurarFrecuenciaDesdeValor('dentista', -1);
}

function limpiarCheckboxesProblemas() {
    const caracteristicasIds = [
        'problemaDislexia', 'problemaVisuales', 'problemaAuditivo', 'problemaNoEntender',
        'problemaLentitud', 'problemaLenguaje', 'problemaComunicacion', 'problemaDiscriminacion',
        'problemaGestos', 'problemaDistraccion', 'problemaTDAH', 'problemaDiscapacidad'
    ];
    caracteristicasIds.forEach(id => {
        const cb = document.getElementById(id);
        if (cb) cb.checked = false;
    });
}

// Función para resetear la sección de actividades recreativas
function resetearSeccionActividades() {
    document.getElementById('pasatiempo_favorito').value = '';
    document.getElementById('horas_pasatiempo').value = '';
    deportesTemporales = [];
    renderizarDeportesAsignados();
    document.getElementById('horas_deporte').value = '';
    document.getElementById('horas_tv').value = '';
    document.getElementById('horas_compu').value = '';
    document.getElementById('uso_compu').value = '';
    document.getElementById('chatearNo').checked = true;
    document.getElementById('temasChatContainer').style.display = 'none';
    document.getElementById('temas_chat').value = '';
}

// Función para actualizar TODOS los contadores del formulario
function actualizarTodosLosContadores() {
    // Mapeo de IDs de inputs a sus contadores correspondientes
    const mapeoContadores = {
        // Sección 1.1 - Nombre completo
        'alumno_nombre': 'alumnoNombreCounter',
        'alumno_apellido1': 'alumnoApellido1Counter',
        'alumno_apellido2': 'alumnoApellido2Counter',
        
        // Sección 1.2 - Lugar de nacimiento
        'localidad': 'localidadCounter',
        'municipio': 'municipioCounter',
        'estado': 'estadoCounter',
        'pais': 'paisCounter',
        
        // Sección 1.3 - Domicilio
        'calle': 'calleCounter',
        'num_ext': 'num_extCounter',
        'num_int': 'num_intCounter',
        'colonia': 'coloniaCounter',
        'domicilio_localidad': 'domicilioLocalidadCounter',
        'domicilio_municipio': 'domicilioMunicipioCounter',
        'cp': 'cpCounter',
        'domicilio_estado': 'domicilioEstadoCounter',
        'telefono_domicilio': 'telefonoDomicilioCounter',
        
        // Sección 1.4 - Secundaria
        'secundaria_nombre': 'secundariaNombreCounter',
        'secundaria_localidad': 'secundariaLocalidadCounter',
        'secundaria_municipio': 'secundariaMunicipioCounter',
        'secundaria_estado': 'secundariaEstadoCounter',
        'secundaria_pais': 'secundariaPaisCounter',
        
        // Sección 1.5 - Datos particulares
        'telefono_celular': 'telefonoCelularCounter',
        'email_personal': 'emailPersonalCounter',
        'email_institucional': 'emailInstitucionalCounter',
        'curp': 'curpCounter',
        'nss': 'nssCounter',
        
        // Sección 1.8 - Trabajo
        'lugar_trabajo': 'lugarTrabajoCounter',
        'horario_laboral': 'horarioLaboralCounter',
        'domicilio_trabajo': 'domicilioTrabajoCounter',
        'telefono_trabajo': 'telefonoTrabajoCounter',
        
        // Sección 2.1 - Familiares (inputs del formulario)
        'parentesco': 'parentescoCounter',
        'familiar_nombre': 'familiarNombreCounter',
        'familiar_apellido1': 'familiarApellido1Counter',
        'familiar_apellido2': 'familiarApellido2Counter',
        'familiar_telefono': 'familiarTelefonoCounter',
        'familiar_ocupacion': 'familiar_ocupacionCounter',
        'familiar_lugar_trabajo': 'familiar_lugar_trabajoCounter',
        'familiar_horario_laboral': 'familiar_horario_laboralCounter',
        'familiar_domicilio_trabajo': 'familiar_domicilio_trabajoCounter',
        'familiar_telefono_trabajo': 'familiar_telefono_trabajoCounter',
        
        // Domicilio del familiar
        'familiar_calle': 'familiar_calleCounter',
        'familiar_num_ext': 'familiar_num_extCounter',
        'familiar_num_int': 'familiar_num_intCounter',
        'familiar_colonia': 'familiar_coloniaCounter',
        'familiar_localidad': 'familiar_localidadCounter',
        'familiar_municipio': 'familiar_municipioCounter',
        'familiar_cp': 'familiar_cpCounter',
        'familiar_estado': 'familiar_estadoCounter',
        'familiar_telefono_domicilio': 'familiar_telefono_domicilioCounter',
        
        // Sección 2.2
        'estado_civil_padres': 'estadoCivilCounter',
        'estado_civil_alumno': 'estadoCivilAlumnoCounter',
        
        // Sección 4
        'otro_dispositivo': 'otroDispositivoCounter',
        'otro_problema': 'otroProblemaCounter',
        
        // Sección 5 - Padecimientos
        'cirugia': 'cirugiaCounter',
        'alergia': 'alergiaCounter',
        'limitante_fisico': 'limitanteCounter',
        'problema_auditivo': 'auditivoCounter',
        'adiccion': 'adiccionCounter',
        'padecimiento_emocional': 'emocionalCounter',
        'enfermedad_actual': 'enfermedadCounter',
        'otro_sintoma': 'otroSintomaCounter',
        'medicamento_controlado': 'medicamentoCounter',
        'alergia_medicamento': 'alergiaMedCounter',
        'motivo_hospitalizacion': 'hospitalizacionCounter',
        
        // Sección 6
        'pasatiempo_favorito': 'pasatiempoCounter',
        'uso_compu': 'usoCompuCounter',
        'temas_chat': 'temasChatCounter'
    };
    
    // Actualizar cada contador
    for (const [inputId, counterId] of Object.entries(mapeoContadores)) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);
        if (input && counter) {
            counter.textContent = input.value ? input.value.length : 0;
        }
    }
    
    // Actualizar contador de número de control
    const numControl = document.getElementById('num_control');
    const numControlCounter = document.getElementById('numControlCounter');
    if (numControl && numControlCounter) {
        numControlCounter.textContent = numControl.value ? numControl.value.length : 0;
    }
}

// ===========================================
// INICIALIZACIÓN
// ===========================================

document.addEventListener('DOMContentLoaded', function() {
    initAccordions();
    setupBusquedaAlumnos();
    setupFechaDesdeCURP();

    // Detectar si viene de Consulta Avanzada
    function detectarConsultaAvanzada() {
        const urlParams = new URLSearchParams(window.location.search);
        const alumnoId = urlParams.get('consulta_avanzada');
        
        if (alumnoId && alumnoId.trim() !== '') {
            const alumnoIdHidden = document.getElementById('alumno_id_selected');
            const busquedaInput = document.getElementById('busqueda_alumno');
            
            if (alumnoIdHidden) {
                alumnoIdHidden.value = alumnoId;
            }
            
            // Mostrar indicador de carga
            if (busquedaInput) {
                busquedaInput.disabled = true;
                busquedaInput.placeholder = 'Cargando datos del alumno...';
            }
            
            fetch(`{{ url("alumnos/obtener-alumno-completo") }}/${alumnoId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.alumno) {
                    if (busquedaInput) {
                        busquedaInput.value = `${data.alumno.num_control || ''} - ${data.alumno.nombre} ${data.alumno.apellido1}`;
                        busquedaInput.disabled = false;
                        busquedaInput.placeholder = 'Núm. control, nombre, apellidos, CURP...';
                    }
                    cargarDatosAlumnoCompleto(alumnoId);
                    
                    // ACTUALIZAR EL BOTÓN LIMPIAR después de un pequeño retraso
                    setTimeout(() => {
                        actualizarBotonLimpiar();
                    }, 500);

                    // Dentro de detectarConsultaAvanzada, después de cargar los datos
                    setTimeout(() => {
                        const toggleEditMode = document.getElementById('toggle_edit_mode');
                        if (toggleEditMode && toggleEditMode.checked) {
                            toggleEditMode.checked = false;
                            toggleEditMode.dispatchEvent(new Event('change'));
                        }
                    }, 500);
                } else {
                    if (busquedaInput) {
                        busquedaInput.disabled = false;
                        busquedaInput.placeholder = 'Núm. control, nombre, apellidos, CURP...';
                    }
                    console.error('No se pudo cargar el alumno para Consulta Avanzada');
                }
            })
            .catch(error => {
                console.error('Error en Consulta Avanzada:', error);
                if (busquedaInput) {
                    busquedaInput.disabled = false;
                    busquedaInput.placeholder = 'Núm. control, nombre, apellidos, CURP...';
                }
            });
            
            // Limpiar la URL para que no se recargue con el mismo parámetro
            const newUrl = window.location.pathname;
            window.history.replaceState({}, '', newUrl);
        }
    }
    
    // Botones flotantes
    const btnGoToBottom = document.getElementById('btnGoToBottom');
    if (btnGoToBottom) {
        function toggleFloatingButtons() {
            const scrollPosition = window.scrollY;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            
            if (scrollPosition + windowHeight < documentHeight - 300) {
                btnGoToBottom.classList.add('visible');
            } else {
                btnGoToBottom.classList.remove('visible');
            }
        }
        
        btnGoToBottom.addEventListener('click', function() {
            window.scrollTo({ top: document.documentElement.scrollHeight, behavior: 'smooth' });
        });
        
        window.addEventListener('scroll', toggleFloatingButtons);
        setTimeout(toggleFloatingButtons, 100);
    }
    
    actualizarBotonLimpiar();
    
    fetch('{{ route("alumnos.get-sections-mode") }}')
        .then(res => res.json())
        .then(data => {
            if (data.modo_multiple) {
                modoMultiplesSecciones = true;
                const toggleCheckbox = document.getElementById('toggle_all_sections');
                if (toggleCheckbox) toggleCheckbox.checked = true;
            }
        });
    
    setupToggleContainer('tiene_cirugia', 'cirugiaContainer', 'cirugia', 'cirugiaCounter');
    setupToggleContainer('tiene_alergia', 'alergiaContainer', 'alergia', 'alergiaCounter');
    setupToggleContainer('tiene_limitante', 'limitanteContainer', 'limitante_fisico', 'limitanteCounter');
    setupToggleContainer('tiene_auditivo', 'auditivoContainer', 'problema_auditivo', 'auditivoCounter');
    setupToggleContainer('tiene_adiccion', 'adiccionContainer', 'adiccion', 'adiccionCounter');
    setupToggleContainer('tiene_emocional', 'emocionalContainer', 'padecimiento_emocional', 'emocionalCounter');
    
    setupAllConditionalToggles();
    setupCheckboxGroups();
    setupFrecuencias();
    setupFamiliarViveToggle();
    setupFamiliarDomicilioToggle();
    setupFamiliarContactoToggle();
    setupChatToggle();
    setupBecaPanelToggle();
    
    // Asignar el evento Cancelar Todo a AMBOS botones
    const cancelarBtns = document.querySelectorAll('#cancelarTodoBtn');
    cancelarBtns.forEach(btn => {
        if (btn) {
            // Remover event listeners anteriores clonando y reemplazando
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            newBtn.addEventListener('click', cancelarTodosLosCambios);
        }
    });
    
    // Asegurar que el botón del final también tenga el evento
    const finalCancelBtn = document.getElementById('cancelarTodoBtn');
    if (finalCancelBtn) {
        finalCancelBtn.addEventListener('click', cancelarTodosLosCambios);
    }
    
    document.addEventListener('click', function(e) {
        const localidadInput = document.getElementById('localidad');
        const localidadResults = document.getElementById('localidadResults');
        if (localidadInput && localidadResults && !localidadInput.contains(e.target) && !localidadResults.contains(e.target)) {
            localidadResults.style.display = 'none';
        }
        
        const coloniaInput = document.getElementById('colonia');
        const coloniaResults = document.getElementById('coloniaResults');
        if (coloniaInput && coloniaResults && !coloniaInput.contains(e.target) && !coloniaResults.contains(e.target)) {
            coloniaResults.style.display = 'none';
        }
        
        const secundariaInput = document.getElementById('secundaria_nombre');
        const secundariaResults = document.getElementById('secundariaResults');
        if (secundariaInput && secundariaResults && !secundariaInput.contains(e.target) && !secundariaResults.contains(e.target)) {
            secundariaResults.style.display = 'none';
        }
    });
    
    const becaInput = document.getElementById('buscarBeca');
    const becaResults = document.getElementById('becaResults');
    if (becaInput && becaResults) {
        document.addEventListener('click', function(e) {
            if (!becaInput.contains(e.target) && !becaResults.contains(e.target)) {
                becaResults.style.display = 'none';
            }
        });
    }
    
    const parentescoInput = document.getElementById('parentesco');
    const parentescoResults = document.getElementById('parentescoResults');
    if (parentescoInput && parentescoResults) {
        document.addEventListener('click', function(e) {
            if (!parentescoInput.contains(e.target) && !parentescoResults.contains(e.target)) {
                parentescoResults.style.display = 'none';
            }
        });
    }
    
    const familiarApellido1Input = document.getElementById('familiar_apellido1');
    const familiarApellido1Results = document.getElementById('familiarApellido1Results');
    if (familiarApellido1Input && familiarApellido1Results) {
        document.addEventListener('click', function(e) {
            if (!familiarApellido1Input.contains(e.target) && !familiarApellido1Results.contains(e.target)) {
                familiarApellido1Results.style.display = 'none';
            }
        });
    }
    
    const familiarApellido2Input = document.getElementById('familiar_apellido2');
    const familiarApellido2Results = document.getElementById('familiarApellido2Results');
    if (familiarApellido2Input && familiarApellido2Results) {
        document.addEventListener('click', function(e) {
            if (!familiarApellido2Input.contains(e.target) && !familiarApellido2Results.contains(e.target)) {
                familiarApellido2Results.style.display = 'none';
            }
        });
    }
    
    const familiarColoniaInput = document.getElementById('familiar_colonia');
    const familiarColoniaResults = document.getElementById('familiarColoniaResults');
    if (familiarColoniaInput && familiarColoniaResults) {
        document.addEventListener('click', function(e) {
            if (!familiarColoniaInput.contains(e.target) && !familiarColoniaResults.contains(e.target)) {
                familiarColoniaResults.style.display = 'none';
            }
        });
    }

    // Ocultar resultados de apellido1
    const apellido1Input = document.getElementById('alumno_apellido1');
    const apellido1Results = document.getElementById('apellido1Results');
    if (apellido1Input && apellido1Results) {
        document.addEventListener('click', function(e) {
            if (!apellido1Input.contains(e.target) && !apellido1Results.contains(e.target)) {
                apellido1Results.style.display = 'none';
            }
        });
    }

    // Ocultar resultados de apellido2
    const apellido2Input = document.getElementById('alumno_apellido2');
    const apellido2Results = document.getElementById('apellido2Results');
    if (apellido2Input && apellido2Results) {
        document.addEventListener('click', function(e) {
            if (!apellido2Input.contains(e.target) && !apellido2Results.contains(e.target)) {
                apellido2Results.style.display = 'none';
            }
        });
    }

    // Ocultar resultados de grupo
    const grupoInput = document.getElementById('buscar_grupo');
    const grupoResults = document.getElementById('grupoResults');
    if (grupoInput && grupoResults) {
        document.addEventListener('click', function(e) {
            if (!grupoInput.contains(e.target) && !grupoResults.contains(e.target)) {
                grupoResults.style.display = 'none';
            }
        });
    }

    // Ocultar resultados de estado civil padres
    const estadoCivilInput = document.getElementById('estado_civil_padres');
    const estadoCivilResults = document.getElementById('estadoCivilResults');
    if (estadoCivilInput && estadoCivilResults) {
        document.addEventListener('click', function(e) {
            if (!estadoCivilInput.contains(e.target) && !estadoCivilResults.contains(e.target)) {
                estadoCivilResults.style.display = 'none';
            }
        });
    }

    // Ocultar resultados de transporte
    const transporteInput = document.getElementById('buscarTransporte');
    const transporteResults = document.getElementById('transporteResults');
    if (transporteInput && transporteResults) {
        document.addEventListener('click', function(e) {
            if (!transporteInput.contains(e.target) && !transporteResults.contains(e.target)) {
                transporteResults.style.display = 'none';
            }
        });
    }

    // Ocultar resultados de estado civil alumno
    const estadoCivilAlumnoInput = document.getElementById('estado_civil_alumno');
    const estadoCivilAlumnoResults = document.getElementById('estadoCivilAlumnoResults');
    if (estadoCivilAlumnoInput && estadoCivilAlumnoResults) {
        document.addEventListener('click', function(e) {
            if (!estadoCivilAlumnoInput.contains(e.target) && !estadoCivilAlumnoResults.contains(e.target)) {
                estadoCivilAlumnoResults.style.display = 'none';
            }
        });
    }

    // Ocultar resultados de deporte
    const deporteInput = document.getElementById('buscarDeporte');
    const deporteResults = document.getElementById('deporteResults');
    if (deporteInput && deporteResults) {
        document.addEventListener('click', function(e) {
            if (!deporteInput.contains(e.target) && !deporteResults.contains(e.target)) {
                deporteResults.style.display = 'none';
            }
        });
    }

    detectarConsultaAvanzada();

    // Inicializar el estado de los elementos según permisos
    const toggleEditModeElement = document.getElementById('toggle_edit_mode');
    const toggleAllSections = document.getElementById('toggle_all_sections');

    // El switch "Permitir desplegar todas las secciones" SIEMPRE debe estar habilitado
    if (toggleAllSections) {
        toggleAllSections.disabled = false;
    }

    @if($puedeEditar)
        // Si tiene permisos de edición, el switch de edición comienza desactivado
        if (toggleEditModeElement) {
            toggleEditModeElement.checked = false;
            toggleEditModeElement.dispatchEvent(new Event('change'));
        }
    @else
        // Si NO tiene permisos de edición (solo consulta)
        if (toggleEditModeElement) {
            // Ocultar el switch de edición completamente
            toggleEditModeElement.closest('.col-md-3').style.display = 'none';
        }
        
        // Deshabilitar TODOS los campos del formulario
        const editables = document.querySelectorAll('#registroForm input:not([type="hidden"]), #registroForm select, #registroForm textarea, #registroForm button');
        editables.forEach(element => {
            element.disabled = true;
            if (element.tagName === 'INPUT' && element.type !== 'checkbox' && element.type !== 'radio') {
                element.setAttribute('readonly', true);
            }
        });
        
        // Ocultar botones de acción de edición
        const actionButtons = document.querySelectorAll('#btnAgregarBeca, #btnAgregarTransporte, #btnAgregarDeporte, #btnAgregarFamiliar, #guardarTodosBtn, #cancelarTodoBtn');
        actionButtons.forEach(btn => {
            if (btn) btn.style.display = 'none';
        });
        
        // Ocultar botones "x" de tags (BECAS, TRANSPORTES Y DEPORTES)
        const closeButtons = document.querySelectorAll('.btn-close');
        closeButtons.forEach(btn => {
            btn.style.display = 'none';
        });
        
        // Deshabilitar campo de contraseña
        const passwordInput = document.getElementById('current_password_global');
        if (passwordInput) {
            passwordInput.disabled = true;
            passwordInput.readOnly = true;
        }
        
        // Deshabilitar selects de frecuencia
        const frecuenciaSelects = document.querySelectorAll('#medico_anios, #medico_meses, #dentista_anios, #dentista_meses');
        frecuenciaSelects.forEach(select => {
            if (select) select.disabled = true;
        });
        
        // Deshabilitar radios de frecuencia
        const frecuenciaRadios = document.querySelectorAll('input[name="frecuencia_medico_opcion"], input[name="frecuencia_dentista_opcion"]');
        frecuenciaRadios.forEach(radio => {
            if (radio) radio.disabled = true;
        });
    @endif
});

function actualizarBotonLimpiar() {
    const btnLimpiar = document.getElementById('btnLimpiarBusqueda');
    const busquedaInput = document.getElementById('busqueda_alumno');
    const alumnoIdHidden = document.getElementById('alumno_id_selected');
    
    if (!btnLimpiar) return;
    
    if ((busquedaInput && busquedaInput.value.trim() !== '') || 
        (alumnoIdHidden && alumnoIdHidden.value !== '')) {
        btnLimpiar.style.display = 'inline-flex';
    } else {
        btnLimpiar.style.display = 'none';
    }
}

function setupFechaDesdeCURP() {
    const curpInput = document.getElementById('curp');
    if (!curpInput) return;
    
    curpInput.addEventListener('input', function() {
        const curp = this.value.toUpperCase();
        if (curp.length >= 10) {
            const dia = curp.substring(4, 6);
            const mes = curp.substring(6, 8);
            const anio = curp.substring(8, 10);
            let anioCompleto = parseInt(anio);
            anioCompleto = anioCompleto <= 24 ? 2000 + anioCompleto : 1900 + anioCompleto;
            
            const fechaTexto = `${dia}/${mes}/${anioCompleto}`;
            const fechaDiv = document.getElementById('fecha_nacimiento');
            if (fechaDiv) fechaDiv.textContent = fechaTexto;
            
            const fechaNac = new Date(anioCompleto, parseInt(mes) - 1, parseInt(dia));
            const hoy = new Date();
            let edad = hoy.getFullYear() - fechaNac.getFullYear();
            const m = hoy.getMonth() - fechaNac.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < fechaNac.getDate())) {
                edad--;
            }
            const edadDiv = document.getElementById('edad_actual');
            if (edadDiv) edadDiv.textContent = edad + ' años';
        }
    });
}

function expandSectionWithScroll(sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) return;
    
    if (section.classList.contains('expanded')) {
        scrollToSectionHeader(sectionId);
        return;
    }
    
    document.querySelectorAll('.accordion-section').forEach(other => {
        if (other !== section) other.classList.remove('expanded');
    });
    section.classList.add('expanded');
    setTimeout(() => scrollToSectionHeader(sectionId), 150);
}

function limpiarDomicilioFamiliar() {
    const campos = ['familiar_calle', 'familiar_num_ext', 'familiar_num_int', 'familiar_colonia', 
                    'familiar_localidad', 'familiar_municipio', 'familiar_cp', 'familiar_estado', 
                    'familiar_telefono_domicilio', 'familiar_domicilio_id'];
    campos.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
}

function cancelarEdicionFamiliar() {
    modoEdicionFamiliarActivo = false;
    familiarEnEdicion = null;
    familiarEnEdicionIndex = null;
    limpiarFormularioFamiliar();
    actualizarBotonesFamiliares();
    
    // Remover resaltado de todos los tags
    document.querySelectorAll('.familiar-tag').forEach(tag => {
        tag.classList.remove('border', 'border-light');
    });
}

function limpiarFormularioFamiliar() {
    document.getElementById('parentesco').value = '';
    document.getElementById('familiar_nombre').value = '';
    document.getElementById('familiar_apellido1').value = '';
    document.getElementById('familiar_apellido2').value = '';
    document.getElementById('familiar_telefono').value = '';
    document.getElementById('familiar_escolaridad').value = '';
    document.getElementById('familiar_ocupacion').value = '';
    document.getElementById('familiar_lugar_trabajo').value = '';
    document.getElementById('familiar_horario_laboral').value = '';
    document.getElementById('familiar_domicilio_trabajo').value = '';
    document.getElementById('familiar_telefono_trabajo').value = '';
    document.getElementById('familiar_dia').value = '';
    document.getElementById('familiar_mes').value = '';
    document.getElementById('familiar_anio').value = '';
    
    document.getElementById('familiarViveSi').checked = true;
    document.getElementById('familiarViveNo').checked = false;
    document.getElementById('tutorNo').checked = true;
    document.getElementById('tutorSi').checked = false;
    document.getElementById('contactoNo').checked = true;
    document.getElementById('contactoSi').checked = false;
    
    document.getElementById('tutorSi').disabled = true;
    document.getElementById('tutorNo').disabled = false;
    
    const prioridadContainer = document.getElementById('prioridadContainer');
    const prioridadInput = document.getElementById('familiar_prioridad');
    if (prioridadContainer) prioridadContainer.style.display = 'none';
    if (prioridadInput) {
        prioridadInput.value = '';
        prioridadInput.required = false;
    }
    
    const comparteDomicilioSection = document.querySelector('input[name="comparte_domicilio"]')?.closest('.row');
    if (comparteDomicilioSection) comparteDomicilioSection.style.display = 'flex';
    document.getElementById('comparteSi').checked = true;
    document.getElementById('comparteNo').checked = false;
    document.getElementById('panelDomicilioDiferente').style.display = 'none';
    limpiarDomicilioFamiliar();
    
    document.getElementById('familiarViveSi').dispatchEvent(new Event('change'));
    document.getElementById('contactoNo').dispatchEvent(new Event('change'));
    document.getElementById('comparteSi').dispatchEvent(new Event('change'));
    
    actualizarTodosLosContadores();
    
    const resultsDivs = ['parentescoResults', 'familiarApellido1Results', 'familiarApellido2Results', 'familiarColoniaResults'];
    resultsDivs.forEach(id => {
        const div = document.getElementById(id);
        if (div) div.style.display = 'none';
    });
}

function cancelarEdicionBeca() {
    becaEnEdicion = null;
    document.getElementById('becaEnEdicionId').value = '';
    document.getElementById('buscarBeca').value = '';
    document.getElementById('becaSeleccionadaId').value = '';
    document.getElementById('estatusBecaSelect').value = '1';
    document.getElementById('btnAgregarBeca').disabled = true;
    document.getElementById('btnCancelarEdicionBeca').style.display = 'none';
    
    const btnAgregar = document.getElementById('btnAgregarBeca');
    btnAgregar.innerHTML = '<i class="fas fa-plus"></i> Añadir Beca';
    
    document.querySelectorAll('.beca-tag').forEach(tag => {
        tag.classList.remove('border', 'border-primary');
    });
}

function setupBecaPanelToggle() {
    const radios = document.querySelectorAll('input[name="tiene_beca"]');
    const panelBecas = document.getElementById('panelBecas');
    
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'si') {
                panelBecas.style.display = 'block';
            } else {
                panelBecas.style.display = 'none';
                document.getElementById('buscarBeca').value = '';
                document.getElementById('becaSeleccionadaId').value = '';
                document.getElementById('btnAgregarBeca').disabled = true;
                cancelarEdicionBeca();
            }
        });
    });
}

// ===========================================
// FUNCIONES PARA GESTIONAR BOTONES SEGÚN MODO
// ===========================================

function actualizarBotonesFamiliares() {
    const isEditingMode = document.getElementById('toggle_edit_mode')?.checked || false;
    const puedeEditar = @json($puedeEditar);
    
    const btnAgregar = document.getElementById('btnAgregarFamiliar');
    const btnActualizar = document.getElementById('btnActualizarFamiliar');
    // CANCELAR CONSULTA (modo solo lectura)
    const btnCancelarConsulta = document.getElementById('btnCancelarConsultaFamiliar');
    if (btnCancelarConsulta) {
        btnCancelarConsulta.addEventListener('click', function() {
            cancelarConsultaFamiliar();
        });
    }

    // CANCELAR REGISTRO (nuevo familiar)
    const btnCancelarRegistro = document.getElementById('btnCancelarRegistroFamiliar');
    if (btnCancelarRegistro) {
        btnCancelarRegistro.addEventListener('click', function() {
            cancelarRegistroFamiliar();
        });
    }

    // CANCELAR EDICIÓN (familiar existente)
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicionFamiliar');
    if (btnCancelarEdicion) {
        btnCancelarEdicion.addEventListener('click', function() {
            cancelarEdicionFamiliar();
        });
    }
    
    // Si NO tiene permisos de edición -> modo consulta (botón Cancelar Consulta SIEMPRE habilitado)
    if (!puedeEditar) {
        if (btnAgregar) btnAgregar.style.display = 'none';
        if (btnActualizar) btnActualizar.style.display = 'none';
        if (btnCancelarEdicion) btnCancelarEdicion.style.display = 'none';
        if (btnCancelarRegistro) btnCancelarRegistro.style.display = 'none';
        if (btnCancelarConsulta) {
            btnCancelarConsulta.style.display = 'inline-block';
            btnCancelarConsulta.disabled = false;  // ← Asegurar que está habilitado
        }
        return;
    }
    
    // Tiene permisos de edición
    if (!isEditingMode) {
        // Modo consulta con permisos (solo ver, no editar familiares)
        if (btnAgregar) btnAgregar.style.display = 'none';
        if (btnActualizar) btnActualizar.style.display = 'none';
        if (btnCancelarEdicion) btnCancelarEdicion.style.display = 'none';
        if (btnCancelarRegistro) btnCancelarRegistro.style.display = 'none';
        if (btnCancelarConsulta) {
            btnCancelarConsulta.style.display = 'inline-block';
            btnCancelarConsulta.disabled = false;  // ← Asegurar que está habilitado
        }
    } else {
        // Modo edición activado
        if (btnCancelarConsulta) btnCancelarConsulta.style.display = 'none';
        
        if (modoEdicionFamiliarActivo && familiarEnEdicion && familiarEnEdicion.id) {
            // Editando un familiar EXISTENTE
            if (btnAgregar) btnAgregar.style.display = 'none';
            if (btnActualizar) btnActualizar.style.display = 'inline-block';
            if (btnCancelarEdicion) btnCancelarEdicion.style.display = 'inline-block';
            if (btnCancelarRegistro) btnCancelarRegistro.style.display = 'none';
        } else {
            // Modo creación de nuevo familiar
            if (btnAgregar) btnAgregar.style.display = 'inline-block';
            if (btnActualizar) btnActualizar.style.display = 'none';
            if (btnCancelarEdicion) btnCancelarEdicion.style.display = 'none';
            if (btnCancelarRegistro) btnCancelarRegistro.style.display = 'inline-block';
        }
    }
}

// ===========================================
// CANCELAR CONSULTA (modo solo lectura)
// ===========================================

function cancelarConsultaFamiliar() {
    limpiarFormularioFamiliar();
    // Remover resaltado de tags
    document.querySelectorAll('.familiar-tag').forEach(tag => {
        tag.classList.remove('border', 'border-light');
    });
    // No cambiar el estado de edición
}

// ===========================================
// CANCELAR REGISTRO (nuevo familiar no guardado)
// ===========================================

function cancelarRegistroFamiliar() {
    limpiarFormularioFamiliar();
    modoEdicionFamiliarActivo = false;
    familiarEnEdicion = null;
    familiarEnEdicionIndex = null;
    actualizarBotonesFamiliares();
}

// ===========================================
// FUNCIONES PARA TOGGLES DE SALUD
// ===========================================

function setupToggleContainer(radioName, containerId, textareaId, counterId) {
    document.querySelectorAll(`input[name="${radioName}"]`).forEach(radio => {
        radio.addEventListener('change', function() {
            const container = document.getElementById(containerId);
            if (this.value === '1') {
                if (container) container.style.display = 'block';
            } else {
                if (container) container.style.display = 'none';
                if (textareaId && document.getElementById(textareaId)) document.getElementById(textareaId).value = '';
                if (counterId && document.getElementById(counterId)) document.getElementById(counterId).textContent = '0';
            }
        });
    });
}

// ===========================================
// ELIMINAR TEMPORALES
// ===========================================

window.eliminarFamiliarTemporal = function(tempId) {
    // Solo se puede eliminar si es temporal (no guardado)
    const familiar = familiaresTemporales.find(f => f.temp_id === tempId);
    if (familiar && !familiar.id) {
        familiaresTemporales = familiaresTemporales.filter(f => f.temp_id !== tempId);
        renderizarFamiliaresAsignados();
        
        // Si estábamos editando este familiar, cancelar edición
        if (familiarEnEdicion && familiarEnEdicion.temp_id === tempId) {
            cancelarEdicionFamiliar();
        }
    }
};

window.eliminarTransporteTemporal = function(index) { transportesTemporales.splice(index, 1); renderizarTransportesAsignados(); };
window.eliminarDeporteTemporal = function(index) { deportesTemporales.splice(index, 1); renderizarDeportesAsignados(); };
window.eliminarBecaTemporal = function(id) {
    // Primero verificar si es una beca nueva (con id string que comienza con 'new_')
    if (typeof id === 'string' && id.startsWith('new_')) {
        // Eliminar del array temporal
        becasTemporales = becasTemporales.filter(b => b.id !== id);
        renderizarBecasAsignadas();
        
        // Si no hay más becas, ocultar el panel
        if (becasTemporales.length === 0) {
            document.getElementById('becaNo').checked = true;
            document.getElementById('panelBecas').style.display = 'none';
        }
    } else {
        // Eliminar beca existente de la base de datos
        if (!confirm('¿Estás seguro de eliminar esta beca? Esta acción se guardará inmediatamente.')) return;
        
        // Primero, verificar si hay contraseña ingresada
        const currentPassword = document.getElementById('current_password_global')?.value;
        if (!currentPassword) {
            alert('Debes ingresar tu contraseña para eliminar una beca.');
            document.getElementById('current_password_global')?.focus();
            return;
        }
        
        fetch('{{ route("alumnos.eliminar-beca-alumno") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                id_beca: id,
                current_password: currentPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                becasTemporales = becasTemporales.filter(b => b.id !== id);
                renderizarBecasAsignadas();
                
                if (becasTemporales.length === 0) {
                    document.getElementById('becaNo').checked = true;
                    document.getElementById('panelBecas').style.display = 'none';
                }
                
                // Limpiar contraseña
                if (document.getElementById('current_password_global')) {
                    document.getElementById('current_password_global').value = '';
                }
                
                alert(data.message || 'Beca eliminada correctamente');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al eliminar la beca.');
        });
    }
};

// ===========================================
// RENDERIZAR TAGS (funciones básicas)
// ===========================================

function ordenarFamiliaresPorPrioridad(familiares) {
    return [...familiares].sort((a, b) => {
        const prioridadA = a.relacion.contacto_emergencia || 0;
        const prioridadB = b.relacion.contacto_emergencia || 0;
        
        // Los contactos (prioridad > 0) van primero, ordenados ascendentemente
        if (prioridadA > 0 && prioridadB > 0) {
            return prioridadA - prioridadB;
        }
        // Si solo A es contacto, va primero
        if (prioridadA > 0) return -1;
        // Si solo B es contacto, va primero
        if (prioridadB > 0) return 1;
        // Si ninguno es contacto, mantener orden original (por índice)
        return 0;
    });
}

function renderizarFamiliaresAsignados() {
    const contenedor = document.getElementById('familiaresAsignados');
    if (!contenedor) return;
    
    if (familiaresTemporales.length === 0) {
        contenedor.innerHTML = '<span class="text-muted">No hay familiares añadidos</span>';
        return;
    }
    
    // Ordenar familiares: primero contactos por prioridad, luego no contactos
    const contactos = familiaresTemporales.filter(f => f.relacion.contacto_emergencia > 0)
        .sort((a, b) => a.relacion.contacto_emergencia - b.relacion.contacto_emergencia);
    const noContactos = familiaresTemporales.filter(f => f.relacion.contacto_emergencia === 0);
    const familiaresOrdenados = [...contactos, ...noContactos];
    
    let html = '';
    familiaresOrdenados.forEach((f, displayIndex) => {
        // Encontrar el índice original para mantener data-index correcto
        const originalIndex = familiaresTemporales.findIndex(original => 
            (original.id && f.id && original.id === f.id) || 
            (original.temp_id && f.temp_id && original.temp_id === f.temp_id) ||
            (!original.id && !f.id && original.temp_id === f.temp_id)
        );
        
        const prioridad = f.relacion.contacto_emergencia;
        const esContacto = prioridad > 0;
        const tutor = f.relacion.tutor;
        
        let colorClase = 'bg-primary';
        if (!esContacto) {
            colorClase = 'bg-secondary';
        } else if (tutor) {
            colorClase = 'bg-info';
        }
        
        const contactoTexto = esContacto ? `Contacto ${prioridad}` : 'No contactar';
        const tutorTexto = tutor ? ' | Tutor' : '';
        const nombreCompleto = `${f.familiar.nombre} ${f.familiar.apellido1}`;
        
        // Determinar si es un familiar nuevo (en caché, no guardado en BD)
        const esNuevo = !f.id;  // Si no tiene ID, es nuevo
        const isEditing = document.getElementById('toggle_edit_mode')?.checked || false;
        
        // Solo mostrar la X si es un familiar nuevo Y está en modo edición
        const mostrarBotonEliminar = esNuevo && isEditing;
        
        html += `<span class="badge ${colorClase} p-2 d-inline-flex align-items-center me-2 mb-2 familiar-tag" 
                        data-id="${f.id || f.temp_id}"
                        data-index="${originalIndex}"
                        style="cursor: pointer;">
            ${nombreCompleto} | ${f.relacion.parentesco} | ${contactoTexto}${tutorTexto}
            ${mostrarBotonEliminar ? `<button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" onclick="event.stopPropagation(); eliminarFamiliarTemporal(${f.temp_id})" aria-label="Eliminar"></button>` : ''}
        </span>`;
    });
    contenedor.innerHTML = html;
    
    // Agregar event listeners para cargar datos del familiar al hacer clic
    document.querySelectorAll('.familiar-tag').forEach((tag) => {
        tag.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-close')) return;
            
            const index = parseInt(this.dataset.index);
            const familiar = familiaresTemporales[index];
            if (familiar) {
                cargarFamiliarEnFormulario(familiar, index);
                document.querySelectorAll('.familiar-tag').forEach(t => t.classList.remove('border', 'border-light'));
                this.classList.add('border', 'border-light');
            }
        });
    });
}

function renderizarTransportesAsignados() {
    const contenedor = document.getElementById('transportesAsignados');
    if (!contenedor) return;
    if (transportesTemporales.length === 0) {
        contenedor.innerHTML = '<span class="text-muted">No hay transportes añadidos</span>';
        return;
    }
    const isEditing = document.getElementById('toggle_edit_mode')?.checked || false;
    const puedeEditar = @json($puedeEditar);
    const mostrarBotones = puedeEditar && isEditing;
    
    let html = '';
    transportesTemporales.forEach((transporte, index) => {
        html += `<span class="badge bg-success p-2 d-inline-flex align-items-center">
            ${transporte}
            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem; ${!mostrarBotones ? 'display: none;' : ''}" onclick="eliminarTransporteTemporal(${index})" aria-label="Eliminar"></button>
        </span>`;
    });
    contenedor.innerHTML = html;
}

function renderizarDeportesAsignados() {
    const contenedor = document.getElementById('deportesAsignados');
    if (!contenedor) return;
    if (deportesTemporales.length === 0) {
        contenedor.innerHTML = '<span class="text-muted">No hay deportes añadidos</span>';
        return;
    }
    const isEditing = document.getElementById('toggle_edit_mode')?.checked || false;
    const puedeEditar = @json($puedeEditar);
    const mostrarBotones = puedeEditar && isEditing;
    
    let html = '';
    deportesTemporales.forEach((deporte, index) => {
        html += `<span class="badge bg-danger p-2 d-inline-flex align-items-center">
            ${deporte}
            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem; ${!mostrarBotones ? 'display: none;' : ''}" onclick="eliminarDeporteTemporal(${index})" aria-label="Eliminar"></button>
        </span>`;
    });
    contenedor.innerHTML = html;
}

function renderizarBecasAsignadas() {
    const contenedor = document.getElementById('becasAsignadas');
    if (!contenedor) return;
    
    if (becasTemporales.length === 0) {
        contenedor.innerHTML = '<span class="text-muted">No hay becas asignadas</span>';
        return;
    }
    
    let html = '';
    becasTemporales.forEach((beca, index) => {
        const estatusTexto = beca.activa ? 'Activa' : 'Inactiva';
        const estatusClase = beca.activa ? 'bg-warning' : 'bg-secondary';
        
        // Dentro de renderizarBecasAsignadas
        const isEditing = document.getElementById('toggle_edit_mode')?.checked || false;
        html += `<span class="badge ${estatusClase} p-2 d-inline-flex align-items-center me-2 mb-2 beca-tag" 
                            data-id="${beca.id}" 
                            data-nombre="${beca.nombre}" 
                            data-activa="${beca.activa}"
                            data-index="${index}"
                            style="cursor: pointer;">
                ${beca.nombre} | ${estatusTexto}
                <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem; ${!isEditing ? 'display: none;' : ''}" 
                        onclick="event.stopPropagation(); eliminarBecaTemporal('${beca.id}')" aria-label="Eliminar">
                </button>
            </span>`;
    });
    contenedor.innerHTML = html;
    
    // Remover todos los event listeners antiguos y agregar nuevos
    const tags = document.querySelectorAll('.beca-tag');
    tags.forEach(tag => {
        // Clonar y reemplazar para eliminar event listeners antiguos
        const newTag = tag.cloneNode(true);
        tag.parentNode.replaceChild(newTag, tag);
        
        newTag.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-close')) return;
            
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const activa = this.dataset.activa === 'true';
            
            document.getElementById('buscarBeca').value = nombre;
            document.getElementById('becaSeleccionadaId').value = id;
            document.getElementById('estatusBecaSelect').value = activa ? '1' : '0';
            document.getElementById('btnAgregarBeca').disabled = false;
            document.getElementById('btnCancelarEdicionBeca').style.display = 'inline-block';
            
            const btnAgregar = document.getElementById('btnAgregarBeca');
            btnAgregar.innerHTML = '<i class="fas fa-save"></i> Actualizar Beca';
            
            becaEnEdicion = { id, nombre, activa };
            document.getElementById('becaEnEdicionId').value = id;
            
            document.querySelectorAll('.beca-tag').forEach(t => t.classList.remove('border', 'border-primary'));
            this.classList.add('border', 'border-primary');
        });
    });
}

// ===========================================
// BÚSQUEDAS SIMPLIFICADAS
// ===========================================

function buscarLugares(query) {
    if (query.length < 2) {
        document.getElementById('localidadResults').style.display = 'none';
        return;
    }
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-lugar") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.lugares.length > 0) {
                const resultsDiv = document.getElementById('localidadResults');
                resultsDiv.innerHTML = '';
                resultadosLugares = data.lugares;
                data.lugares.forEach((lugar, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${lugar.localidad}</strong> - ${lugar.municipio}, ${lugar.estado}, ${lugar.pais}`;
                    div.addEventListener('click', () => seleccionarLugar(lugar));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedIndex = -1;
            } else {
                document.getElementById('localidadResults').style.display = 'none';
            }
        });
    }, 300);
}

// Función para seleccionar lugar de nacimiento
function seleccionarLugar(lugar) {
    document.getElementById('localidad').value = lugar.localidad;
    document.getElementById('municipio').value = lugar.municipio;
    document.getElementById('estado').value = lugar.estado;
    document.getElementById('pais').value = lugar.pais;
    document.getElementById('lugarNacimientoId').value = lugar.id;
    updateCounter(document.getElementById('localidad'), 'localidadCounter');
    updateCounter(document.getElementById('municipio'), 'municipioCounter');
    updateCounter(document.getElementById('estado'), 'estadoCounter');
    updateCounter(document.getElementById('pais'), 'paisCounter');
    document.getElementById('localidadResults').style.display = 'none';
}

// Función para seleccionar domicilio (colonia)
function seleccionarDomicilio(domicilio) {
    document.getElementById('colonia').value = domicilio.colonia;
    document.getElementById('domicilio_localidad').value = domicilio.localidad;
    document.getElementById('domicilio_municipio').value = domicilio.municipio;
    document.getElementById('cp').value = domicilio.cp;
    document.getElementById('domicilio_estado').value = domicilio.estado;
    document.getElementById('domicilioId').value = domicilio.id;
    updateCounter(document.getElementById('colonia'), 'coloniaCounter');
    updateCounter(document.getElementById('domicilio_localidad'), 'domicilioLocalidadCounter');
    updateCounter(document.getElementById('domicilio_municipio'), 'domicilioMunicipioCounter');
    updateCounter(document.getElementById('cp'), 'cpCounter');
    updateCounter(document.getElementById('domicilio_estado'), 'domicilioEstadoCounter');
    document.getElementById('coloniaResults').style.display = 'none';
}

// Función para seleccionar secundaria
function seleccionarSecundaria(secundaria) {
    document.getElementById('secundaria_nombre').value = secundaria.nombre;
    document.getElementById('secundaria_tipo').value = secundaria.tipo;
    document.getElementById('secundaria_localidad').value = secundaria.localidad;
    document.getElementById('secundaria_municipio').value = secundaria.municipio;
    document.getElementById('secundaria_estado').value = secundaria.estado;
    document.getElementById('secundaria_pais').value = secundaria.pais;
    document.getElementById('secundariaId').value = secundaria.id;
    updateCounter(document.getElementById('secundaria_nombre'), 'secundariaNombreCounter');
    updateCounter(document.getElementById('secundaria_localidad'), 'secundariaLocalidadCounter');
    updateCounter(document.getElementById('secundaria_municipio'), 'secundariaMunicipioCounter');
    updateCounter(document.getElementById('secundaria_estado'), 'secundariaEstadoCounter');
    updateCounter(document.getElementById('secundaria_pais'), 'secundariaPaisCounter');
    document.getElementById('secundariaResults').style.display = 'none';
}

// Función para cargar datos del alumno (placeholder)
function cargarDatosAlumno(alumno) {
    console.log('Cargando alumno:', alumno);
    // Aquí se cargarán los datos del alumno en el formulario
}

// BOTÓN DE GUARDAR
document.getElementById('guardarTodosBtn').addEventListener('click', async function() {
    // Validar formulario
    if (!validarFormularioCompleto()) {
        return;
    }
    
    const password = document.getElementById('current_password_global').value;
    if (!password) {
        alert('Debe ingresar su contraseña para guardar los cambios.');
        document.getElementById('current_password_global').focus();
        return;
    }
    
    const alumnoId = document.getElementById('alumnoId').value;
    if (!alumnoId) {
        alert('No hay un alumno seleccionado.');
        return;
    }

    const estatusSeleccionado = document.getElementById('estatusSelect').value;

    // Si el alumno se está desactivando (estatus = 0)
    if (estatusSeleccionado === '0') {
        // Limpiar grupo
        document.getElementById('id_grupo').value = '';
        document.getElementById('buscar_grupo').value = '';
        
        // Limpiar becas temporales
        becasTemporales = [];
        renderizarBecasAsignadas();
        
        // Ocultar panel de becas
        document.getElementById('becaNo').checked = true;
        document.getElementById('panelBecas').style.display = 'none';
    }
    
    // Estructurar datos según el formato que espera el controlador
    const datosActualizar = {
        alumno_id: alumnoId,
        current_password: password,
        estatus: document.getElementById('estatusSelect').value,
        
        // Datos básicos del alumno
        nombre: document.getElementById('alumno_nombre').value,
        apellido1: document.getElementById('alumno_apellido1').value,
        apellido2: document.getElementById('alumno_apellido2').value,
        num_control: document.getElementById('num_control').value,
        telefono_celular: document.getElementById('telefono_celular').value,
        email_personal: document.getElementById('email_personal').value,
        email_institucional: document.getElementById('email_institucional').value,
        curp: document.getElementById('curp').value,
        nss: document.getElementById('nss').value,
        id_grupo: document.getElementById('id_grupo').value || null,
        
        // Lugar de nacimiento
        lugar_nacimiento: {
            localidad: document.getElementById('localidad').value,
            municipio: document.getElementById('municipio').value,
            estado: document.getElementById('estado').value,
            pais: document.getElementById('pais').value
        },
        
        // Domicilio
        domicilio: {
            calle: document.getElementById('calle').value,
            num_ext: document.getElementById('num_ext').value,
            num_int: document.getElementById('num_int').value,
            colonia: document.getElementById('colonia').value,
            localidad: document.getElementById('domicilio_localidad').value,
            municipio: document.getElementById('domicilio_municipio').value,
            cp: document.getElementById('cp').value,
            estado: document.getElementById('domicilio_estado').value,
            telefono_domicilio: document.getElementById('telefono_domicilio').value
        },
        
        // Secundaria
        secundaria: {
            nombre: document.getElementById('secundaria_nombre').value,
            tipo: document.getElementById('secundaria_tipo').value,
            localidad: document.getElementById('secundaria_localidad').value,
            municipio: document.getElementById('secundaria_municipio').value,
            estado: document.getElementById('secundaria_estado').value,
            pais: document.getElementById('secundaria_pais').value
        },

        // Becas
        becas: becasTemporales.map(b => ({
            id: b.id,
            nombre: b.nombre,
            activa: b.activa
        })),
        
        // Trabajo
        trabajo: {
            lugar_trabajo: document.getElementById('lugar_trabajo').value,
            horario_laboral: document.getElementById('horario_laboral').value,
            domicilio_trabajo: document.getElementById('domicilio_trabajo').value,
            telefono_trabajo: document.getElementById('telefono_trabajo').value
        },

        // Agregar esta línea:
        familiares: familiaresTemporales.map(f => ({
            id: f.id || null,
            temp_id: f.temp_id || null,
            familiar: {
                vive: f.familiar.vive,
                nombre: f.familiar.nombre,
                apellido1: f.familiar.apellido1,
                apellido2: f.familiar.apellido2,
                fecha_nacimiento: f.familiar.fecha_nacimiento,
                telefono_celular: f.familiar.telefono_celular,
                id_domicilio: f.familiar.id_domicilio || null,
                domicilio: f.familiar.domicilio || null,
                escolaridad: f.familiar.escolaridad,
                ocupacion: f.familiar.ocupacion,
                lugar_trabajo: f.familiar.lugar_trabajo,
                horario_laboral: f.familiar.horario_laboral,
                domicilio_trabajo: f.familiar.domicilio_trabajo,
                telefono_trabajo: f.familiar.telefono_trabajo,
            },
            relacion: {
                parentesco: f.relacion.parentesco,
                tutor: f.relacion.tutor,
                contacto_emergencia: f.relacion.contacto_emergencia,
                comparte_domicilio: f.relacion.comparte_domicilio
            }
        })),
        
        // Datos Familiares (sección 2.2)
        datos_familiares: {
            estado_civil_padres: document.getElementById('estado_civil_padres').value,
            ingreso_familiar_aprox: document.getElementById('ingreso_familiar').value ? parseInt(document.getElementById('ingreso_familiar').value) : null,
            gasto_familiar_aprox: document.getElementById('gasto_familiar').value ? parseInt(document.getElementById('gasto_familiar').value) : null,
            casa_propia: document.querySelector('input[name="casa_propia"]:checked')?.value === '1',
            auto_propio_familia: document.querySelector('input[name="auto_propio"]:checked')?.value === '1',
            servicios: obtenerServiciosSeleccionados()
        },
        
        // Info Socioeconómica
        info_socioeco: {
            auto_propio_alumno: document.querySelector('input[name="auto_propio_alumno"]:checked')?.value === '1',
            transportes: transportesTemporales,
            traslado_horas: document.getElementById('traslado_horas').value ? parseInt(document.getElementById('traslado_horas').value) : null,
            traslado_minutos: document.getElementById('traslado_minutos').value ? parseInt(document.getElementById('traslado_minutos').value) : null,
            estado_civil_alumno: document.getElementById('estado_civil_alumno').value,
            num_hijos: document.getElementById('hijosSi').checked ? (parseInt(document.getElementById('num_hijos').value) || 0) : 0,
            edades_hijos: document.getElementById('hijosSi').checked ? document.getElementById('edades_hijos').value : null,
            monto_apoyo: document.getElementById('apoyoSi').checked ? (parseInt(document.getElementById('monto_apoyo').value) || null) : null,
            gasto_comida_transporte: document.getElementById('gasto_comida_transporte').value ? parseInt(document.getElementById('gasto_comida_transporte').value) : null,
            comidas_diarias: parseInt(document.getElementById('comidas_diarias').value) || 3
        },
        
        // Datos Académicos
        datos_academicos: {
            dispositivos: obtenerDispositivosSeleccionados(),
            otro_dispositivo: document.getElementById('otro_dispositivo').value
        },
        
        // Problema de Aprendizaje
        problema_aprendizaje: {
            tiene_problema: document.getElementById('problemaSi').checked,
            caracteristicas: obtenerCaracteristicasSeleccionadas(),
            otro_problema: document.getElementById('otro_problema').value
        },
        
        // Datos Salud
        datos_salud: {
            estatura: parseInt(document.getElementById('estatura').value),
            peso: parseFloat(document.getElementById('peso').value),
            tipo_sangre: document.getElementById('tipo_sangre').value,
            cuadro_basico_vacunas: document.getElementById('vacunasSi').checked,
            usa_anteojos: document.getElementById('anteojosSi').checked,
            graduacion_anteojos: document.getElementById('anteojosSi').checked ? parseFloat(document.getElementById('graduacion_anteojos').value) : null,
            cirugia: document.getElementById('cirugiaSi').checked ? document.getElementById('cirugia').value : null,
            alergia: document.getElementById('alergiaSi').checked ? document.getElementById('alergia').value : null,
            limitante_fisico: document.getElementById('limitanteSi').checked ? document.getElementById('limitante_fisico').value : null,
            problema_auditivo: document.getElementById('auditivoSi').checked ? document.getElementById('problema_auditivo').value : null,
            adiccion: document.getElementById('adiccionSi').checked ? document.getElementById('adiccion').value : null,
            padecimiento_emocional: document.getElementById('emocionalSi').checked ? document.getElementById('padecimiento_emocional').value : null,
            enfermedad_actual: document.getElementById('enfermedad_actual').value,
            tiene_sintomas: document.getElementById('sintomasSi').checked,
            sintomas: obtenerSintomasSeleccionados(),
            otro_sintoma: document.getElementById('sintomasSi').checked ? document.getElementById('otro_sintoma').value : null,
            medicamento_controlado: document.getElementById('medicamentoSi').checked ? document.getElementById('medicamento_controlado').value : null,
            alergia_medicamento: document.getElementById('alergiaMedSi').checked ? document.getElementById('alergia_medicamento').value : null,
            motivo_hospitalizacion: document.getElementById('hospitalizacionSi').checked ? document.getElementById('motivo_hospitalizacion').value : null,
            diabetes: document.getElementById('diabetesSi').checked,
            hipertension: document.getElementById('hipertensionSi').checked,
            dolores_cabeza: document.getElementById('cabezaSi').checked,
            dolores_estomago: document.getElementById('estomagoSi').checked,
            frecuencia_medico: obtenerFrecuencia('medico'),
            frecuencia_dentista: obtenerFrecuencia('dentista')
        },
        
        // Actividades Recreativas
        actividades_recreativas: {
            pasatiempo_favorito: document.getElementById('pasatiempo_favorito').value,
            horas_pasatiempo: document.getElementById('horas_pasatiempo').value ? parseInt(document.getElementById('horas_pasatiempo').value) : null,
            deportes: deportesTemporales,
            horas_deporte: document.getElementById('horas_deporte').value ? parseInt(document.getElementById('horas_deporte').value) : null,
            horas_tv: document.getElementById('horas_tv').value ? parseInt(document.getElementById('horas_tv').value) : null,
            horas_compu: document.getElementById('horas_compu').value ? parseInt(document.getElementById('horas_compu').value) : null,
            uso_compu: document.getElementById('uso_compu').value,
            chatea: document.getElementById('chatearSi').checked,
            temas_chat: document.getElementById('chatearSi').checked ? document.getElementById('temas_chat').value : null
        }
    };
    
    // Enviar datos
    try {
        const response = await fetch('{{ route("alumnos.actualizar-alumno-completo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(datosActualizar)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            
            // Guardar referencia del familiar que se estaba editando antes de recargar
            const familiarEnEdicionBackup = familiarEnEdicion ? { ...familiarEnEdicion } : null;
            const familiarEnEdicionIndexBackup = familiarEnEdicionIndex;
            
            // Recargar SOLO los datos del alumno
            await cargarDatosAlumnoCompleto(alumnoId);
            
            // Limpiar contraseña
            document.getElementById('current_password_global').value = '';
            
            // Si se estaba editando un familiar, volver a cargar sus datos en el formulario
            if (familiarEnEdicionBackup) {
                // Buscar el familiar actualizado en familiaresTemporales
                const familiarActualizado = familiaresTemporales.find((f, idx) => {
                    if (familiarEnEdicionBackup.id && f.id === familiarEnEdicionBackup.id) {
                        familiarEnEdicionIndexBackupActualizado = idx;
                        return true;
                    }
                    if (familiarEnEdicionBackup.temp_id && f.temp_id === familiarEnEdicionBackup.temp_id) {
                        familiarEnEdicionIndexBackupActualizado = idx;
                        return true;
                    }
                    return false;
                });
                
                if (familiarActualizado) {
                    cargarFamiliarEnFormulario(familiarActualizado, familiarEnEdicionIndexBackupActualizado);
                } else {
                    cancelarEdicionFamiliar();
                }
            } else {
                cancelarEdicionFamiliar();
            }
        }
    } catch (error) {
        console.error('Error en la petición:', error);
        alert('Error de conexión al guardar los cambios.');
    }
});

// Funciones auxiliares para obtener datos
function obtenerServiciosSeleccionados() {
    const servicios = [];
    if (document.getElementById('servicioLuz').checked) servicios.push(1);
    if (document.getElementById('servicioAgua').checked) servicios.push(2);
    if (document.getElementById('servicioDrenaje').checked) servicios.push(3);
    if (document.getElementById('servicioAlumbrado').checked) servicios.push(4);
    return servicios;
}

function obtenerDispositivosSeleccionados() {
    const dispositivos = [];
    if (document.getElementById('dispositivoCelular').checked) dispositivos.push(1);
    if (document.getElementById('dispositivoComputadora').checked) dispositivos.push(2);
    if (document.getElementById('dispositivoInternet').checked) dispositivos.push(3);
    if (document.getElementById('dispositivoTablet').checked) dispositivos.push(4);
    return dispositivos;
}

function obtenerCaracteristicasSeleccionadas() {
    const caracteristicas = [];
    const ids = ['problemaDislexia', 'problemaVisuales', 'problemaAuditivo', 'problemaNoEntender', 
                 'problemaLentitud', 'problemaLenguaje', 'problemaComunicacion', 'problemaDiscriminacion',
                 'problemaGestos', 'problemaDistraccion', 'problemaTDAH', 'problemaDiscapacidad'];
    ids.forEach((id, index) => {
        if (document.getElementById(id).checked) caracteristicas.push(index + 1);
    });
    return caracteristicas;
}

function obtenerSintomasSeleccionados() {
    const sintomas = [];
    if (document.getElementById('sintomaAnsiedad').checked) sintomas.push(1);
    if (document.getElementById('sintomaEstres').checked) sintomas.push(2);
    if (document.getElementById('sintomaDepresion').checked) sintomas.push(3);
    if (document.getElementById('sintomaCulpa').checked) sintomas.push(4);
    if (document.getElementById('sintomaTristeza').checked) sintomas.push(5);
    return sintomas;
}

function obtenerFrecuencia(prefix) {
    const opcionNunca = document.getElementById(`${prefix}Nunca`);
    const opcionSoloEnfermo = document.getElementById(`${prefix}SoloEnfermo`);
    const aniosSelect = document.getElementById(`${prefix}_anios`);
    const mesesSelect = document.getElementById(`${prefix}_meses`);
    
    if (opcionNunca && opcionNunca.checked) return 0;
    if (opcionSoloEnfermo && opcionSoloEnfermo.checked) return -1;
    
    const anios = parseInt(aniosSelect?.value) || 0;
    const meses = parseInt(mesesSelect?.value) || 0;
    return anios + meses;
}

// ===========================================
// BÚSQUEDAS DE APELLIDOS EN SECCIÓN 1.1
// ===========================================

function buscarApellido(tipo, query) {
    if (query.length < 2) {
        document.getElementById(tipo === 'apellido1' ? 'apellido1Results' : 'apellido2Results').style.display = 'none';
        return;
    }
    clearTimeout(apellidoTimeout);
    apellidoTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-apellido") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ tipo: tipo, busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.resultados.length > 0) {
                const resultsDiv = document.getElementById(tipo === 'apellido1' ? 'apellido1Results' : 'apellido2Results');
                resultsDiv.innerHTML = '';
                if (tipo === 'apellido1') resultadosApellido1 = data.resultados;
                else resultadosApellido2 = data.resultados;
                
                data.resultados.forEach((valor, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${valor}</strong>`;
                    div.addEventListener('click', () => seleccionarApellido(tipo, valor));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                if (tipo === 'apellido1') selectedApellido1Index = -1;
                else selectedApellido2Index = -1;
            } else {
                document.getElementById(tipo === 'apellido1' ? 'apellido1Results' : 'apellido2Results').style.display = 'none';
            }
        });
    }, 300);
}

function seleccionarApellido(tipo, valor) {
    const inputId = tipo === 'apellido1' ? 'alumno_apellido1' : 'alumno_apellido2';
    const counterId = tipo === 'apellido1' ? 'alumnoApellido1Counter' : 'alumnoApellido2Counter';
    
    document.getElementById(inputId).value = valor;
    updateCounter(document.getElementById(inputId), counterId);
    document.getElementById(tipo === 'apellido1' ? 'apellido1Results' : 'apellido2Results').style.display = 'none';
}

// ===========================================
// BÚSQUEDA DE COLONIA (Domicilio)
// ===========================================

function buscarColonia(query) {
    if (query.length < 2) {
        document.getElementById('coloniaResults').style.display = 'none';
        return;
    }
    clearTimeout(domicilioTimeout);
    domicilioTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-domicilio") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.domicilios.length > 0) {
                const resultsDiv = document.getElementById('coloniaResults');
                resultsDiv.innerHTML = '';
                resultadosDomicilios = data.domicilios;
                data.domicilios.forEach((domicilio, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${domicilio.colonia}</strong> - ${domicilio.localidad}, ${domicilio.municipio}, ${domicilio.estado}`;
                    div.addEventListener('click', () => seleccionarDomicilio(domicilio));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedDomicilioIndex = -1;
            } else {
                document.getElementById('coloniaResults').style.display = 'none';
            }
        });
    }, 300);
}

// ===========================================
// BÚSQUEDA DE SECUNDARIA
// ===========================================

function buscarSecundaria(query) {
    if (query.length < 2) {
        document.getElementById('secundariaResults').style.display = 'none';
        return;
    }
    clearTimeout(secundariaTimeout);
    secundariaTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-secundaria") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.secundarias.length > 0) {
                const resultsDiv = document.getElementById('secundariaResults');
                resultsDiv.innerHTML = '';
                resultadosSecundarias = data.secundarias;
                data.secundarias.forEach((sec, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${sec.nombre}</strong> - ${sec.localidad}, ${sec.municipio}, ${sec.estado}`;
                    div.addEventListener('click', () => seleccionarSecundaria(sec));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedSecundariaIndex = -1;
            } else {
                document.getElementById('secundariaResults').style.display = 'none';
            }
        });
    }, 300);
}

// ===========================================
// BÚSQUEDA DE GRUPOS
// ===========================================

function buscarGrupos(query) {
    clearTimeout(grupoTimeout);
    grupoTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-grupos") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.grupos.length > 0) {
                const resultsDiv = document.getElementById('grupoResults');
                resultsDiv.innerHTML = '';
                resultadosGrupos = data.grupos;
                
                data.grupos.forEach((grupo, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${grupo.nombre}</strong><br><small>Asesor: ${grupo.asesor} | Tutor: ${grupo.tutor}</small>`;
                    div.addEventListener('click', () => seleccionarGrupo(grupo));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedGrupoIndex = -1;
            } else {
                document.getElementById('grupoResults').style.display = 'none';
            }
        });
    }, 300);
}

function seleccionarGrupo(grupo) {
    document.getElementById('buscar_grupo').value = grupo.nombre;
    document.getElementById('id_grupo').value = grupo.id;
    document.getElementById('grupoResults').style.display = 'none';
}

// ===========================================
// BÚSQUEDA DE PARENTESCO (Sección 2.1)
// ===========================================

function buscarParentesco(query) {
    if (query.length < 2) {
        document.getElementById('parentescoResults').style.display = 'none';
        return;
    }
    clearTimeout(parentescoTimeout);
    parentescoTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-parentesco") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.resultados.length > 0) {
                const resultsDiv = document.getElementById('parentescoResults');
                resultsDiv.innerHTML = '';
                resultadosParentescos = data.resultados;
                
                data.resultados.forEach((valor, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${valor}</strong>`;
                    div.addEventListener('click', () => seleccionarParentesco(valor));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedParentescoIndex = -1;
            } else {
                document.getElementById('parentescoResults').style.display = 'none';
            }
        });
    }, 300);
}

function seleccionarParentesco(valor) {
    document.getElementById('parentesco').value = valor;
    updateCounter(document.getElementById('parentesco'), 'parentescoCounter');
    document.getElementById('parentescoResults').style.display = 'none';
}

// Navegación con teclado para Parentesco
document.getElementById('parentesco')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('parentescoResults');
    if (resultsDiv.style.display !== 'block' || resultadosParentescos.length === 0) return;
    const items = document.querySelectorAll('#parentescoResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedParentescoIndex = (selectedParentescoIndex + 1) % items.length;
        updateHighlight(items, selectedParentescoIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedParentescoIndex = (selectedParentescoIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedParentescoIndex);
    } else if (e.key === 'Enter' && selectedParentescoIndex >= 0) {
        e.preventDefault();
        seleccionarParentesco(resultadosParentescos[selectedParentescoIndex]);
    }
});

// ===========================================
// BÚSQUEDA DE APELLIDOS DEL FAMILIAR
// ===========================================

function buscarApellidoFamiliar(tipo, query) {
    if (query.length < 2) {
        document.getElementById(tipo === 'apellido1' ? 'familiarApellido1Results' : 'familiarApellido2Results').style.display = 'none';
        return;
    }
    clearTimeout(familiarApellidoTimeout);
    familiarApellidoTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-apellido") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ tipo: tipo, busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.resultados.length > 0) {
                const resultsDiv = document.getElementById(tipo === 'apellido1' ? 'familiarApellido1Results' : 'familiarApellido2Results');
                resultsDiv.innerHTML = '';
                if (tipo === 'apellido1') resultadosFamiliarApellido1 = data.resultados;
                else resultadosFamiliarApellido2 = data.resultados;
                
                data.resultados.forEach((valor, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${valor}</strong>`;
                    div.addEventListener('click', () => seleccionarApellidoFamiliar(tipo, valor));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                if (tipo === 'apellido1') selectedFamiliarApellido1Index = -1;
                else selectedFamiliarApellido2Index = -1;
            } else {
                document.getElementById(tipo === 'apellido1' ? 'familiarApellido1Results' : 'familiarApellido2Results').style.display = 'none';
            }
        });
    }, 300);
}

function seleccionarApellidoFamiliar(tipo, valor) {
    const inputId = tipo === 'apellido1' ? 'familiar_apellido1' : 'familiar_apellido2';
    const counterId = tipo === 'apellido1' ? 'familiarApellido1Counter' : 'familiarApellido2Counter';
    
    document.getElementById(inputId).value = valor;
    updateCounter(document.getElementById(inputId), counterId);
    document.getElementById(tipo === 'apellido1' ? 'familiarApellido1Results' : 'familiarApellido2Results').style.display = 'none';
}

// Navegación con teclado para apellidos del familiar
document.getElementById('familiar_apellido1')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('familiarApellido1Results');
    if (resultsDiv.style.display !== 'block' || resultadosFamiliarApellido1.length === 0) return;
    const items = document.querySelectorAll('#familiarApellido1Results .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedFamiliarApellido1Index = (selectedFamiliarApellido1Index + 1) % items.length;
        updateHighlight(items, selectedFamiliarApellido1Index);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedFamiliarApellido1Index = (selectedFamiliarApellido1Index - 1 + items.length) % items.length;
        updateHighlight(items, selectedFamiliarApellido1Index);
    } else if (e.key === 'Enter' && selectedFamiliarApellido1Index >= 0) {
        e.preventDefault();
        seleccionarApellidoFamiliar('apellido1', resultadosFamiliarApellido1[selectedFamiliarApellido1Index]);
    }
});

document.getElementById('familiar_apellido2')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('familiarApellido2Results');
    if (resultsDiv.style.display !== 'block' || resultadosFamiliarApellido2.length === 0) return;
    const items = document.querySelectorAll('#familiarApellido2Results .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedFamiliarApellido2Index = (selectedFamiliarApellido2Index + 1) % items.length;
        updateHighlight(items, selectedFamiliarApellido2Index);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedFamiliarApellido2Index = (selectedFamiliarApellido2Index - 1 + items.length) % items.length;
        updateHighlight(items, selectedFamiliarApellido2Index);
    } else if (e.key === 'Enter' && selectedFamiliarApellido2Index >= 0) {
        e.preventDefault();
        seleccionarApellidoFamiliar('apellido2', resultadosFamiliarApellido2[selectedFamiliarApellido2Index]);
    }
});

// ===========================================
// BÚSQUEDA DE COLONIA DEL FAMILIAR (Domicilio diferente)
// ===========================================

function buscarColoniaFamiliar(query) {
    if (query.length < 2) {
        document.getElementById('familiarColoniaResults').style.display = 'none';
        return;
    }
    clearTimeout(familiarColoniaTimeout);
    familiarColoniaTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-domicilio") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.domicilios.length > 0) {
                const resultsDiv = document.getElementById('familiarColoniaResults');
                resultsDiv.innerHTML = '';
                resultadosFamiliarColonias = data.domicilios;
                data.domicilios.forEach((domicilio, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${domicilio.colonia}</strong> - ${domicilio.localidad}, ${domicilio.municipio}, ${domicilio.estado}`;
                    div.addEventListener('click', () => seleccionarColoniaFamiliar(domicilio));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedFamiliarColoniaIndex = -1;
            } else {
                document.getElementById('familiarColoniaResults').style.display = 'none';
            }
        });
    }, 300);
}

function seleccionarColoniaFamiliar(domicilio) {
    document.getElementById('familiar_colonia').value = domicilio.colonia;
    document.getElementById('familiar_localidad').value = domicilio.localidad;
    document.getElementById('familiar_municipio').value = domicilio.municipio;
    document.getElementById('familiar_cp').value = domicilio.cp;
    document.getElementById('familiar_estado').value = domicilio.estado;
    document.getElementById('familiar_domicilio_id').value = domicilio.id;
    
    updateCounter(document.getElementById('familiar_colonia'), 'familiar_coloniaCounter');
    updateCounter(document.getElementById('familiar_localidad'), 'familiar_localidadCounter');
    updateCounter(document.getElementById('familiar_municipio'), 'familiar_municipioCounter');
    updateCounter(document.getElementById('familiar_cp'), 'familiar_cpCounter');
    updateCounter(document.getElementById('familiar_estado'), 'familiar_estadoCounter');
    
    document.getElementById('familiarColoniaResults').style.display = 'none';
}

// Navegación con teclado para Colonia del familiar
document.getElementById('familiar_colonia')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('familiarColoniaResults');
    if (resultsDiv.style.display !== 'block' || resultadosFamiliarColonias.length === 0) return;
    const items = document.querySelectorAll('#familiarColoniaResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedFamiliarColoniaIndex = (selectedFamiliarColoniaIndex + 1) % items.length;
        updateHighlight(items, selectedFamiliarColoniaIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedFamiliarColoniaIndex = (selectedFamiliarColoniaIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedFamiliarColoniaIndex);
    } else if (e.key === 'Enter' && selectedFamiliarColoniaIndex >= 0) {
        e.preventDefault();
        seleccionarColoniaFamiliar(resultadosFamiliarColonias[selectedFamiliarColoniaIndex]);
    }
});

// ===========================================
// BÚSQUEDA DE ESTADO CIVIL (Padres)
// ===========================================

function buscarEstadoCivil(query) {
    if (query.length < 2) {
        document.getElementById('estadoCivilResults').style.display = 'none';
        return;
    }
    clearTimeout(estadoCivilTimeout);
    estadoCivilTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-estado-civil") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.resultados.length > 0) {
                const resultsDiv = document.getElementById('estadoCivilResults');
                resultsDiv.innerHTML = '';
                resultadosEstadoCivil = data.resultados;
                
                data.resultados.forEach((valor, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${valor}</strong>`;
                    div.addEventListener('click', () => seleccionarEstadoCivil(valor));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedEstadoCivilIndex = -1;
            } else {
                document.getElementById('estadoCivilResults').style.display = 'none';
            }
        });
    }, 300);
}

function seleccionarEstadoCivil(valor) {
    document.getElementById('estado_civil_padres').value = valor;
    updateCounter(document.getElementById('estado_civil_padres'), 'estadoCivilCounter');
    document.getElementById('estadoCivilResults').style.display = 'none';
}

// ===========================================
// BÚSQUEDA DE ESTADO CIVIL (Alumno)
// ===========================================

function buscarEstadoCivilAlumno(query) {
    if (query.length < 2) {
        document.getElementById('estadoCivilAlumnoResults').style.display = 'none';
        return;
    }
    clearTimeout(estadoCivilAlumnoTimeout);
    estadoCivilAlumnoTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-estado-civil-alumno") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.resultados.length > 0) {
                const resultsDiv = document.getElementById('estadoCivilAlumnoResults');
                resultsDiv.innerHTML = '';
                resultadosEstadoCivilAlumno = data.resultados;
                
                data.resultados.forEach((valor, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${valor}</strong>`;
                    div.addEventListener('click', () => seleccionarEstadoCivilAlumno(valor));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedEstadoCivilAlumnoIndex = -1;
            } else {
                document.getElementById('estadoCivilAlumnoResults').style.display = 'none';
            }
        });
    }, 300);
}

function seleccionarEstadoCivilAlumno(valor) {
    document.getElementById('estado_civil_alumno').value = valor;
    updateCounter(document.getElementById('estado_civil_alumno'), 'estadoCivilAlumnoCounter');
    document.getElementById('estadoCivilAlumnoResults').style.display = 'none';
}

// ===========================================
// BÚSQUEDA DE TRANSPORTES
// ===========================================

function buscarTransportes(query) {
    const btnAgregar = document.getElementById('btnAgregarTransporte');
    
    if (query.length < 2) {
        btnAgregar.disabled = true;
        document.getElementById('transporteResults').style.display = 'none';
        return;
    }
    
    btnAgregar.disabled = false;
    clearTimeout(transporteTimeout);
    transporteTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-transportes") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.resultados.length > 0) {
                const resultsDiv = document.getElementById('transporteResults');
                resultsDiv.innerHTML = '';
                resultadosTransportes = data.resultados;
                
                data.resultados.forEach((valor, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${valor}</strong>`;
                    div.addEventListener('click', () => seleccionarTransporte(valor));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedTransporteIndex = -1;
            } else {
                document.getElementById('transporteResults').style.display = 'none';
            }
        });
    }, 300);
}

function seleccionarTransporte(valor) {
    document.getElementById('buscarTransporte').value = valor;
    document.getElementById('btnAgregarTransporte').disabled = false;
    document.getElementById('transporteResults').style.display = 'none';
}

// ===========================================
// BÚSQUEDA DE DEPORTES
// ===========================================

function buscarDeportes(query) {
    const btnAgregar = document.getElementById('btnAgregarDeporte');
    
    if (query.length < 2) {
        btnAgregar.disabled = true;
        document.getElementById('deporteResults').style.display = 'none';
        return;
    }
    
    btnAgregar.disabled = false;
    clearTimeout(deporteTimeout);
    deporteTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-deportes") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.resultados.length > 0) {
                const resultsDiv = document.getElementById('deporteResults');
                resultsDiv.innerHTML = '';
                resultadosDeportes = data.resultados;
                
                data.resultados.forEach((valor, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${valor}</strong>`;
                    div.addEventListener('click', () => seleccionarDeporte(valor));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedDeporteIndex = -1;
            } else {
                document.getElementById('deporteResults').style.display = 'none';
            }
        });
    }, 300);
}

function seleccionarDeporte(valor) {
    document.getElementById('buscarDeporte').value = valor;
    document.getElementById('btnAgregarDeporte').disabled = false;
    document.getElementById('deporteResults').style.display = 'none';
}

// ===========================================
// NAVEGACIÓN CON TECLADO PARA RESULTADOS
// ===========================================

// Para apellido1
document.getElementById('alumno_apellido1')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('apellido1Results');
    if (resultsDiv.style.display !== 'block' || resultadosApellido1.length === 0) return;
    const items = document.querySelectorAll('#apellido1Results .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedApellido1Index = (selectedApellido1Index + 1) % items.length;
        updateHighlight(items, selectedApellido1Index);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedApellido1Index = (selectedApellido1Index - 1 + items.length) % items.length;
        updateHighlight(items, selectedApellido1Index);
    } else if (e.key === 'Enter' && selectedApellido1Index >= 0) {
        e.preventDefault();
        seleccionarApellido('apellido1', resultadosApellido1[selectedApellido1Index]);
    }
});

// Para apellido2
document.getElementById('alumno_apellido2')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('apellido2Results');
    if (resultsDiv.style.display !== 'block' || resultadosApellido2.length === 0) return;
    const items = document.querySelectorAll('#apellido2Results .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedApellido2Index = (selectedApellido2Index + 1) % items.length;
        updateHighlight(items, selectedApellido2Index);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedApellido2Index = (selectedApellido2Index - 1 + items.length) % items.length;
        updateHighlight(items, selectedApellido2Index);
    } else if (e.key === 'Enter' && selectedApellido2Index >= 0) {
        e.preventDefault();
        seleccionarApellido('apellido2', resultadosApellido2[selectedApellido2Index]);
    }
});

// Navegación con teclado para Localidad
document.getElementById('localidad')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('localidadResults');
    if (resultsDiv.style.display !== 'block' || resultadosLugares.length === 0) return;
    const items = document.querySelectorAll('#localidadResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedIndex = (selectedIndex + 1) % items.length;
        updateHighlight(items, selectedIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex = (selectedIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedIndex);
    } else if (e.key === 'Enter' && selectedIndex >= 0) {
        e.preventDefault();
        seleccionarLugar(resultadosLugares[selectedIndex]);
    }
});

// Navegación con teclado para Colonia
document.getElementById('colonia')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('coloniaResults');
    if (resultsDiv.style.display !== 'block' || resultadosDomicilios.length === 0) return;
    const items = document.querySelectorAll('#coloniaResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedDomicilioIndex = (selectedDomicilioIndex + 1) % items.length;
        updateHighlight(items, selectedDomicilioIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedDomicilioIndex = (selectedDomicilioIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedDomicilioIndex);
    } else if (e.key === 'Enter' && selectedDomicilioIndex >= 0) {
        e.preventDefault();
        seleccionarDomicilio(resultadosDomicilios[selectedDomicilioIndex]);
    }
});

// Navegación con teclado para Secundaria
document.getElementById('secundaria_nombre')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('secundariaResults');
    if (resultsDiv.style.display !== 'block' || resultadosSecundarias.length === 0) return;
    const items = document.querySelectorAll('#secundariaResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedSecundariaIndex = (selectedSecundariaIndex + 1) % items.length;
        updateHighlight(items, selectedSecundariaIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedSecundariaIndex = (selectedSecundariaIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedSecundariaIndex);
    } else if (e.key === 'Enter' && selectedSecundariaIndex >= 0) {
        e.preventDefault();
        seleccionarSecundaria(resultadosSecundarias[selectedSecundariaIndex]);
    }
});

// Para grupo
document.getElementById('buscar_grupo')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('grupoResults');
    if (resultsDiv.style.display !== 'block' || resultadosGrupos.length === 0) return;
    const items = document.querySelectorAll('#grupoResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedGrupoIndex = (selectedGrupoIndex + 1) % items.length;
        updateHighlight(items, selectedGrupoIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedGrupoIndex = (selectedGrupoIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedGrupoIndex);
    } else if (e.key === 'Enter' && selectedGrupoIndex >= 0) {
        e.preventDefault();
        seleccionarGrupo(resultadosGrupos[selectedGrupoIndex]);
    }
});

// Para estado civil padres
document.getElementById('estado_civil_padres')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('estadoCivilResults');
    if (resultsDiv.style.display !== 'block' || resultadosEstadoCivil.length === 0) return;
    const items = document.querySelectorAll('#estadoCivilResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedEstadoCivilIndex = (selectedEstadoCivilIndex + 1) % items.length;
        updateHighlight(items, selectedEstadoCivilIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedEstadoCivilIndex = (selectedEstadoCivilIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedEstadoCivilIndex);
    } else if (e.key === 'Enter' && selectedEstadoCivilIndex >= 0) {
        e.preventDefault();
        seleccionarEstadoCivil(resultadosEstadoCivil[selectedEstadoCivilIndex]);
    }
});

// Para estado civil alumno
document.getElementById('estado_civil_alumno')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('estadoCivilAlumnoResults');
    if (resultsDiv.style.display !== 'block' || resultadosEstadoCivilAlumno.length === 0) return;
    const items = document.querySelectorAll('#estadoCivilAlumnoResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedEstadoCivilAlumnoIndex = (selectedEstadoCivilAlumnoIndex + 1) % items.length;
        updateHighlight(items, selectedEstadoCivilAlumnoIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedEstadoCivilAlumnoIndex = (selectedEstadoCivilAlumnoIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedEstadoCivilAlumnoIndex);
    } else if (e.key === 'Enter' && selectedEstadoCivilAlumnoIndex >= 0) {
        e.preventDefault();
        seleccionarEstadoCivilAlumno(resultadosEstadoCivilAlumno[selectedEstadoCivilAlumnoIndex]);
    }
});

// Para transporte
document.getElementById('buscarTransporte')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('transporteResults');
    if (resultsDiv.style.display !== 'block' || resultadosTransportes.length === 0) return;
    const items = document.querySelectorAll('#transporteResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedTransporteIndex = (selectedTransporteIndex + 1) % items.length;
        updateHighlight(items, selectedTransporteIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedTransporteIndex = (selectedTransporteIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedTransporteIndex);
    } else if (e.key === 'Enter' && selectedTransporteIndex >= 0) {
        e.preventDefault();
        seleccionarTransporte(resultadosTransportes[selectedTransporteIndex]);
    }
});

// Para deporte
document.getElementById('buscarDeporte')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('deporteResults');
    if (resultsDiv.style.display !== 'block' || resultadosDeportes.length === 0) return;
    const items = document.querySelectorAll('#deporteResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedDeporteIndex = (selectedDeporteIndex + 1) % items.length;
        updateHighlight(items, selectedDeporteIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedDeporteIndex = (selectedDeporteIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedDeporteIndex);
    } else if (e.key === 'Enter' && selectedDeporteIndex >= 0) {
        e.preventDefault();
        seleccionarDeporte(resultadosDeportes[selectedDeporteIndex]);
    }
});

// ===========================================
// EVENTOS PARA AGREGAR TRANSPORTE Y DEPORTE
// ===========================================

// Agregar transporte
document.getElementById('btnAgregarTransporte')?.addEventListener('click', function() {
    const transporteNombre = document.getElementById('buscarTransporte').value.trim();
    if (!transporteNombre) {
        alert('Ingrese un transporte.');
        return;
    }
    
    const transporteNormalizado = transporteNombre.charAt(0).toUpperCase() + transporteNombre.slice(1).toLowerCase();
    
    if (transportesTemporales.some(t => t.toLowerCase() === transporteNormalizado.toLowerCase())) {
        alert('Este transporte ya está añadido.');
        return;
    }
    
    transportesTemporales.push(transporteNormalizado);
    renderizarTransportesAsignados();
    document.getElementById('buscarTransporte').value = '';
    document.getElementById('btnAgregarTransporte').disabled = true;
});

// Agregar deporte
document.getElementById('btnAgregarDeporte')?.addEventListener('click', function() {
    const deporteNombre = document.getElementById('buscarDeporte').value.trim();
    if (!deporteNombre) {
        alert('Ingrese un deporte.');
        return;
    }
    
    const deporteNormalizado = deporteNombre.charAt(0).toUpperCase() + deporteNombre.slice(1).toLowerCase();
    
    if (deportesTemporales.some(d => d.toLowerCase() === deporteNormalizado.toLowerCase())) {
        alert('Este deporte ya está añadido.');
        return;
    }
    
    deportesTemporales.push(deporteNormalizado);
    renderizarDeportesAsignados();
    document.getElementById('buscarDeporte').value = '';
    document.getElementById('btnAgregarDeporte').disabled = true;
});

// Permitir agregar con Enter
document.getElementById('buscarTransporte')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const btn = document.getElementById('btnAgregarTransporte');
        if (!btn.disabled && this.value.trim()) btn.click();
    }
});

document.getElementById('buscarDeporte')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const btn = document.getElementById('btnAgregarDeporte');
        if (!btn.disabled && this.value.trim()) btn.click();
    }
});

// ===========================================
// FUNCIONES PARA TOGGLES CONDICIONALES (RADIOS)
// ===========================================

// Configura un toggle simple (muestra/oculta y limpia campos)
function setupConditionalToggle(radioName, targetId, fieldsToClear = []) {
    const radios = document.querySelectorAll(`input[name="${radioName}"]`);
    const target = document.getElementById(targetId);
    
    if (!radios.length || !target) return;
    
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === '1' || this.value === 'si') {
                // Mostrar el panel
                target.style.display = 'block';
            } else {
                // Ocultar el panel y limpiar los campos
                target.style.display = 'none';
                fieldsToClear.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        if (field.tagName === 'INPUT' && field.type === 'checkbox') {
                            field.checked = false;
                        } else if (field.tagName === 'INPUT' || field.tagName === 'TEXTAREA') {
                            field.value = '';
                        } else if (field.tagName === 'SELECT') {
                            field.value = '';
                        }
                        // Actualizar contador si existe
                        const counterId = fieldId + 'Counter';
                        const counter = document.getElementById(counterId);
                        if (counter) counter.textContent = '0';
                    }
                });
            }
        });
    });
}

// Configurar todos los toggles
function setupAllConditionalToggles() {
    // Trabajo del alumno
    setupConditionalToggle('tiene_trabajo', 'panelTrabajo', 
        ['lugar_trabajo', 'horario_laboral', 'domicilio_trabajo', 'telefono_trabajo']);
    
    // Hijos del alumno
    setupConditionalToggle('tiene_hijos', 'panelHijos', 
        ['num_hijos', 'edades_hijos']);
    
    // Apoyo económico
    setupConditionalToggle('recibe_apoyo', 'panelMontoApoyo', 
        ['monto_apoyo']);
    
    // Problemas de aprendizaje
    setupConditionalToggle('tiene_problema', 'panelProblemas', 
        ['otro_problema']);
    
    // Anteojos (este es especial porque tiene un campo adicional)
    const anteojosRadios = document.querySelectorAll('input[name="usa_anteojos"]');
    const graduacionContainer = document.getElementById('graduacionContainer');
    const graduacionInput = document.getElementById('graduacion_anteojos');
    
    if (anteojosRadios.length && graduacionContainer) {
        anteojosRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === '1') {
                    graduacionContainer.style.display = 'block';
                } else {
                    graduacionContainer.style.display = 'none';
                    if (graduacionInput) graduacionInput.value = '';
                }
            });
        });
    }
    
    // Síntomas de salud mental
    setupConditionalToggle('tiene_sintomas', 'panelSintomas', 
        ['otro_sintoma']);
    
    // Medicamento controlado
    setupConditionalToggle('tiene_medicamento', 'medicamentoContainer', 
        ['medicamento_controlado']);
    
    // Alergia a medicamento
    setupConditionalToggle('tiene_alergia_med', 'alergiaMedContainer', 
        ['alergia_medicamento']);
    
    // Hospitalización
    setupConditionalToggle('tiene_hospitalizacion', 'hospitalizacionContainer', 
        ['motivo_hospitalizacion']);
}

// Función específica para los checkboxes de problemas de aprendizaje y síntomas
function setupCheckboxGroups() {
    // Para problemas de aprendizaje - limpiar checkboxes cuando se selecciona "No"
    const problemaNo = document.getElementById('problemaNo');
    if (problemaNo) {
        problemaNo.addEventListener('change', function() {
            if (this.checked) {
                // Limpiar todos los checkboxes de características
                const caracteristicasIds = [
                    'problemaDislexia', 'problemaVisuales', 'problemaAuditivo', 'problemaNoEntender',
                    'problemaLentitud', 'problemaLenguaje', 'problemaComunicacion', 'problemaDiscriminacion',
                    'problemaGestos', 'problemaDistraccion', 'problemaTDAH', 'problemaDiscapacidad'
                ];
                caracteristicasIds.forEach(id => {
                    const cb = document.getElementById(id);
                    if (cb) cb.checked = false;
                });
            }
        });
    }
    
    // Para síntomas de salud mental - limpiar checkboxes cuando se selecciona "No"
    const sintomasNo = document.getElementById('sintomasNo');
    if (sintomasNo) {
        sintomasNo.addEventListener('change', function() {
            if (this.checked) {
                const sintomasIds = ['sintomaAnsiedad', 'sintomaEstres', 'sintomaDepresion', 'sintomaCulpa', 'sintomaTristeza'];
                sintomasIds.forEach(id => {
                    const cb = document.getElementById(id);
                    if (cb) cb.checked = false;
                });
            }
        });
    }
}

// ===========================================
// TOGGLE PARA DOMICILIO DEL FAMILIAR
// ===========================================

function setupFamiliarDomicilioToggle() {
    const comparteSiRadio = document.getElementById('comparteSi');
    const comparteNoRadio = document.getElementById('comparteNo');
    const panelDomicilioDiferente = document.getElementById('panelDomicilioDiferente');
    
    if (!comparteSiRadio || !comparteNoRadio || !panelDomicilioDiferente) return;
    
    function toggleDomicilioPanel() {
        if (comparteNoRadio.checked) {
            panelDomicilioDiferente.style.display = 'block';
        } else {
            panelDomicilioDiferente.style.display = 'none';
            limpiarDomicilioFamiliar();
        }
    }
    
    comparteSiRadio.addEventListener('change', toggleDomicilioPanel);
    comparteNoRadio.addEventListener('change', toggleDomicilioPanel);
    
    // Estado inicial
    toggleDomicilioPanel();
}

// ===========================================
// TOGGLE PARA CONTACTO DE EMERGENCIA
// ===========================================

function setupFamiliarContactoToggle() {
    const contactoSiRadio = document.getElementById('contactoSi');
    const contactoNoRadio = document.getElementById('contactoNo');
    const prioridadContainer = document.getElementById('prioridadContainer');
    const prioridadInput = document.getElementById('familiar_prioridad');
    const tutorSiRadio = document.getElementById('tutorSi');
    const tutorNoRadio = document.getElementById('tutorNo');
    
    if (!contactoSiRadio || !contactoNoRadio || !prioridadContainer) return;
    
    function togglePrioridadPanel() {
        const isEditing = document.getElementById('toggle_edit_mode')?.checked || false;
        const puedeEditar = @json($puedeEditar);
        const puedeEditarActual = puedeEditar && isEditing;
        
        if (contactoSiRadio.checked) {
            prioridadContainer.style.display = 'block';
            if (prioridadInput) {
                prioridadInput.required = true;
                prioridadInput.min = 1;
                // Aplicar estado de edición al prioridadInput
                if (puedeEditarActual) {
                    prioridadInput.removeAttribute('readonly');
                    prioridadInput.disabled = false;
                } else {
                    prioridadInput.setAttribute('readonly', true);
                    prioridadInput.disabled = true;
                }
            }
            // Un contacto puede ser tutor o no - habilitar ambos radios según modo edición
            if (tutorSiRadio) {
                tutorSiRadio.disabled = !puedeEditarActual;
            }
            if (tutorNoRadio) {
                tutorNoRadio.disabled = !puedeEditarActual;
            }
        } else {
            prioridadContainer.style.display = 'none';
            if (prioridadInput) {
                prioridadInput.value = '';
                prioridadInput.required = false;
                prioridadInput.disabled = true;
            }
            // Si no es contacto, no puede ser tutor
            if (tutorSiRadio) {
                tutorSiRadio.checked = false;
                tutorSiRadio.disabled = true;
            }
            if (tutorNoRadio) {
                tutorNoRadio.checked = true;
                tutorNoRadio.disabled = !puedeEditarActual;
            }
        }
        
        // Aplicar el estado de edición a los radios de contacto
        if (contactoSiRadio) contactoSiRadio.disabled = !puedeEditarActual;
        if (contactoNoRadio) contactoNoRadio.disabled = !puedeEditarActual;
    }
    
    contactoSiRadio.addEventListener('change', togglePrioridadPanel);
    contactoNoRadio.addEventListener('change', togglePrioridadPanel);
    
    // Estado inicial
    togglePrioridadPanel();
}

// ===========================================
// TOGGLE PARA VIVE DEL FAMILIAR
// ===========================================

function setupFamiliarViveToggle() {
    const viveSiRadio = document.getElementById('familiarViveSi');
    const viveNoRadio = document.getElementById('familiarViveNo');
    const comparteDomicilioSection = document.querySelector('input[name="comparte_domicilio"]')?.closest('.row');
    const comparteSiRadio = document.getElementById('comparteSi');
    const comparteNoRadio = document.getElementById('comparteNo');
    const panelDomicilioDiferente = document.getElementById('panelDomicilioDiferente');
    
    if (!viveSiRadio || !viveNoRadio) return;
    
    function toggleViveSection() {
        if (viveNoRadio.checked) {
            // No vive - ocultar toda la sección de domicilio
            if (comparteDomicilioSection) comparteDomicilioSection.style.display = 'none';
            if (panelDomicilioDiferente) panelDomicilioDiferente.style.display = 'none';
            
            // Desmarcar radios
            if (comparteSiRadio) comparteSiRadio.checked = false;
            if (comparteNoRadio) comparteNoRadio.checked = false;
            
            limpiarDomicilioFamiliar();
        } else {
            // Vive - mostrar sección
            if (comparteDomicilioSection) comparteDomicilioSection.style.display = 'flex';
            if (comparteSiRadio) comparteSiRadio.checked = true;
            if (comparteNoRadio) comparteNoRadio.checked = false;
            if (panelDomicilioDiferente) panelDomicilioDiferente.style.display = 'none';
        }
    }
    
    viveSiRadio.addEventListener('change', toggleViveSection);
    viveNoRadio.addEventListener('change', toggleViveSection);
    
    // Estado inicial
    toggleViveSection();
}

function setupChatToggle() {
    const chatearSiRadio = document.getElementById('chatearSi');
    const chatearNoRadio = document.getElementById('chatearNo');
    const temasChatContainer = document.getElementById('temasChatContainer');
    
    if (!chatearSiRadio || !chatearNoRadio || !temasChatContainer) return;
    
    function toggleChatPanel() {
        if (chatearSiRadio.checked) {
            temasChatContainer.style.display = 'block';
        } else {
            temasChatContainer.style.display = 'none';
            document.getElementById('temas_chat').value = '';
            const temasChatCounter = document.getElementById('temasChatCounter');
            if (temasChatCounter) temasChatCounter.textContent = '0';
        }
    }
    
    chatearSiRadio.addEventListener('change', toggleChatPanel);
    chatearNoRadio.addEventListener('change', toggleChatPanel);
    
    // Estado inicial
    toggleChatPanel();
}

// ===========================================
// FRECUENCIAS DE MÉDICO Y DENTISTA
// ===========================================

function setupFrecuencias() {
    setupFrecuenciaToggle('medico');
    setupFrecuenciaToggle('dentista');
}

function setupFrecuenciaToggle(prefix) {
    const aniosSelect = document.getElementById(`${prefix}_anios`);
    const mesesSelect = document.getElementById(`${prefix}_meses`);
    const opcionNunca = document.getElementById(`${prefix}Nunca`);
    const opcionSoloEnfermo = document.getElementById(`${prefix}SoloEnfermo`);
    const hiddenInput = document.getElementById(`frecuencia_${prefix}`);
    
    if (!aniosSelect || !mesesSelect || !hiddenInput) return;
    
    function actualizarHidden() {
        if (opcionNunca && opcionNunca.checked) {
            hiddenInput.value = 0;
        } else if (opcionSoloEnfermo && opcionSoloEnfermo.checked) {
            hiddenInput.value = -1;
        } else {
            const anios = parseInt(aniosSelect.value) || 0;
            const meses = parseInt(mesesSelect.value) || 0;
            hiddenInput.value = anios + meses;
        }
    }
    
    // Eventos para selects
    aniosSelect.addEventListener('change', function() {
        if (opcionNunca) opcionNunca.checked = false;
        if (opcionSoloEnfermo) opcionSoloEnfermo.checked = false;
        actualizarHidden();
    });
    
    mesesSelect.addEventListener('change', function() {
        if (opcionNunca) opcionNunca.checked = false;
        if (opcionSoloEnfermo) opcionSoloEnfermo.checked = false;
        actualizarHidden();
    });
    
    // Eventos para radios
    if (opcionNunca) {
        opcionNunca.addEventListener('change', function() {
            if (this.checked) {
                aniosSelect.value = '';
                mesesSelect.value = '';
                actualizarHidden();
            }
        });
    }
    
    if (opcionSoloEnfermo) {
        opcionSoloEnfermo.addEventListener('change', function() {
            if (this.checked) {
                aniosSelect.value = '';
                mesesSelect.value = '';
                actualizarHidden();
            }
        });
    }
    
    // Inicializar
    actualizarHidden();
}

// ===========================================
// BÚSQUEDA DE BECAS
// ===========================================

function buscarBecas(query) {
    const btnAgregar = document.getElementById('btnAgregarBeca');
    
    if (query.length < 2) {
        btnAgregar.disabled = true;
        document.getElementById('becaResults').style.display = 'none';
        return;
    }
    
    btnAgregar.disabled = false;
    clearTimeout(becaTimeout);
    becaTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-becas") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.becas && data.becas.length > 0) {
                const resultsDiv = document.getElementById('becaResults');
                resultsDiv.innerHTML = '';
                resultadosBecas = data.becas;
                
                data.becas.forEach((beca, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${beca.tipo_beca}</strong>`;
                    div.addEventListener('click', () => seleccionarBeca(beca));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedBecaIndex = -1;
                btnAgregar.disabled = false;
            } else {
                document.getElementById('becaResults').style.display = 'none';
                btnAgregar.disabled = false;
            }
        });
    }, 300);
}

function seleccionarBeca(beca) {
    document.getElementById('buscarBeca').value = beca.tipo_beca;
    document.getElementById('becaSeleccionadaId').value = beca.id;
    document.getElementById('btnAgregarBeca').disabled = false;
    document.getElementById('becaResults').style.display = 'none';
}

// Navegación con teclado para becas
document.getElementById('buscarBeca')?.addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('becaResults');
    if (resultsDiv.style.display !== 'block' || resultadosBecas.length === 0) return;
    const items = document.querySelectorAll('#becaResults .search-result-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedBecaIndex = (selectedBecaIndex + 1) % items.length;
        updateHighlight(items, selectedBecaIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedBecaIndex = (selectedBecaIndex - 1 + items.length) % items.length;
        updateHighlight(items, selectedBecaIndex);
    } else if (e.key === 'Enter' && selectedBecaIndex >= 0) {
        e.preventDefault();
        seleccionarBeca(resultadosBecas[selectedBecaIndex]);
    } else if (e.key === 'Enter' && selectedBecaIndex === -1 && this.value.trim()) {
        e.preventDefault();
        document.getElementById('btnAgregarBeca').disabled = false;
    }
});

// ===========================================
// AGREGAR NUEVA BECA
// ===========================================

document.getElementById('btnAgregarBeca')?.addEventListener('click', async function() {
    const becaId = document.getElementById('becaSeleccionadaId').value;
    const becaNombre = document.getElementById('buscarBeca').value.trim();
    const estatus = document.getElementById('estatusBecaSelect').value;
    
    if (!becaNombre) {
        alert('Ingrese el nombre de la beca.');
        return;
    }
    
    // Si estamos editando una beca existente
    if (becaEnEdicion) {
        if (!confirm(`¿Desea actualizar la beca "${becaEnEdicion.nombre}" con los nuevos datos?`)) {
            cancelarEdicionBeca();
            return;
        }
        
        // Actualizar la beca existente
        const index = becasTemporales.findIndex(b => b.id === becaEnEdicion.id);
        if (index !== -1) {
            becasTemporales[index] = {
                id: becaEnEdicion.id,
                nombre: becaNombre,
                activa: estatus === '1'
            };
            renderizarBecasAsignadas();
            alert('Beca actualizada correctamente. Presione "Guardar Todos los Cambios" para guardar permanentemente.');
        }
        cancelarEdicionBeca();
        return;
    }
    
    // Verificar si ya existe la beca en la lista temporal
    if (becasTemporales.some(b => b.nombre.toLowerCase() === becaNombre.toLowerCase())) {
        alert('Esta beca ya está asignada.');
        return;
    }
    
    // Agregar nueva beca temporal (solo localmente)
    const becaParaAgregar = {
        id: becaId || 'new_' + Date.now(),
        nombre: becaNombre,
        activa: estatus === '1'
    };
    
    // Agregar localmente
    becasTemporales.push(becaParaAgregar);
    renderizarBecasAsignadas();
    
    // Limpiar campos
    document.getElementById('buscarBeca').value = '';
    document.getElementById('becaSeleccionadaId').value = '';
    document.getElementById('btnAgregarBeca').disabled = true;
    document.getElementById('estatusBecaSelect').value = '1';
    
    alert('Beca añadida correctamente. Presione "Guardar Todos los Cambios" para guardar permanentemente.');
});

// ===========================================
// ELIMINAR BECA TEMPORAL (MEJORADA)
// ===========================================

window.eliminarBecaTemporal = async function(id) {
    // Verificar si es una beca nueva (con id string que comienza con 'new_')
    if (typeof id === 'string' && id.startsWith('new_')) {
        becasTemporales = becasTemporales.filter(b => b.id !== id);
        renderizarBecasAsignadas();
        
        if (becasTemporales.length === 0) {
            document.getElementById('becaNo').checked = true;
            document.getElementById('panelBecas').style.display = 'none';
        }
        return;
    }
    
    // Para becas existentes
    if (confirm('¿Deseas eliminar esta beca? Los cambios se guardarán cuando presiones "Guardar Todos los Cambios".')) {
        becasTemporales = becasTemporales.filter(b => b.id !== id);
        renderizarBecasAsignadas();
        
        if (becasTemporales.length === 0) {
            document.getElementById('becaNo').checked = true;
            document.getElementById('panelBecas').style.display = 'none';
        }
    }
};

// ===========================================
// Botón cancelar edición de beca
// ===========================================

document.getElementById('btnCancelarEdicionBeca')?.addEventListener('click', function() {
    cancelarEdicionBeca();
});

// Agregar o actualizar familiar
document.getElementById('btnAgregarFamiliar')?.addEventListener('click', async function() {
    if (!validarFamiliarAntesDeAgregar()) return;
    
    const fecha = obtenerFechaFamiliar();
    if (!fecha) return;
    
    const familiarVive = document.querySelector('input[name="familiar_vive"]:checked')?.value === '1';
    let comparteDomicilio = false, id_domicilio = null, domicilioData = null;
    
    if (familiarVive) {
        comparteDomicilio = document.querySelector('input[name="comparte_domicilio"]:checked')?.value === '1';
        const domicilioIdGuardado = document.getElementById('domicilioId').value;
        
        if (comparteDomicilio) {
            if (domicilioIdGuardado && domicilioIdGuardado !== '') {
                id_domicilio = domicilioIdGuardado;
            } else {
                domicilioData = {
                    calle: document.getElementById('calle').value,
                    num_ext: document.getElementById('num_ext').value,
                    num_int: document.getElementById('num_int').value || null,
                    colonia: document.getElementById('colonia').value,
                    localidad: document.getElementById('domicilio_localidad').value,
                    municipio: document.getElementById('domicilio_municipio').value,
                    cp: document.getElementById('cp').value,
                    estado: document.getElementById('domicilio_estado').value,
                    telefono_domicilio: document.getElementById('telefono_domicilio').value || null
                };
                if (!domicilioData.calle || !domicilioData.num_ext || !domicilioData.colonia || 
                    !domicilioData.localidad || !domicilioData.municipio || !domicilioData.cp || !domicilioData.estado) {
                    alert('Complete todos los campos del domicilio del alumno antes de añadir un familiar que comparte domicilio.');
                    return;
                }
            }
        } else {
            domicilioData = {
                calle: document.getElementById('familiar_calle').value,
                num_ext: document.getElementById('familiar_num_ext').value,
                num_int: document.getElementById('familiar_num_int').value || null,
                colonia: document.getElementById('familiar_colonia').value,
                localidad: document.getElementById('familiar_localidad').value,
                municipio: document.getElementById('familiar_municipio').value,
                cp: document.getElementById('familiar_cp').value,
                estado: document.getElementById('familiar_estado').value,
                telefono_domicilio: document.getElementById('familiar_telefono_domicilio').value || null
            };
            if (!domicilioData.calle || !domicilioData.num_ext || !domicilioData.colonia || 
                !domicilioData.localidad || !domicilioData.municipio || !domicilioData.cp || !domicilioData.estado) {
                alert('Complete todos los campos del domicilio.');
                return;
            }
        }
    }
    
    const esContacto = document.querySelector('input[name="familiar_contacto"]:checked')?.value === '1';
    const prioridad = parseInt(document.getElementById('familiar_prioridad').value) || 0;
    
    if (!esContacto && prioridad > 0) {
        alert('Si no autoriza como contacto de emergencia, la prioridad debe ser 0.');
        return;
    }
    
    if (esContacto && prioridad <= 0) {
        alert('Si autoriza como contacto de emergencia, debe asignar una prioridad mayor a 0.');
        document.getElementById('familiar_prioridad').focus();
        return;
    }
    
    const esTutor = document.querySelector('input[name="familiar_tutor"]:checked')?.value === '1';
    if (!esContacto && esTutor) {
        alert('Un familiar que no es contacto de emergencia no puede ser tutor.');
        document.getElementById('tutorNo').checked = true;
        return;
    }
    
    if (esContacto && (prioridad <= 0 || isNaN(prioridad))) {
        alert('Debe asignar una prioridad válida (número mayor a 0).');
        document.getElementById('familiar_prioridad').focus();
        return;
    }
    
    const familiarData = {
        vive: familiarVive,
        nombre: document.getElementById('familiar_nombre').value,
        apellido1: document.getElementById('familiar_apellido1').value,
        apellido2: document.getElementById('familiar_apellido2').value || null,
        fecha_nacimiento: fecha,
        telefono_celular: document.getElementById('familiar_telefono').value || null,
        id_domicilio: id_domicilio,
        escolaridad: document.getElementById('familiar_escolaridad').value,
        ocupacion: document.getElementById('familiar_ocupacion').value || null,
        lugar_trabajo: document.getElementById('familiar_lugar_trabajo').value || null,
        horario_laboral: document.getElementById('familiar_horario_laboral').value || null,
        domicilio_trabajo: document.getElementById('familiar_domicilio_trabajo').value || null,
        telefono_trabajo: document.getElementById('familiar_telefono_trabajo').value || null,
        domicilio: domicilioData
    };
    
    const nuevaPrioridad = prioridad;
    
    const nuevoFamiliar = {
        temp_id: Date.now(),
        familiar: familiarData,
        relacion: {
            parentesco: document.getElementById('parentesco').value,
            tutor: esTutor,
            contacto_emergencia: nuevaPrioridad,
            comparte_domicilio: comparteDomicilio
        }
    };
    
    familiaresTemporales.push(nuevoFamiliar);
    
    if (nuevaPrioridad > 0) {
        reordenarPrioridadesContactos(nuevaPrioridad);
    } else {
        reordenarPrioridadesContactos();
    }
    
    renderizarFamiliaresAsignados();
    limpiarFormularioFamiliar();
    
    // Resetear estado de edición
    modoEdicionFamiliarActivo = false;
    familiarEnEdicion = null;
    familiarEnEdicionIndex = null;
    actualizarBotonesFamiliares();
    
    alert('Familiar añadido correctamente en caché. Presione "Guardar Todos los Cambios" para guardar permanentemente.');
});

// ACTUALIZAR FAMILIAR EXISTENTE
document.getElementById('btnActualizarFamiliar')?.addEventListener('click', async function() {
    if (!validarFamiliarAntesDeAgregar()) return;
    
    const fecha = obtenerFechaFamiliar();
    if (!fecha) return;
    
    const familiarVive = document.querySelector('input[name="familiar_vive"]:checked')?.value === '1';
    let comparteDomicilio = false, id_domicilio = null, domicilioData = null;
    
    if (familiarVive) {
        comparteDomicilio = document.querySelector('input[name="comparte_domicilio"]:checked')?.value === '1';
        const domicilioIdGuardado = document.getElementById('domicilioId').value;
        
        if (comparteDomicilio) {
            if (domicilioIdGuardado && domicilioIdGuardado !== '') {
                id_domicilio = domicilioIdGuardado;
            } else {
                domicilioData = {
                    calle: document.getElementById('calle').value,
                    num_ext: document.getElementById('num_ext').value,
                    num_int: document.getElementById('num_int').value || null,
                    colonia: document.getElementById('colonia').value,
                    localidad: document.getElementById('domicilio_localidad').value,
                    municipio: document.getElementById('domicilio_municipio').value,
                    cp: document.getElementById('cp').value,
                    estado: document.getElementById('domicilio_estado').value,
                    telefono_domicilio: document.getElementById('telefono_domicilio').value || null
                };
                if (!domicilioData.calle || !domicilioData.num_ext || !domicilioData.colonia || 
                    !domicilioData.localidad || !domicilioData.municipio || !domicilioData.cp || !domicilioData.estado) {
                    alert('Complete todos los campos del domicilio del alumno.');
                    return;
                }
            }
        } else {
            domicilioData = {
                calle: document.getElementById('familiar_calle').value,
                num_ext: document.getElementById('familiar_num_ext').value,
                num_int: document.getElementById('familiar_num_int').value || null,
                colonia: document.getElementById('familiar_colonia').value,
                localidad: document.getElementById('familiar_localidad').value,
                municipio: document.getElementById('familiar_municipio').value,
                cp: document.getElementById('familiar_cp').value,
                estado: document.getElementById('familiar_estado').value,
                telefono_domicilio: document.getElementById('familiar_telefono_domicilio').value || null
            };
            if (!domicilioData.calle || !domicilioData.num_ext || !domicilioData.colonia || 
                !domicilioData.localidad || !domicilioData.municipio || !domicilioData.cp || !domicilioData.estado) {
                alert('Complete todos los campos del domicilio.');
                return;
            }
        }
    }
    
    const esContacto = document.querySelector('input[name="familiar_contacto"]:checked')?.value === '1';
    const prioridad = parseInt(document.getElementById('familiar_prioridad').value) || 0;
    
    if (!esContacto && prioridad > 0) {
        alert('Si no autoriza como contacto de emergencia, la prioridad debe ser 0.');
        return;
    }
    
    if (esContacto && prioridad <= 0) {
        alert('Si autoriza como contacto de emergencia, debe asignar una prioridad mayor a 0.');
        document.getElementById('familiar_prioridad').focus();
        return;
    }
    
    const esTutor = document.querySelector('input[name="familiar_tutor"]:checked')?.value === '1';
    if (!esContacto && esTutor) {
        alert('Un familiar que no es contacto de emergencia no puede ser tutor.');
        document.getElementById('tutorNo').checked = true;
        return;
    }
    
    if (esContacto && (prioridad <= 0 || isNaN(prioridad))) {
        alert('Debe asignar una prioridad válida (número mayor a 0).');
        document.getElementById('familiar_prioridad').focus();
        return;
    }
    
    const familiarData = {
        vive: familiarVive,
        nombre: document.getElementById('familiar_nombre').value,
        apellido1: document.getElementById('familiar_apellido1').value,
        apellido2: document.getElementById('familiar_apellido2').value || null,
        fecha_nacimiento: fecha,
        telefono_celular: document.getElementById('familiar_telefono').value || null,
        id_domicilio: id_domicilio,
        escolaridad: document.getElementById('familiar_escolaridad').value,
        ocupacion: document.getElementById('familiar_ocupacion').value || null,
        lugar_trabajo: document.getElementById('familiar_lugar_trabajo').value || null,
        horario_laboral: document.getElementById('familiar_horario_laboral').value || null,
        domicilio_trabajo: document.getElementById('familiar_domicilio_trabajo').value || null,
        telefono_trabajo: document.getElementById('familiar_telefono_trabajo').value || null,
        domicilio: domicilioData
    };
    
    const nuevoFamiliar = {
        id: familiarEnEdicion?.id,
        temp_id: familiarEnEdicion?.temp_id,
        familiar: familiarData,
        relacion: {
            parentesco: document.getElementById('parentesco').value,
            tutor: esTutor,
            contacto_emergencia: prioridad,
            comparte_domicilio: comparteDomicilio
        }
    };
    
    if (familiarEnEdicionIndex !== null) {
        familiaresTemporales[familiarEnEdicionIndex] = nuevoFamiliar;
    }
    
    reordenarPrioridadesContactos(prioridad, nuevoFamiliar);
    
    renderizarFamiliaresAsignados();
    limpiarFormularioFamiliar();
    
    // Resetear estado de edición
    modoEdicionFamiliarActivo = false;
    familiarEnEdicion = null;
    familiarEnEdicionIndex = null;
    actualizarBotonesFamiliares();
    
    alert('Familiar actualizado correctamente. Presione "Guardar Todos los Cambios" para guardar permanentemente.');
});

// CANCELAR REGISTRO (nuevo familiar)
document.getElementById('btnCancelarRegistroFamiliar')?.addEventListener('click', function() {
    cancelarRegistroFamiliar();
});

// CANCELAR EDICIÓN (familiar existente)
document.getElementById('btnCancelarEdicionFamiliar')?.addEventListener('click', function() {
    cancelarEdicionFamiliar();
});

// CANCELAR CONSULTA (modo solo lectura)
document.getElementById('btnCancelarConsultaFamiliar')?.addEventListener('click', function() {
    cancelarConsultaFamiliar();
});

function validarFamiliarAntesDeAgregar() {
    // Validar campos obligatorios y resaltar los que faltan
    const camposObligatorios = [
        { id: 'parentesco', nombre: 'Parentesco' },
        { id: 'familiar_nombre', nombre: 'Nombre(s)' },
        { id: 'familiar_apellido1', nombre: 'Primer Apellido' },
        { id: 'familiar_ocupacion', nombre: 'Ocupación' }
    ];
    
    for (const campo of camposObligatorios) {
        const elemento = document.getElementById(campo.id);
        if (!elemento || !elemento.value.trim()) {
            if (elemento) {
                elemento.scrollIntoView({ behavior: 'smooth', block: 'center' });
                resaltarCampo(elemento);
            }
            return false;
        }
    }
    
    // Validar fecha campo por campo
    const dia = document.getElementById('familiar_dia');
    const mes = document.getElementById('familiar_mes');
    const anio = document.getElementById('familiar_anio');
    let campoFaltante = null;
    if (!dia.value) campoFaltante = dia;
    else if (!mes.value) campoFaltante = mes;
    else if (!anio.value) campoFaltante = anio;
    
    if (campoFaltante) {
        campoFaltante.scrollIntoView({ behavior: 'smooth', block: 'center' });
        resaltarCampo(campoFaltante);
        return false;
    }
    
    // Validar escolaridad
    const escolaridad = document.getElementById('familiar_escolaridad');
    if (!escolaridad.value) {
        escolaridad.scrollIntoView({ behavior: 'smooth', block: 'center' });
        resaltarCampo(escolaridad);
        return false;
    }
    
    const familiarVive = document.querySelector('input[name="familiar_vive"]:checked')?.value === '1';
    
    if (familiarVive) {
        const comparteDomicilio = document.querySelector('input[name="comparte_domicilio"]:checked')?.value === '1';
        
        if (comparteDomicilio) {
            // Validar domicilio del alumno
            const domicilioAlumnoCampos = [
                { id: 'calle', nombre: 'Calle' },
                { id: 'num_ext', nombre: 'Número Exterior' },
                { id: 'colonia', nombre: 'Colonia' },
                { id: 'domicilio_localidad', nombre: 'Localidad' },
                { id: 'domicilio_municipio', nombre: 'Municipio' },
                { id: 'cp', nombre: 'Código Postal' },
                { id: 'domicilio_estado', nombre: 'Estado' }
            ];
            
            for (const campo of domicilioAlumnoCampos) {
                const elemento = document.getElementById(campo.id);
                if (!elemento || !elemento.value.trim()) {
                    elemento.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    resaltarCampo(elemento);
                    alert(`Complete el campo ${campo.nombre} del domicilio del alumno antes de añadir un familiar que comparte domicilio.`);
                    return false;
                }
            }
        } else {
            // Validar domicilio del familiar
            const domicilioFamiliarCampos = [
                { id: 'familiar_calle', nombre: 'Calle' },
                { id: 'familiar_num_ext', nombre: 'Número Exterior' },
                { id: 'familiar_colonia', nombre: 'Colonia' },
                { id: 'familiar_localidad', nombre: 'Localidad' },
                { id: 'familiar_municipio', nombre: 'Municipio' },
                { id: 'familiar_cp', nombre: 'Código Postal' },
                { id: 'familiar_estado', nombre: 'Estado' }
            ];
            
            for (const campo of domicilioFamiliarCampos) {
                const elemento = document.getElementById(campo.id);
                if (!elemento || !elemento.value.trim()) {
                    elemento.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    resaltarCampo(elemento);
                    return false;
                }
            }
        }
    }
    
    const esContacto = document.querySelector('input[name="familiar_contacto"]:checked')?.value === '1';
    const prioridad = parseInt(document.getElementById('familiar_prioridad').value) || 0;
    
    if (esContacto && prioridad <= 0) {
        const prioridadInput = document.getElementById('familiar_prioridad');
        prioridadInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        resaltarCampo(prioridadInput);
        alert('Si autoriza como contacto de emergencia, debe asignar una prioridad mayor a 0.');
        return false;
    }
    
    if (!esContacto && prioridad > 0) {
        alert('Si no autoriza como contacto de emergencia, la prioridad debe ser 0.');
        document.getElementById('familiar_prioridad').value = '';
        return false;
    }
    
    const esTutor = document.querySelector('input[name="familiar_tutor"]:checked')?.value === '1';
    if (!esContacto && esTutor) {
        alert('Un familiar que no es contacto de emergencia no puede ser tutor.');
        document.getElementById('tutorNo').checked = true;
        return false;
    }
    
    return true;
}

// Función para reordenar prioridades de contactos
function reordenarPrioridadesContactos(nuevaPrioridadDeseada = null, familiarEnEdicionOriginal = null) {
    if (!familiaresTemporales || familiaresTemporales.length === 0) return false;
    
    // Hacer una copia de los familiares
    let familiaresActualizados = [];
    
    // 1. Separar contactos (prioridad > 0) y no contactos (prioridad = 0)
    let contactos = [];
    let noContactos = [];
    
    for (let f of familiaresTemporales) {
        // Determinar si es contacto (prioridad > 0)
        const esContacto = f.relacion && f.relacion.contacto_emergencia && f.relacion.contacto_emergencia > 0;
        
        if (esContacto) {
            contactos.push({
                ...f,
                relacion: { ...f.relacion }
            });
        } else {
            noContactos.push({
                ...f,
                relacion: { ...f.relacion, contacto_emergencia: 0 }
            });
        }
    }
    
    // 2. Si estamos editando un familiar, actualizar su prioridad en la lista de contactos
    let familiarEditandoActualizado = null;
    if (familiarEnEdicionOriginal && familiarEnEdicionOriginal.relacion && familiarEnEdicionOriginal.relacion.contacto_emergencia > 0) {
        familiarEditandoActualizado = {
            ...familiarEnEdicionOriginal,
            relacion: {
                ...familiarEnEdicionOriginal.relacion,
                contacto_emergencia: nuevaPrioridadDeseada || familiarEnEdicionOriginal.relacion.contacto_emergencia
            }
        };
        
        // Remover el familiar editado de la lista de contactos (si existe)
        contactos = contactos.filter(f => {
            if (f.id && familiarEnEdicionOriginal.id && f.id === familiarEnEdicionOriginal.id) return false;
            if (f.temp_id && familiarEnEdicionOriginal.temp_id && f.temp_id === familiarEnEdicionOriginal.temp_id) return false;
            return true;
        });
    }
    
    // 3. Ordenar contactos por prioridad actual
    contactos.sort((a, b) => a.relacion.contacto_emergencia - b.relacion.contacto_emergencia);
    
    // 4. Determinar la prioridad a insertar
    let prioridadInsertar = nuevaPrioridadDeseada;
    if (familiarEditandoActualizado) {
        prioridadInsertar = familiarEditandoActualizado.relacion.contacto_emergencia;
    }
    
    if (prioridadInsertar && prioridadInsertar > 0) {
        // Validar que la prioridad no exceda contactos.length + 1
        let posicion = prioridadInsertar;
        if (posicion > contactos.length + 1) {
            posicion = contactos.length + 1;
        }
        if (posicion < 1) posicion = 1;
        
        // Crear un nuevo array para los contactos reordenados
        let nuevosContactos = [];
        
        // Reasignar prioridades
        let nuevaPrioridad = 1;
        
        for (let i = 0; i < contactos.length; i++) {
            // Si llegamos a la posición donde debemos insertar el nuevo contacto
            if (nuevaPrioridad === posicion) {
                // Insertar el nuevo contacto
                if (familiarEditandoActualizado) {
                    familiarEditandoActualizado.relacion.contacto_emergencia = nuevaPrioridad;
                    nuevosContactos.push(familiarEditandoActualizado);
                    nuevaPrioridad++;
                }
            }
            
            // Insertar el contacto existente con la nueva prioridad
            let contactoExistente = { ...contactos[i] };
            contactoExistente.relacion.contacto_emergencia = nuevaPrioridad;
            nuevosContactos.push(contactoExistente);
            nuevaPrioridad++;
        }
        
        // Si después del bucle aún no se insertó el nuevo contacto (caso: insertar al final)
        if (familiarEditandoActualizado && nuevosContactos.length < contactos.length + 1) {
            familiarEditandoActualizado.relacion.contacto_emergencia = nuevaPrioridad;
            nuevosContactos.push(familiarEditandoActualizado);
        }
        
        contactos = nuevosContactos;
    } else {
        // Solo reasignar prioridades secuencialmente
        let nuevaPrioridad = 1;
        for (let contacto of contactos) {
            contacto.relacion.contacto_emergencia = nuevaPrioridad;
            nuevaPrioridad++;
        }
    }
    
    // 5. Combinar contactos y no contactos
    familiaresActualizados = [...contactos, ...noContactos];
    
    // 6. Actualizar el array global
    familiaresTemporales = familiaresActualizados;
    
    // 7. Si estábamos editando, actualizar la referencia
    if (familiarEditandoActualizado && !familiarEnEdicionOriginal) {
        let familiarEncontrado = familiaresTemporales.find(f => {
            if (f.id && familiarEditandoActualizado.id && f.id === familiarEditandoActualizado.id) return true;
            if (f.temp_id && familiarEditandoActualizado.temp_id && f.temp_id === familiarEditandoActualizado.temp_id) return true;
            return false;
        });
        
        if (familiarEncontrado) {
            let nuevoIndex = familiaresTemporales.findIndex(f => f === familiarEncontrado);
            if (nuevoIndex !== -1) {
                cargarFamiliarEnFormulario(familiarEncontrado, nuevoIndex);
            }
        }
    }
    
    return true;
}

// Función para sincronizar el familiar que se está editando después de un reordenamiento
function sincronizarFamiliarEnEdicionDespuesDeReordenar(familiarOriginal) {
    if (!familiarOriginal) return null;
    
    // Buscar el familiar actualizado en familiaresTemporales
    const familiarActualizado = familiaresTemporales.find(f => 
        (f.id && familiarOriginal.id && f.id === familiarOriginal.id) ||
        (f.temp_id && familiarOriginal.temp_id && f.temp_id === familiarOriginal.temp_id)
    );
    
    if (familiarActualizado) {
        const nuevoIndex = familiaresTemporales.findIndex(f => f === familiarActualizado);
        if (nuevoIndex !== -1) {
            // Actualizar el formulario con los datos actualizados
            cargarFamiliarEnFormulario(familiarActualizado, nuevoIndex);
            return familiarActualizado;
        }
    }
    return null;
}

// Botón Cancelar Todo
document.getElementById('cancelarTodoBtn')?.addEventListener('click', function() {
    const alumnoId = document.getElementById('alumnoId').value;
    const busquedaInput = document.getElementById('busqueda_alumno');
    const alumnoIdHidden = document.getElementById('alumno_id_selected');
    
    if (!alumnoId || !alumnoId.trim()) {
        // No hay alumno seleccionado, solo limpiar el formulario
        if (confirm('No hay alumno seleccionado. ¿Deseas limpiar el formulario?')) {
            // Reiniciar variables temporales
            becasTemporales = [];
            familiaresTemporales = [];
            transportesTemporales = [];
            deportesTemporales = [];
            renderizarBecasAsignadas();
            renderizarFamiliaresAsignados();
            renderizarTransportesAsignados();
            renderizarDeportesAsignados();
            
            // Limpiar campos básicos
            const formulario = document.getElementById('registroForm');
            if (formulario) {
                const inputs = formulario.querySelectorAll('input:not([type="hidden"]), select, textarea');
                inputs.forEach(input => {
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        if (input.id === 'buscar_grupo') {
                            input.value = '';
                        } else {
                            input.checked = false;
                        }
                    } else if (input.id === 'id_grupo') {
                        input.value = '';
                    } else {
                        input.value = '';
                    }
                });
            }
            
            // Resetear acordeones
            document.querySelectorAll('.accordion-section').forEach(section => {
                section.classList.remove('expanded');
            });
            
            // Limpiar contadores
            actualizarTodosLosContadores();
        }
        return;
    }
    
    // Confirmar acción
    if (!confirm('¿Estás seguro de que deseas cancelar TODOS los cambios no guardados? Los datos volverán a su estado original.')) {
        return;
    }
    
    // Guardar el valor actual del campo de búsqueda para restaurarlo después
    const textoBusqueda = busquedaInput ? busquedaInput.value : '';
    
    // Usar el ID del alumno para recargar los datos (igual que hace la búsqueda)
    if (alumnoIdHidden) {
        alumnoIdHidden.value = alumnoId;
    }
    
    // Recargar los datos del alumno usando la misma función que la búsqueda
    cargarDatosAlumnoCompleto(alumnoId);
    
    // Limpiar variables temporales por si acaso
    setTimeout(() => {
        becasTemporales = [];
        familiaresTemporales = [];
        transportesTemporales = [];
        deportesTemporales = [];
        
        // Cancelar cualquier modo edición activo
        cancelarEdicionFamiliar();
        cancelarEdicionBeca();
        
        // Desactivar modo edición si estaba activado
        const toggleEditMode = document.getElementById('toggle_edit_mode');
        if (toggleEditMode && toggleEditMode.checked) {
            toggleEditMode.checked = false;
            toggleEditMode.dispatchEvent(new Event('change'));
        }
        
        // Limpiar contraseña
        const passwordInput = document.getElementById('current_password_global');
        if (passwordInput) passwordInput.value = '';
        
        // Mostrar mensaje
        alert('Todos los cambios han sido cancelados. El formulario ha sido restaurado.');
    }, 300);
});

</script>
@endpush