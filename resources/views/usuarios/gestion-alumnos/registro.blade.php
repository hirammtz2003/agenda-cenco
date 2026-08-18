@extends('layouts.app')

@section('title', 'Registro de Nuevos Alumnos - SGGDI')

@php
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
            'name' => 'Registro de Nuevos Alumnos',
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
                <i class="fas fa-user-plus me-2 text-info"></i>
                REGISTRO DE NUEVOS ALUMNOS
            </h2>
        </div>
    </div>

    <!-- Switch para desplegar todas las secciones -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="toggle_all_sections" 
                    {{ session('toggle_all_sections') ? 'checked' : '' }}
                    onchange="toggleAllSectionsMode()">
                <label class="form-check-label" for="toggle_all_sections">
                    <i class="fas fa-layer-group"></i> Permitir desplegar todas las secciones
                </label>
            </div>
        </div>
    </div>
    
    <form id="registroForm">
        @csrf
        
        <!-- Número de control -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Número de Control</label>
                <input type="text" 
                    id="num_control"
                    class="form-control" 
                    maxlength="14"
                    placeholder="Ingresar si ya se asignó"
                    autocomplete="off"
                    readonly
                    onfocus="this.removeAttribute('readonly')"
                    oninput="updateCounter(this, 'numControlCounter')">
                <small class="text-muted"><span id="numControlCounter">0</span>/14</small>
            </div>
        </div>

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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            readonly
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
                            <input class="form-check-input" type="radio" name="tiene_beca" id="becaSi" value="si">
                            <label class="form-check-label" for="becaSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tiene_beca" id="becaNo" value="no" checked>
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
                                    readonly
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="buscarBecas(this.value)">
                                <button type="button" class="btn btn-warning" id="btnAgregarBeca" disabled>
                                    <i class="fas fa-plus"></i> Añadir Beca
                                </button>
                            </div>
                            <div id="becaResults" class="search-results-dropdown"></div>
                            <small class="text-muted">Seleccione una beca de la lista y luego presione "Añadir Beca"</small>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estatus</label>
                            <select name="estatus" id="estatusBecaSelect" class="form-select">
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
                            <div id="becasAsignadas" class="d-flex flex-wrap gap-2 p-3 bg-light rounded">
                                <span class="text-muted">No hay becas asignadas</span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="becaSeleccionadaId" value="">

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
                <div id="panelTrabajo" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Lugar de trabajo</label>
                            <input type="text" 
                                id="lugar_trabajo"
                                class="form-control" 
                                maxlength="30"
                                placeholder="Nombre de la empresa o institución"
                                autocomplete="off"
                                readonly
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
                                readonly
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
                                readonly
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
                                readonly
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
                                    readonly
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
                                    readonly
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
                                    readonly
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
                                    readonly
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
                                    readonly
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
                                        readonly
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
                                        readonly
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
                                        readonly
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
                                        readonly
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
                                        readonly
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
                                        readonly
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
                                        readonly
                                        onfocus="this.removeAttribute('readonly')"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateCounter(this, 'familiar_cpCounter')"
                                        data-required="true">
                                    <small class="text-muted"><span id="familiar_cpCounter">0</span>/5</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Estado *</label>
                                    <input type="text" id="familiar_estado" class="form-control" maxlength="20"
                                        autocomplete="off"
                                        readonly
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
                                    readonly
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
                                    readonly
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
                                    readonly
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="updateCounter(this, 'familiar_lugar_trabajoCounter')">
                                <small class="text-muted"><span id="familiar_lugar_trabajoCounter">0</span>/30</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Horario laboral</label>
                                <input type="text" id="familiar_horario_laboral" class="form-control" maxlength="50"
                                    placeholder="Ej: 9:00 - 18:00, Tiempo completo, etc."
                                    autocomplete="off"
                                    readonly
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
                                    readonly
                                    onfocus="this.removeAttribute('readonly')"
                                    oninput="updateCounter(this, 'familiar_domicilio_trabajoCounter')">
                                <small class="text-muted"><span id="familiar_domicilio_trabajoCounter">0</span>/100</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Teléfono del trabajo</label>
                                <input type="tel" id="familiar_telefono_trabajo" class="form-control" maxlength="10" 
                                    placeholder="10 dígitos"
                                    autocomplete="off"
                                    readonly
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
                        <!-- Botón Agregar Familiar -->
                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-primary" id="btnAgregarFamiliar">
                                    <i class="fas fa-plus"></i> Añadir Familiar
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
                            readonly
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
                                readonly
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
                                readonly
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
                                readonly
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
                                    readonly
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
                                    readonly
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
                            readonly
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
                                    readonly
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
                                    readonly
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
                                    readonly
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
                                readonly
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
                            readonly
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
                            readonly
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
                                readonly
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
                            readonly
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
                        <textarea id="cirugia" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'cirugiaCounter')"></textarea>
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
                        <textarea id="alergia" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'alergiaCounter')"></textarea>
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
                        <textarea id="limitante_fisico" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'limitanteCounter')"></textarea>
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
                        <textarea id="problema_auditivo" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'auditivoCounter')"></textarea>
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
                        <textarea id="adiccion" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'adiccionCounter')"></textarea>
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
                        <textarea id="padecimiento_emocional" class="form-control" maxlength="255" rows="1" placeholder="Describa el caso." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'emocionalCounter')"></textarea>
                        <small class="text-muted"><span id="emocionalCounter">0</span>/255</small>
                    </div>
                </div>                
                <!-- Enfermedad actual -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">¿Qué tipo de enfermedad padece actualmente?</label>
                        <textarea id="enfermedad_actual" class="form-control" maxlength="255" rows="1" placeholder="Describa en caso de haber." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'enfermedadCounter')"></textarea>
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
                            <textarea id="otro_sintoma" class="form-control" maxlength="255" rows="1" placeholder="Describa otros síntomas de salud mental que presente el alumno y no estén contemplados en la lista anterior." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'otroSintomaCounter')"></textarea>
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
                        <textarea id="medicamento_controlado" class="form-control" maxlength="255" rows="1" placeholder="Nombre el o los medicamentos." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'medicamentoCounter')"></textarea>
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
                        <textarea id="alergia_medicamento" class="form-control" maxlength="255" rows="1" placeholder="Nombre el o los medicamentos." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'alergiaMedCounter')"></textarea>
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
                        <textarea id="motivo_hospitalizacion" class="form-control" maxlength="255" rows="1" placeholder="Mencione los motivos." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'hospitalizacionCounter')"></textarea>
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
                            readonly
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
                            readonly
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
                                readonly
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
                            readonly
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
                            readonly
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
                            readonly
                            onfocus="this.removeAttribute('readonly')"
                            oninput="this.value = Math.abs(parseInt(this.value)) || ''">
                        <span>horas</span>
                    </div>
                </div>
                <!-- Uso frecuente de computadora -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">¿Cuál es el uso más frecuente que le da a la computadora?</label>
                        <textarea id="uso_compu" class="form-control" maxlength="50" rows="1" placeholder="Ej: Tareas escolares, Juegos, Redes sociales..." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'usoCompuCounter')"></textarea>
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
                        <textarea id="temas_chat" class="form-control" maxlength="255" rows="2" placeholder="Ej: Habla de videojuegos con sus amigos, temas escolares con compañeros..." autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" oninput="updateCounter(this, 'temasChatCounter')"></textarea>
                        <small class="text-muted"><span id="temasChatCounter">0</span>/255</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de guardado -->
        <div class="row mt-4 pt-4 border-top">
            <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-2">                    
                <!-- Botnes de Consultas -->
                <div class="d-flex gap-2">
                    <a href="{{ route('alumnos.consulta-general') }}" class="btn btn-outline-warning">
                        <i class="fas fa-external-link-alt me-2"></i>Ir a CONSULTA Y EDICIÓN GENERAL
                    </a>
                    <a href="{{ route('alumnos.consulta-individual') }}" class="btn btn-outline-success">
                        <i class="fas fa-external-link-alt me-2"></i>Ir a CONSULTA Y EDICIÓN INDIVIDUAL
                    </a>
                </div>
                <!-- Botones de Acciones -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info" id="btnRegistrarNuevoAlumno">
                        <i class="fas fa-save me-2"></i>Registrar Nuevo Alumno
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormulario()">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </button>
                    <a href="{{ route('alumnos.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                    </a>
                </div>
            </div>
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

let modoMultiplesSecciones = false;

function toggleAllSectionsMode() {
    modoMultiplesSecciones = !modoMultiplesSecciones;
    
    if (!modoMultiplesSecciones) {
        document.querySelectorAll('.accordion-section').forEach(section => {
            section.classList.remove('expanded');
        });
    }
    
    // Guardar preferencia en sesión
    fetch('{{ route("alumnos.toggle-sections-mode") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ modo_multiple: modoMultiplesSecciones })
    });
}

// ===========================================
// FUNCIONES DE UTILIDAD
// ===========================================

function updateCounter(input, counterId) {
    document.getElementById(counterId).textContent = input.value.length;
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
// ACORDEONES
// ===========================================

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

function expandSection(sectionId) { expandSectionWithScroll(sectionId); }
function collapseAllSections() { document.querySelectorAll('.accordion-section').forEach(s => s.classList.remove('expanded')); }

function initAccordions() {
    document.querySelectorAll('.accordion-section').forEach(section => {
        const header = section.querySelector('.accordion-header');
        header.addEventListener('click', function(e) {
            if (e.target.closest('.accordion-icon')) return;
            
            if (modoMultiplesSecciones) {
                // Modo múltiple: toggle simple
                section.classList.toggle('expanded');
            } else {
                // Modo normal: solo una abierta
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

// ===========================================
// VALIDACIÓN CONDICIONAL
// ===========================================

const conditionalRequiredFields = {
    familiar_prioridad: { dependsOn: () => document.getElementById('contactoSi')?.checked === true }
};

// ===========================================
// VALIDACIÓN PRINCIPAL
// ===========================================

function getFirstInvalidFieldInSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) return null;

    // SECCIÓN 1: Validar en orden lógico
    if (sectionId === 'section1') {
        // 1. Primero validar campos de nombre
        const nombreCampos = ['alumno_nombre', 'alumno_apellido1'];
        for (const campoId of nombreCampos) {
            const elemento = document.getElementById(campoId);
            if (elemento && (!elemento.value || elemento.value.trim() === '')) {
                return elemento;
            }
        }
        
        // 2. Validar lugar de nacimiento
        const lugarCampos = ['localidad', 'municipio', 'estado', 'pais'];
        for (const campoId of lugarCampos) {
            const elemento = document.getElementById(campoId);
            if (elemento && (!elemento.value || elemento.value.trim() === '')) {
                return elemento;
            }
        }
        
        // 3. Validar domicilio
        const domicilioCampos = ['calle', 'num_ext', 'colonia', 'domicilio_localidad', 'domicilio_municipio', 'cp', 'domicilio_estado'];
        for (const campoId of domicilioCampos) {
            const elemento = document.getElementById(campoId);
            if (elemento && (!elemento.value || elemento.value.trim() === '')) {
                return elemento;
            }
        }
        
        // 4. Validar secundaria
        const secundariaCampos = ['secundaria_nombre', 'secundaria_tipo', 'secundaria_localidad', 'secundaria_municipio', 'secundaria_estado', 'secundaria_pais'];
        for (const campoId of secundariaCampos) {
            const elemento = document.getElementById(campoId);
            if (elemento && (!elemento.value || elemento.value.trim() === '')) {
                return elemento;
            }
        }
        
        // 5. ÚLTIMO: Validar grupo
        const grupoInput = document.getElementById('id_grupo');
        if (!grupoInput || !grupoInput.value) {
            return document.getElementById('buscar_grupo');
        }
        
        // 6. Luego validar otros campos required de la sección 1 (teléfono, email, etc.)
        const otrosRequired = section.querySelectorAll('[data-required="true"]');
        for (const element of otrosRequired) {
            // Saltar los campos que ya validamos
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
    
    // Sección 2: Obtener elementos required fuera del card-body
    let requiredElements = [];
    if (sectionId === 'section2') {
        const allRequired = section.querySelectorAll('[data-required="true"]');
        for (const el of allRequired) {
            if (!el.closest('.card-body')) {
                requiredElements.push(el);
            }
        }
        // Validar familiares añadidos
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
    
    // SECCIÓN 3: Validaciones específicas
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
    
    // Validar elementos required
    for (const element of requiredElements) {
        if (conditionalRequiredFields[element.id] && !conditionalRequiredFields[element.id].dependsOn()) continue;
        if (!esCampoValido(element)) return element;
    }
    
    // Validar campos condicionales adicionales
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
                // Maneja tanto elementos DOM como objetos con mensaje
                if (invalidField.message) {
                    // Es un objeto con mensaje personalizado
                    scrollToSectionHeader(sectionId);
                    alert(invalidField.message);
                } else if (invalidField.isSectionHeader) {
                    // Por compatibilidad con otras validaciones que uses
                    scrollToSectionHeader(sectionId);
                    alert(invalidField.message);
                } else {
                    // Es un elemento DOM
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
// VALIDACIÓN FAMILIAR
// ===========================================

function validarFamiliarAntesDeAgregar() {
    const camposObligatorios = [
        { id: 'parentesco', nombre: 'Parentesco' },
        { id: 'familiar_nombre', nombre: 'Nombre(s)' },
        { id: 'familiar_apellido1', nombre: 'Primer Apellido' }
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
    
    const escolaridad = document.getElementById('familiar_escolaridad');
    if (!escolaridad || !escolaridad.value) {
        escolaridad.scrollIntoView({ behavior: 'smooth', block: 'center' });
        resaltarCampo(escolaridad);
        return false;
    }
    
    const familiarVive = document.querySelector('input[name="familiar_vive"]:checked')?.value === '1';
    
    if (familiarVive) {
        const comparteDomicilio = document.querySelector('input[name="comparte_domicilio"]:checked')?.value === '1';
        const domicilioIdGuardado = document.getElementById('domicilioId').value;
        
        if (comparteDomicilio) {
            if (!domicilioIdGuardado) {
                const domicilioAlumnoCampos = [
                    { id: 'calle', nombre: 'Calle' }, { id: 'num_ext', nombre: 'Número Exterior' },
                    { id: 'colonia', nombre: 'Colonia' }, { id: 'domicilio_localidad', nombre: 'Localidad' },
                    { id: 'domicilio_municipio', nombre: 'Municipio' }, { id: 'cp', nombre: 'Código Postal' },
                    { id: 'domicilio_estado', nombre: 'Estado' }
                ];
                for (const campo of domicilioAlumnoCampos) {
                    const elemento = document.getElementById(campo.id);
                    if (!elemento || !elemento.value.trim()) {
                        alert(`Complete el campo ${campo.nombre} del domicilio del alumno antes de añadir un familiar que comparte domicilio.`);
                        const section1 = document.getElementById('section1');
                        if (section1 && !section1.classList.contains('expanded')) expandSectionWithScroll('section1');
                        setTimeout(() => {
                            elemento.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            resaltarCampo(elemento);
                        }, 200);
                        return false;
                    }
                }
            }
        } else {
            const domicilioFamiliarCampos = [
                { id: 'familiar_calle', nombre: 'Calle' }, { id: 'familiar_num_ext', nombre: 'Número Exterior' },
                { id: 'familiar_colonia', nombre: 'Colonia' }, { id: 'familiar_localidad', nombre: 'Localidad' },
                { id: 'familiar_municipio', nombre: 'Municipio' }, { id: 'familiar_cp', nombre: 'Código Postal' },
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

    const ocupacion = document.getElementById('familiar_ocupacion');
    if (!ocupacion || !ocupacion.value) {
        ocupacion.scrollIntoView({ behavior: 'smooth', block: 'center' });
        resaltarCampo(ocupacion);
        return false;
    }
    
    const esContacto = document.querySelector('input[name="familiar_contacto"]:checked')?.value === '1';
    const prioridad = document.getElementById('familiar_prioridad')?.value;
    
    if (esContacto && (!prioridad || prioridad <= 0)) {
        alert('Si autoriza como contacto de emergencia, debe asignar una prioridad mayor a 0.');
        const prioridadInput = document.getElementById('familiar_prioridad');
        prioridadInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        resaltarCampo(prioridadInput);
        return false;
    }
    
    // NUEVA VALIDACIÓN: Un familiar que no es contacto NO puede ser tutor
    const esTutor = document.querySelector('input[name="familiar_tutor"]:checked')?.value === '1';
    if (!esContacto && esTutor) {
        alert('Un familiar que no es contacto de emergencia no puede ser tutor.');
        document.getElementById('tutorNo').checked = true;
        return false;
    }
    
    return true;
}

// ===========================================
// VALIDACIÓN NÚMERO DE CONTROL
// ===========================================

async function validarNumeroControl(numControl) {
    if (!numControl || numControl.trim() === '') return true;
    try {
        const response = await fetch('{{ route("alumnos.validar-numero-control") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ num_control: numControl })
        });
        const data = await response.json();
        return data.disponible;
    } catch (error) {
        console.error('Error validando número de control:', error);
        return true;
    }
}

// ===========================================
// FRECUENCIAS
// ===========================================

function setupFrecuencia(prefix, hiddenInputId) {
    const aniosSelect = document.getElementById(`${prefix}_anios`);
    const mesesSelect = document.getElementById(`${prefix}_meses`);
    const opcionNunca = document.getElementById(`${prefix}Nunca`);
    const opcionSoloEnfermo = document.getElementById(`${prefix}SoloEnfermo`);
    const hiddenInput = document.getElementById(hiddenInputId);
    
    function limpiarRadios() { if (opcionNunca) opcionNunca.checked = false; if (opcionSoloEnfermo) opcionSoloEnfermo.checked = false; }
    function limpiarSelects() { if (aniosSelect) aniosSelect.value = ''; if (mesesSelect) mesesSelect.value = ''; }
    function actualizarHidden() {
        if (opcionNunca && opcionNunca.checked) hiddenInput.value = 0;
        else if (opcionSoloEnfermo && opcionSoloEnfermo.checked) hiddenInput.value = -1;
        else hiddenInput.value = (parseInt(aniosSelect?.value) || 0) + (parseInt(mesesSelect?.value) || 0);
    }
    
    if (aniosSelect) aniosSelect.addEventListener('change', () => { limpiarRadios(); actualizarHidden(); });
    if (mesesSelect) mesesSelect.addEventListener('change', () => { limpiarRadios(); actualizarHidden(); });
    if (opcionNunca) opcionNunca.addEventListener('change', () => { if (opcionNunca.checked) { limpiarSelects(); if (opcionSoloEnfermo) opcionSoloEnfermo.checked = false; } actualizarHidden(); });
    if (opcionSoloEnfermo) opcionSoloEnfermo.addEventListener('change', () => { if (opcionSoloEnfermo.checked) { limpiarSelects(); if (opcionNunca) opcionNunca.checked = false; } actualizarHidden(); });
    
    function resetearPorDefecto() { limpiarSelects(); if (opcionNunca) opcionNunca.checked = false; if (opcionSoloEnfermo) opcionSoloEnfermo.checked = true; actualizarHidden(); }
    resetearPorDefecto();
    return resetearPorDefecto;
}

function obtenerFrecuencia(prefix) {
    const opcionNunca = document.getElementById(`${prefix}Nunca`);
    const opcionSoloEnfermo = document.getElementById(`${prefix}SoloEnfermo`);
    const aniosSelect = document.getElementById(`${prefix}_anios`);
    const mesesSelect = document.getElementById(`${prefix}_meses`);
    if (opcionNunca && opcionNunca.checked) return 0;
    if (opcionSoloEnfermo && opcionSoloEnfermo.checked) return -1;
    return (parseInt(aniosSelect?.value) || 0) + (parseInt(mesesSelect?.value) || 0);
}

resetFrecuenciaMedico = setupFrecuencia('medico', 'frecuencia_medico');
resetFrecuenciaDentista = setupFrecuencia('dentista', 'frecuencia_dentista');

// ===========================================
// LIMPIAR FORMULARIO
// ===========================================

function limpiarFormulario() {
    // Limpiar sección 1
    document.getElementById('localidad').value = ''; document.getElementById('municipio').value = '';
    document.getElementById('estado').value = ''; document.getElementById('pais').value = '';
    document.getElementById('lugarNacimientoId').value = '';
    document.getElementById('calle').value = ''; document.getElementById('num_ext').value = '';
    document.getElementById('num_int').value = ''; document.getElementById('colonia').value = '';
    document.getElementById('domicilio_localidad').value = ''; document.getElementById('domicilio_municipio').value = '';
    document.getElementById('cp').value = ''; document.getElementById('domicilio_estado').value = '';
    document.getElementById('telefono_domicilio').value = '';
    document.getElementById('secundaria_nombre').value = ''; document.getElementById('secundaria_tipo').value = '';
    document.getElementById('secundaria_localidad').value = ''; document.getElementById('secundaria_municipio').value = '';
    document.getElementById('secundaria_estado').value = ''; document.getElementById('secundaria_pais').value = '';
    document.getElementById('secundariaId').value = '';
    document.getElementById('alumno_nombre').value = ''; document.getElementById('alumno_apellido1').value = '';
    document.getElementById('alumno_apellido2').value = ''; document.getElementById('num_control').value = '';
    document.getElementById('telefono_celular').value = ''; document.getElementById('email_personal').value = '';
    document.getElementById('email_institucional').value = ''; document.getElementById('curp').value = '';
    document.getElementById('nss').value = ''; document.getElementById('buscar_grupo').value = '';
    document.getElementById('id_grupo').value = '';
    
    // Limpiar becas
    document.getElementById('becaSi').checked = false; document.getElementById('becaNo').checked = true;
    document.getElementById('panelBecas').style.display = 'none';
    document.getElementById('buscarBeca').value = ''; document.getElementById('becaSeleccionadaId').value = '';
    document.getElementById('btnAgregarBeca').disabled = true;
    becasTemporales = []; renderizarBecasAsignadas();
    fetch('{{ route("alumnos.limpiar-becas-temporales") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
    
    // Limpiar trabajo
    document.getElementById('trabajoSi').checked = false; document.getElementById('trabajoNo').checked = true;
    document.getElementById('panelTrabajo').style.display = 'none';
    document.getElementById('lugar_trabajo').value = ''; document.getElementById('horario_laboral').value = '';
    document.getElementById('domicilio_trabajo').value = ''; document.getElementById('telefono_trabajo').value = '';
    trabajoData = null;
    
    // Limpiar familiares
    document.getElementById('parentesco').value = '';
    familiaresTemporales = []; renderizarFamiliaresAsignados();
    familiarEnEdicion = null; limpiarFormularioFamiliar();
    
    // Limpiar sección 2.2
    document.getElementById('estado_civil_padres').value = '';
    document.getElementById('ingreso_familiar').value = ''; document.getElementById('gasto_familiar').value = '';
    document.getElementById('casaPropiaNo').checked = true; document.getElementById('autoPropioNo').checked = true;
    document.getElementById('servicioLuz').checked = false; document.getElementById('servicioAgua').checked = false;
    document.getElementById('servicioDrenaje').checked = false; document.getElementById('servicioAlumbrado').checked = false;
    document.getElementById('estadoCivilCounter').textContent = '0';
    
    // Limpiar sección 3
    document.getElementById('autoPropioAlumnoSi').checked = false; document.getElementById('autoPropioAlumnoNo').checked = true;
    document.getElementById('estado_civil_alumno').value = ''; document.getElementById('estadoCivilCounter').textContent = '0';
    document.getElementById('hijosSi').checked = false; document.getElementById('hijosNo').checked = true;
    document.getElementById('panelHijos').style.display = 'none';
    document.getElementById('num_hijos').value = ''; document.getElementById('edades_hijos').value = '';
    document.getElementById('apoyoSi').checked = false; document.getElementById('apoyoNo').checked = true;
    document.getElementById('panelMontoApoyo').style.display = 'none'; document.getElementById('monto_apoyo').value = '';
    document.getElementById('gasto_comida_transporte').value = ''; document.getElementById('comidas_diarias').value = '3';
    document.getElementById('traslado_horas').value = ''; document.getElementById('traslado_minutos').value = '';
    transportesTemporales = []; renderizarTransportesAsignados();
    document.getElementById('buscarTransporte').value = ''; document.getElementById('btnAgregarTransporte').disabled = true;
    
    // Limpiar sección 4
    document.getElementById('dispositivoCelular').checked = false; document.getElementById('dispositivoComputadora').checked = false;
    document.getElementById('dispositivoInternet').checked = false; document.getElementById('dispositivoTablet').checked = false;
    document.getElementById('otro_dispositivo').value = ''; document.getElementById('otroDispositivoCounter').textContent = '0';
    document.getElementById('problemaNo').checked = true; document.getElementById('panelProblemas').style.display = 'none';
    limpiarCheckboxesProblemas(); document.getElementById('otro_problema').value = ''; document.getElementById('otroProblemaCounter').textContent = '0';
    
    // Limpiar sección 5
    document.getElementById('estatura').value = ''; document.getElementById('peso').value = ''; document.getElementById('tipo_sangre').value = '';
    document.getElementById('vacunasNo').checked = true; document.getElementById('vacunasSi').checked = false;
    document.getElementById('anteojosNo').checked = true; document.getElementById('anteojosSi').checked = false;
    document.getElementById('graduacionContainer').style.display = 'none'; document.getElementById('graduacion_anteojos').value = '';
    const toggles = ['cirugia', 'alergia', 'limitante', 'auditivo', 'adiccion', 'emocional', 'medicamento', 'alergiaMed', 'hospitalizacion'];
    toggles.forEach(t => {
        const radioNo = document.getElementById(`${t}No`);
        const radioSi = document.getElementById(`${t}Si`);
        const container = document.getElementById(`${t}Container`);        
        let textareaId = t;
        switch (t) {
            case 'medicamento': textareaId = 'medicamento_controlado'; break;
            case 'alergiaMed': textareaId = 'alergia_medicamento'; break;
            case 'hospitalizacion': textareaId = 'motivo_hospitalizacion'; break;
            case 'limitante': textareaId = 'limitante_fisico'; break;
            case 'auditivo': textareaId = 'problema_auditivo'; break;
            case 'emocional': textareaId = 'padecimiento_emocional'; break;
            default: textareaId = t;
        }
        const textarea = document.getElementById(textareaId);
        if (radioNo) radioNo.checked = true;
        if (radioSi) radioSi.checked = false;
        if (container) container.style.display = 'none';
        if (textarea) textarea.value = '';
    });
    const enfermedadActual = document.getElementById('enfermedad_actual'); if (enfermedadActual) enfermedadActual.value = '';
    const sintomasNo = document.getElementById('sintomasNo'); const sintomasSi = document.getElementById('sintomasSi');
    if (sintomasNo) sintomasNo.checked = true; if (sintomasSi) sintomasSi.checked = false;
    const panelSintomas = document.getElementById('panelSintomas'); if (panelSintomas) panelSintomas.style.display = 'none';
    const sintomasCheckboxes = ['sintomaAnsiedad', 'sintomaEstres', 'sintomaDepresion', 'sintomaCulpa', 'sintomaTristeza'];
    sintomasCheckboxes.forEach(id => { const cb = document.getElementById(id); if (cb) cb.checked = false; });
    const otroSintoma = document.getElementById('otro_sintoma'); if (otroSintoma) otroSintoma.value = '';
    const diabetesNo = document.getElementById('diabetesNo'); const diabetesSi = document.getElementById('diabetesSi');
    if (diabetesNo) diabetesNo.checked = true; if (diabetesSi) diabetesSi.checked = false;
    const hipertensionNo = document.getElementById('hipertensionNo'); const hipertensionSi = document.getElementById('hipertensionSi');
    if (hipertensionNo) hipertensionNo.checked = true; if (hipertensionSi) hipertensionSi.checked = false;
    const cabezaNo = document.getElementById('cabezaNo'); const cabezaSi = document.getElementById('cabezaSi');
    if (cabezaNo) cabezaNo.checked = true; if (cabezaSi) cabezaSi.checked = false;
    const estomagoNo = document.getElementById('estomagoNo'); const estomagoSi = document.getElementById('estomagoSi');
    if (estomagoNo) estomagoNo.checked = true; if (estomagoSi) estomagoSi.checked = false;
    
    // Resetear frecuencias
    const medicoNunca = document.getElementById('medicoNunca'); const medicoSoloEnfermo = document.getElementById('medicoSoloEnfermo');
    const medicoAnios = document.getElementById('medico_anios'); const medicoMeses = document.getElementById('medico_meses');
    if (medicoNunca) medicoNunca.checked = false; if (medicoSoloEnfermo) medicoSoloEnfermo.checked = false;
    if (medicoAnios) medicoAnios.value = ''; if (medicoMeses) medicoMeses.value = '';
    const dentistaNunca = document.getElementById('dentistaNunca'); const dentistaSoloEnfermo = document.getElementById('dentistaSoloEnfermo');
    const dentistaAnios = document.getElementById('dentista_anios'); const dentistaMeses = document.getElementById('dentista_meses');
    if (dentistaNunca) dentistaNunca.checked = false; if (dentistaSoloEnfermo) dentistaSoloEnfermo.checked = false;
    if (dentistaAnios) dentistaAnios.value = ''; if (dentistaMeses) dentistaMeses.value = '';
    if (resetFrecuenciaMedico) resetFrecuenciaMedico(); if (resetFrecuenciaDentista) resetFrecuenciaDentista();
    
    const contadoresSalud = ['cirugiaCounter', 'alergiaCounter', 'limitanteCounter', 'auditivoCounter', 'adiccionCounter', 'emocionalCounter', 'enfermedadCounter', 'otroSintomaCounter', 'medicamentoCounter', 'alergiaMedCounter', 'hospitalizacionCounter'];
    contadoresSalud.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = '0'; });
    
    // Limpiar sección 6
    document.getElementById('pasatiempo_favorito').value = ''; document.getElementById('horas_pasatiempo').value = '';
    document.getElementById('buscarDeporte').value = ''; document.getElementById('btnAgregarDeporte').disabled = true;
    deportesTemporales = []; renderizarDeportesAsignados();
    document.getElementById('horas_deporte').value = ''; document.getElementById('horas_tv').value = '';
    document.getElementById('horas_compu').value = ''; document.getElementById('uso_compu').value = '';
    document.getElementById('chatearNo').checked = true; document.getElementById('temasChatContainer').style.display = 'none';
    document.getElementById('temas_chat').value = '';
    document.getElementById('pasatiempoCounter').textContent = '0'; document.getElementById('usoCompuCounter').textContent = '0';
    document.getElementById('temasChatCounter').textContent = '0';
    
    // Resetear acordeones
    collapseAllSections();
    resetearContadores();
}

function resetearContadores() {
    const contadores = [
        'localidadCounter', 'municipioCounter', 'estadoCounter', 'paisCounter', 'calleCounter', 'num_extCounter',
        'num_intCounter', 'coloniaCounter', 'domicilioLocalidadCounter', 'domicilioMunicipioCounter', 'cpCounter',
        'domicilioEstadoCounter', 'telefonoDomicilioCounter', 'secundariaNombreCounter', 'secundariaLocalidadCounter',
        'secundariaMunicipioCounter', 'secundariaEstadoCounter', 'secundariaPaisCounter', 'alumnoNombreCounter',
        'alumnoApellido1Counter', 'alumnoApellido2Counter', 'numControlCounter', 'telefonoCelularCounter',
        'emailPersonalCounter', 'emailInstitucionalCounter', 'curpCounter', 'nssCounter', 'lugarTrabajoCounter',
        'horarioLaboralCounter', 'domicilioTrabajoCounter', 'telefonoTrabajoCounter', 'parentescoCounter',
        'estadoCivilAlumnoCounter', 'otroDispositivoCounter', 'otroProblemaCounter', 'pasatiempoCounter',
        'usoCompuCounter', 'temasChatCounter'
    ];
    contadores.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = '0'; });
    const saludContadores = ['cirugiaCounter', 'alergiaCounter', 'limitanteCounter', 'auditivoCounter', 'adiccionCounter', 'emocionalCounter', 'enfermedadCounter', 'otroSintomaCounter', 'medicamentoCounter', 'alergiaMedCounter', 'hospitalizacionCounter'];
    saludContadores.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = '0'; });
}

function limpiarFormularioFamiliar() {
    document.getElementById('parentesco').value = ''; document.getElementById('familiar_nombre').value = '';
    document.getElementById('familiar_apellido1').value = ''; document.getElementById('familiar_apellido2').value = '';
    document.getElementById('familiar_telefono').value = ''; document.getElementById('familiar_escolaridad').value = '';
    document.getElementById('familiar_ocupacion').value = ''; document.getElementById('familiar_lugar_trabajo').value = '';
    document.getElementById('familiar_horario_laboral').value = ''; document.getElementById('familiar_domicilio_trabajo').value = '';
    document.getElementById('familiar_telefono_trabajo').value = '';
    document.getElementById('familiar_dia').value = ''; document.getElementById('familiar_mes').value = ''; document.getElementById('familiar_anio').value = '';
    document.getElementById('familiarViveSi').checked = true; document.getElementById('familiarViveNo').checked = false;
    document.getElementById('tutorNo').checked = true; document.getElementById('tutorSi').checked = false;
    document.getElementById('contactoNo').checked = true; document.getElementById('contactoSi').checked = false;
    const prioridadContainer = document.getElementById('prioridadContainer'); const prioridadInput = document.getElementById('familiar_prioridad');
    if (prioridadContainer) prioridadContainer.style.display = 'none';
    if (prioridadInput) { prioridadInput.value = ''; prioridadInput.required = false; }
    const comparteDomicilioSection = document.querySelector('input[name="comparte_domicilio"]')?.closest('.row');
    if (comparteDomicilioSection) comparteDomicilioSection.style.display = 'flex';
    document.getElementById('comparteSi').checked = true; document.getElementById('comparteNo').checked = false;
    document.getElementById('panelDomicilioDiferente').style.display = 'none';
    limpiarDomicilioFamiliar();
    const familiarContadores = ['familiarNombreCounter', 'familiarApellido1Counter', 'familiarApellido2Counter', 'familiarTelefonoCounter', 'familiar_calleCounter', 'familiar_num_extCounter', 'familiar_num_intCounter', 'familiar_coloniaCounter', 'familiar_localidadCounter', 'familiar_municipioCounter', 'familiar_cpCounter', 'familiar_estadoCounter', 'familiar_telefono_domicilioCounter', 'familiar_ocupacionCounter', 'familiar_lugar_trabajoCounter', 'familiar_horario_laboralCounter', 'familiar_domicilio_trabajoCounter', 'familiar_telefono_trabajoCounter', 'parentescoCounter'];
    familiarContadores.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = '0'; });
    const resultsDivs = ['parentescoResults', 'familiarApellido1Results', 'familiarApellido2Results', 'familiarColoniaResults'];
    resultsDivs.forEach(id => { const div = document.getElementById(id); if (div) div.style.display = 'none'; });
    familiarEnEdicion = null;
}

function limpiarDomicilioFamiliar() {
    const campos = ['familiar_calle', 'familiar_num_ext', 'familiar_num_int', 'familiar_colonia', 'familiar_localidad', 'familiar_municipio', 'familiar_cp', 'familiar_estado', 'familiar_telefono_domicilio', 'familiar_domicilio_id'];
    campos.forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    const contadores = ['familiar_calleCounter', 'familiar_num_extCounter', 'familiar_num_intCounter', 'familiar_coloniaCounter', 'familiar_localidadCounter', 'familiar_municipioCounter', 'familiar_cpCounter', 'familiar_estadoCounter', 'familiar_telefono_domicilioCounter'];
    contadores.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = '0'; });
    const coloniaResults = document.getElementById('familiarColoniaResults');
    if (coloniaResults) coloniaResults.style.display = 'none';
}

function limpiarCheckboxesProblemas() {
    const checkboxes = ['problemaDislexia', 'problemaVisuales', 'problemaAuditivo', 'problemaNoEntender', 'problemaLentitud', 'problemaLenguaje', 'problemaComunicacion', 'problemaDiscriminacion', 'problemaGestos', 'problemaDistraccion', 'problemaTDAH', 'problemaDiscapacidad'];
    checkboxes.forEach(id => { const cb = document.getElementById(id); if (cb) cb.checked = false; });
}

// ===========================================
// RENDERIZAR TAGS
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
        // Si ninguno es contacto, mantener orden original (por índice o temp_id)
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
    
    // Ordenar familiares antes de renderizar
    const familiaresOrdenados = ordenarFamiliaresPorPrioridad(familiaresTemporales);
    
    let html = '';
    familiaresOrdenados.forEach((f, idx) => {
        // Encontrar el índice original para mantener data-temp-id correcto
        const originalIndex = familiaresTemporales.findIndex(original => original.temp_id === f.temp_id);
        
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
        
        html += `<span class="badge ${colorClase} p-2 d-inline-flex align-items-center" data-temp-id="${f.temp_id}">
            ${nombreCompleto} | ${f.relacion.parentesco} | ${contactoTexto}${tutorTexto}
            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" onclick="eliminarFamiliarTemporal(${f.temp_id})" aria-label="Eliminar"></button>
        </span>`;
    });
    contenedor.innerHTML = html;
}

function renderizarTransportesAsignados() {
    const contenedor = document.getElementById('transportesAsignados');
    if (!contenedor) return;
    if (transportesTemporales.length === 0) {
        contenedor.innerHTML = '<span class="text-muted">No hay transportes añadidos</span>';
        return;
    }
    let html = '';
    transportesTemporales.forEach((transporte, index) => {
        html += `<span class="badge bg-success p-2 d-inline-flex align-items-center">
            ${transporte}
            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" onclick="eliminarTransporteTemporal(${index})" aria-label="Eliminar"></button>
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
    let html = '';
    deportesTemporales.forEach((deporte, index) => {
        html += `<span class="badge bg-danger p-2 d-inline-flex align-items-center">
            ${deporte}
            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" onclick="eliminarDeporteTemporal(${index})" aria-label="Eliminar"></button>
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
    becasTemporales.forEach(beca => {
        const estatusTexto = beca.activa ? 'Activa' : 'Inactiva';
        const estatusClase = beca.activa ? 'bg-warning' : 'bg-secondary';
        
        html += `<span class="badge ${estatusClase} p-2 d-inline-flex align-items-center">
            ${beca.nombre} | ${estatusTexto}
            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" onclick="eliminarBecaTemporal('${beca.id}')" aria-label="Eliminar"></button>
        </span>`;
    });
    contenedor.innerHTML = html;
}

// ===========================================
// ELIMINAR TEMPORALES
// ===========================================

window.eliminarFamiliarTemporal = function(tempId) {
    fetch('{{ route("alumnos.eliminar-familiar-temporal") }}', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ temp_id: tempId })
    }).then(response => response.json()).then(data => {
        if (data.success) { familiaresTemporales = data.familiares; renderizarFamiliaresAsignados(); }
    });
};

window.eliminarTransporteTemporal = function(index) { transportesTemporales.splice(index, 1); renderizarTransportesAsignados(); };
window.eliminarDeporteTemporal = function(index) { deportesTemporales.splice(index, 1); renderizarDeportesAsignados(); };
window.eliminarBecaTemporal = function(id) {
    fetch('{{ route("alumnos.eliminar-beca-temporal") }}', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ id_beca: id })
    }).then(response => response.json()).then(data => {
        if (data.success) { becasTemporales = data.becas; renderizarBecasAsignadas(); }
    });
};

// ===========================================
// BÚSQUEDAS (SIMPLIFICADAS)
// ===========================================

function buscarLugares(query) {
    if (query.length < 2) { document.getElementById('localidadResults').style.display = 'none'; return; }
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-lugar") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById('localidadResults').style.display = 'none'; }
        });
    }, 300);
}

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

function buscarColonia(query) {
    if (query.length < 2) { document.getElementById('coloniaResults').style.display = 'none'; return; }
    clearTimeout(domicilioTimeout);
    domicilioTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-domicilio") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById('coloniaResults').style.display = 'none'; }
        });
    }, 300);
}

function seleccionarDomicilio(domicilio) {
    document.getElementById('colonia').value = domicilio.colonia;
    document.getElementById('domicilio_localidad').value = domicilio.localidad;
    document.getElementById('domicilio_municipio').value = domicilio.municipio;
    document.getElementById('cp').value = domicilio.cp;
    document.getElementById('domicilio_estado').value = domicilio.estado;
    document.getElementById('domicilioId').value = domicilio.id;
    updateCounter(document.getElementById('calle'), 'calleCounter');
    updateCounter(document.getElementById('num_ext'), 'num_extCounter');
    updateCounter(document.getElementById('num_int'), 'num_intCounter');
    updateCounter(document.getElementById('colonia'), 'coloniaCounter');
    updateCounter(document.getElementById('domicilio_localidad'), 'domicilioLocalidadCounter');
    updateCounter(document.getElementById('domicilio_municipio'), 'domicilioMunicipioCounter');
    updateCounter(document.getElementById('cp'), 'cpCounter');
    updateCounter(document.getElementById('domicilio_estado'), 'domicilioEstadoCounter');
    updateCounter(document.getElementById('telefono_domicilio'), 'telefonoDomicilioCounter');
    document.getElementById('coloniaResults').style.display = 'none';
}

function buscarSecundaria(query) {
    if (query.length < 2) { document.getElementById('secundariaResults').style.display = 'none'; return; }
    clearTimeout(secundariaTimeout);
    secundariaTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-secundaria") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById('secundariaResults').style.display = 'none'; }
        });
    }, 300);
}

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

function buscarApellido(tipo, query) {
    if (query.length < 2) { document.getElementById(tipo === 'apellido1' ? 'apellido1Results' : 'apellido2Results').style.display = 'none'; return; }
    clearTimeout(apellidoTimeout);
    apellidoTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-apellido") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ tipo: tipo, busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById(tipo === 'apellido1' ? 'apellido1Results' : 'apellido2Results').style.display = 'none'; }
        });
    }, 300);
}

function seleccionarApellido(tipo, valor) {
    document.getElementById(tipo === 'apellido1' ? 'alumno_apellido1' : 'alumno_apellido2').value = valor;
    updateCounter(document.getElementById(tipo === 'apellido1' ? 'alumno_apellido1' : 'alumno_apellido2'), tipo === 'apellido1' ? 'alumnoApellido1Counter' : 'alumnoApellido2Counter');
    document.getElementById(tipo === 'apellido1' ? 'apellido1Results' : 'apellido2Results').style.display = 'none';
}

function buscarGrupos(query) {
    clearTimeout(grupoTimeout);
    grupoTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-grupos") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById('grupoResults').style.display = 'none'; }
        });
    }, 300);
}

function seleccionarGrupo(grupo) {
    document.getElementById('buscar_grupo').value = grupo.nombre;
    document.getElementById('id_grupo').value = grupo.id;
    document.getElementById('grupoResults').style.display = 'none';
}

function buscarParentesco(query) {
    if (query.length < 2) { document.getElementById('parentescoResults').style.display = 'none'; return; }
    clearTimeout(parentescoTimeout);
    parentescoTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-parentesco") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById('parentescoResults').style.display = 'none'; }
        });
    }, 300);
}

function seleccionarParentesco(valor) {
    document.getElementById('parentesco').value = valor;
    document.getElementById('parentescoResults').style.display = 'none';
}

function buscarApellidoFamiliar(tipo, query) {
    if (query.length < 2) { document.getElementById(tipo === 'apellido1' ? 'familiarApellido1Results' : 'familiarApellido2Results').style.display = 'none'; return; }
    clearTimeout(familiarApellidoTimeout);
    familiarApellidoTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-apellido") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ tipo: tipo, busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById(tipo === 'apellido1' ? 'familiarApellido1Results' : 'familiarApellido2Results').style.display = 'none'; }
        });
    }, 300);
}

function seleccionarApellidoFamiliar(tipo, valor) {
    document.getElementById(tipo === 'apellido1' ? 'familiar_apellido1' : 'familiar_apellido2').value = valor;
    updateCounter(document.getElementById(tipo === 'apellido1' ? 'familiar_apellido1' : 'familiar_apellido2'), tipo === 'apellido1' ? 'familiarApellido1Counter' : 'familiarApellido2Counter');
    document.getElementById(tipo === 'apellido1' ? 'familiarApellido1Results' : 'familiarApellido2Results').style.display = 'none';
}

function buscarColoniaFamiliar(query) {
    if (query.length < 2) { document.getElementById('familiarColoniaResults').style.display = 'none'; return; }
    clearTimeout(familiarColoniaTimeout);
    familiarColoniaTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-domicilio") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
            if (data.success && data.domicilios.length > 0) {
                const resultsDiv = document.getElementById('familiarColoniaResults');
                resultsDiv.innerHTML = '';
                resultadosFamiliarColonias = data.domicilios;
                data.domicilios.forEach((dom, index) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.dataset.index = index;
                    div.innerHTML = `<strong>${dom.colonia}</strong> - ${dom.localidad}, ${dom.municipio}, ${dom.estado}`;
                    div.addEventListener('click', () => seleccionarColoniaFamiliar(dom));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.style.display = 'block';
                selectedFamiliarColoniaIndex = -1;
            } else { document.getElementById('familiarColoniaResults').style.display = 'none'; }
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
    document.getElementById('familiarColoniaResults').style.display = 'none';
}

function buscarTransportes(query) {
    const btnAgregar = document.getElementById('btnAgregarTransporte');
    if (query.length >= 2) btnAgregar.disabled = false;
    else { btnAgregar.disabled = true; document.getElementById('transporteResults').style.display = 'none'; return; }
    clearTimeout(transporteTimeout);
    transporteTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-transportes") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById('transporteResults').style.display = 'none'; }
        });
    }, 300);
}

function seleccionarTransporte(valor) {
    document.getElementById('buscarTransporte').value = valor;
    document.getElementById('btnAgregarTransporte').disabled = false;
    document.getElementById('transporteResults').style.display = 'none';
}

function buscarDeportes(query) {
    const btnAgregar = document.getElementById('btnAgregarDeporte');
    if (query.length >= 2) btnAgregar.disabled = false;
    else { btnAgregar.disabled = true; document.getElementById('deporteResults').style.display = 'none'; return; }
    clearTimeout(deporteTimeout);
    deporteTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-deportes") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById('deporteResults').style.display = 'none'; }
        });
    }, 300);
}

function seleccionarDeporte(valor) {
    document.getElementById('buscarDeporte').value = valor;
    document.getElementById('btnAgregarDeporte').disabled = false;
    document.getElementById('deporteResults').style.display = 'none';
}

function buscarEstadoCivil(query) {
    if (query.length < 2) { document.getElementById('estadoCivilResults').style.display = 'none'; return; }
    clearTimeout(estadoCivilTimeout);
    estadoCivilTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-estado-civil") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById('estadoCivilResults').style.display = 'none'; }
        });
    }, 300);
}

function seleccionarEstadoCivil(valor) {
    document.getElementById('estado_civil_padres').value = valor;
    updateCounter(document.getElementById('estado_civil_padres'), 'estadoCivilCounter');
    document.getElementById('estadoCivilResults').style.display = 'none';
}

function buscarEstadoCivilAlumno(query) {
    if (query.length < 2) { document.getElementById('estadoCivilAlumnoResults').style.display = 'none'; return; }
    clearTimeout(estadoCivilAlumnoTimeout);
    estadoCivilAlumnoTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-estado-civil-alumno") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
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
            } else { document.getElementById('estadoCivilAlumnoResults').style.display = 'none'; }
        });
    }, 300);
}

function seleccionarEstadoCivilAlumno(valor) {
    document.getElementById('estado_civil_alumno').value = valor;
    updateCounter(document.getElementById('estado_civil_alumno'), 'estadoCivilAlumnoCounter');
    document.getElementById('estadoCivilAlumnoResults').style.display = 'none';
}

function buscarBecas(query) {
    if (query.length < 2) { document.getElementById('becaResults').style.display = 'none'; document.getElementById('btnAgregarBeca').disabled = true; return; }
    clearTimeout(becaTimeout);
    becaTimeout = setTimeout(() => {
        fetch('{{ route("alumnos.buscar-becas") }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ busqueda: query })
        }).then(response => response.json()).then(data => {
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
                document.getElementById('btnAgregarBeca').disabled = false;
            } else {
                document.getElementById('becaResults').style.display = 'none';
                document.getElementById('btnAgregarBeca').disabled = false;
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

// ===========================================
// EVENTOS Y CONFIGURACIÓN INICIAL
// ===========================================

document.addEventListener('DOMContentLoaded', function() {
    initAccordions();

    // Cargar preferencia de modo múltiple
    fetch('{{ route("alumnos.get-sections-mode") }}')
        .then(res => res.json())
        .then(data => {
            if (data.modo_multiple) {
                modoMultiplesSecciones = true;
                const toggleCheckbox = document.getElementById('toggle_all_sections');
                if (toggleCheckbox) toggleCheckbox.checked = true;
            }
        });
    
    // Mostrar/ocultar panel de becas
    document.querySelectorAll('input[name="tiene_beca"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const panel = document.getElementById('panelBecas');
            if (this.value === 'si') {
                panel.style.display = 'block';
                const estatusSelect = document.getElementById('estatusBecaSelect');
                if (estatusSelect) estatusSelect.value = '1';
            } else {
                panel.style.display = 'none';
                document.getElementById('buscarBeca').value = '';
                document.getElementById('btnAgregarBeca').disabled = true;
            }
        });
    });
    
    // Mostrar/ocultar panel de trabajo
    document.querySelectorAll('input[name="tiene_trabajo"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const panel = document.getElementById('panelTrabajo');
            if (this.value === 'si') panel.style.display = 'block';
            else panel.style.display = 'none';
        });
    });
    
    // Mostrar/ocultar panel de hijos
    document.querySelectorAll('input[name="tiene_hijos"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const panel = document.getElementById('panelHijos');
            const numHijosInput = document.getElementById('num_hijos');
            if (this.value === '1') {
                panel.style.display = 'flex';
                panel.style.gap = '1rem';
                numHijosInput.value = '';
            } else {
                panel.style.display = 'none';
                numHijosInput.value = '';
                document.getElementById('edades_hijos').value = '';
            }
        });
    });
    
    // Mostrar/ocultar panel de apoyo económico
    document.querySelectorAll('input[name="recibe_apoyo"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const panel = document.getElementById('panelMontoApoyo');
            if (this.value === '1') panel.style.display = 'block';
            else panel.style.display = 'none';
        });
    });
    
    // Mostrar/ocultar panel de problemas de aprendizaje
    document.querySelectorAll('input[name="tiene_problema"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const panel = document.getElementById('panelProblemas');
            if (this.value === '1') panel.style.display = 'block';
            else panel.style.display = 'none';
        });
    });
    
    // Mostrar/ocultar panel de síntomas
    document.querySelectorAll('input[name="tiene_sintomas"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const panel = document.getElementById('panelSintomas');
            if (this.value === '1') panel.style.display = 'block';
            else panel.style.display = 'none';
        });
    });
    
    // Mostrar/ocultar prioridad de contacto
    document.querySelectorAll('input[name="familiar_contacto"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const prioridadContainer = document.getElementById('prioridadContainer');
            const prioridadInput = document.getElementById('familiar_prioridad');
            if (this.value === '1') {
                prioridadContainer.style.display = 'block';
                prioridadInput.required = true;
                prioridadInput.min = 1;
                prioridadInput.value = '';
            } else {
                prioridadContainer.style.display = 'none';
                prioridadInput.required = false;
                prioridadInput.value = '';
            }
        });
    });
    
    // Mostrar/ocultar domicilio según estado de vida
    document.querySelectorAll('input[name="familiar_vive"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const comparteDomicilioSection = document.querySelector('input[name="comparte_domicilio"]')?.closest('.row');
            const domicilioPanel = document.getElementById('panelDomicilioDiferente');
            const comparteSiRadio = document.getElementById('comparteSi');
            const comparteNoRadio = document.getElementById('comparteNo');
            if (this.value === '0') {
                if (comparteDomicilioSection) comparteDomicilioSection.style.display = 'none';
                if (domicilioPanel) domicilioPanel.style.display = 'none';
                limpiarDomicilioFamiliar();
                if (comparteSiRadio) comparteSiRadio.checked = false;
                if (comparteNoRadio) comparteNoRadio.checked = false;
            } else {
                if (comparteDomicilioSection) comparteDomicilioSection.style.display = 'flex';
                if (comparteSiRadio) comparteSiRadio.checked = true;
                if (comparteNoRadio) comparteNoRadio.checked = false;
                if (domicilioPanel) domicilioPanel.style.display = 'none';
            }
        });
    });
    
    // Mostrar/ocultar domicilio diferente
    document.querySelectorAll('input[name="comparte_domicilio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const panel = document.getElementById('panelDomicilioDiferente');
            if (this.value === '0') panel.style.display = 'block';
            else {
                panel.style.display = 'none';
                limpiarDomicilioFamiliar();
            }
        });
    });
    
    // Mostrar/ocultar graduación de anteojos
    document.querySelectorAll('input[name="usa_anteojos"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const container = document.getElementById('graduacionContainer');
            if (this.value === '1') container.style.display = 'block';
            else {
                container.style.display = 'none';
                document.getElementById('graduacion_anteojos').value = '';
            }
        });
    });
    
    // Mostrar/ocultar panel de chat
    document.querySelectorAll('input[name="chatea"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const container = document.getElementById('temasChatContainer');
            if (this.value === '1') container.style.display = 'block';
            else {
                container.style.display = 'none';
                document.getElementById('temas_chat').value = '';
            }
        });
    });

    // Botones flotantes para navegación rápida
    const btnGoToBottom = document.getElementById('btnGoToBottom');
    const btnGoToTop = document.getElementById('btnGoToTop');

    // Función para mostrar/ocultar botones según el scroll
    function toggleFloatingButtons() {
        const scrollPosition = window.scrollY;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        
        // Mostrar botón "ir al final" cuando no estamos en el bottom
        if (scrollPosition + windowHeight < documentHeight - 300) {
            btnGoToBottom.classList.add('visible');
        } else {
            btnGoToBottom.classList.remove('visible');
        }
    }

    // Ir al final de la página
    btnGoToBottom.addEventListener('click', function() {
        window.scrollTo({
            top: document.documentElement.scrollHeight,
            behavior: 'smooth'
        });
    });

    // Escuchar evento de scroll
    window.addEventListener('scroll', toggleFloatingButtons);
    // Llamar una vez al cargar para establecer estado inicial
    setTimeout(toggleFloatingButtons, 100);
});

// Eventos de teclado para navegación
document.getElementById('localidad').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('localidadResults');
    if (resultsDiv.style.display !== 'block' || resultadosLugares.length === 0) return;
    const items = document.querySelectorAll('#localidadResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedIndex = (selectedIndex + 1) % items.length; updateHighlight(items, selectedIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedIndex = (selectedIndex - 1 + items.length) % items.length; updateHighlight(items, selectedIndex); }
    else if (e.key === 'Enter' && selectedIndex >= 0) { e.preventDefault(); seleccionarLugar(resultadosLugares[selectedIndex]); }
});

document.getElementById('colonia').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('coloniaResults');
    if (resultsDiv.style.display !== 'block' || resultadosDomicilios.length === 0) return;
    const items = document.querySelectorAll('#coloniaResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedDomicilioIndex = (selectedDomicilioIndex + 1) % items.length; updateHighlight(items, selectedDomicilioIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedDomicilioIndex = (selectedDomicilioIndex - 1 + items.length) % items.length; updateHighlight(items, selectedDomicilioIndex); }
    else if (e.key === 'Enter' && selectedDomicilioIndex >= 0) { e.preventDefault(); seleccionarDomicilio(resultadosDomicilios[selectedDomicilioIndex]); }
});

document.getElementById('secundaria_nombre').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('secundariaResults');
    if (resultsDiv.style.display !== 'block' || resultadosSecundarias.length === 0) return;
    const items = document.querySelectorAll('#secundariaResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedSecundariaIndex = (selectedSecundariaIndex + 1) % items.length; updateHighlight(items, selectedSecundariaIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedSecundariaIndex = (selectedSecundariaIndex - 1 + items.length) % items.length; updateHighlight(items, selectedSecundariaIndex); }
    else if (e.key === 'Enter' && selectedSecundariaIndex >= 0) { e.preventDefault(); seleccionarSecundaria(resultadosSecundarias[selectedSecundariaIndex]); }
});

document.getElementById('alumno_apellido1').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('apellido1Results');
    if (resultsDiv.style.display !== 'block' || resultadosApellido1.length === 0) return;
    const items = document.querySelectorAll('#apellido1Results .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedApellido1Index = (selectedApellido1Index + 1) % items.length; updateHighlight(items, selectedApellido1Index); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedApellido1Index = (selectedApellido1Index - 1 + items.length) % items.length; updateHighlight(items, selectedApellido1Index); }
    else if (e.key === 'Enter' && selectedApellido1Index >= 0) { e.preventDefault(); seleccionarApellido('apellido1', resultadosApellido1[selectedApellido1Index]); }
});

document.getElementById('alumno_apellido2').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('apellido2Results');
    if (resultsDiv.style.display !== 'block' || resultadosApellido2.length === 0) return;
    const items = document.querySelectorAll('#apellido2Results .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedApellido2Index = (selectedApellido2Index + 1) % items.length; updateHighlight(items, selectedApellido2Index); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedApellido2Index = (selectedApellido2Index - 1 + items.length) % items.length; updateHighlight(items, selectedApellido2Index); }
    else if (e.key === 'Enter' && selectedApellido2Index >= 0) { e.preventDefault(); seleccionarApellido('apellido2', resultadosApellido2[selectedApellido2Index]); }
});

document.getElementById('buscar_grupo').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('grupoResults');
    if (resultsDiv.style.display !== 'block' || resultadosGrupos.length === 0) return;
    const items = document.querySelectorAll('#grupoResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedGrupoIndex = (selectedGrupoIndex + 1) % items.length; updateHighlight(items, selectedGrupoIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedGrupoIndex = (selectedGrupoIndex - 1 + items.length) % items.length; updateHighlight(items, selectedGrupoIndex); }
    else if (e.key === 'Enter' && selectedGrupoIndex >= 0) { e.preventDefault(); seleccionarGrupo(resultadosGrupos[selectedGrupoIndex]); }
});

document.getElementById('parentesco').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('parentescoResults');
    if (resultsDiv.style.display !== 'block' || resultadosParentescos.length === 0) return;
    const items = document.querySelectorAll('#parentescoResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedParentescoIndex = (selectedParentescoIndex + 1) % items.length; updateHighlight(items, selectedParentescoIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedParentescoIndex = (selectedParentescoIndex - 1 + items.length) % items.length; updateHighlight(items, selectedParentescoIndex); }
    else if (e.key === 'Enter' && selectedParentescoIndex >= 0) { e.preventDefault(); seleccionarParentesco(resultadosParentescos[selectedParentescoIndex]); }
});

document.getElementById('familiar_apellido1').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('familiarApellido1Results');
    if (resultsDiv.style.display !== 'block' || resultadosFamiliarApellido1.length === 0) return;
    const items = document.querySelectorAll('#familiarApellido1Results .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedFamiliarApellido1Index = (selectedFamiliarApellido1Index + 1) % items.length; updateHighlight(items, selectedFamiliarApellido1Index); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedFamiliarApellido1Index = (selectedFamiliarApellido1Index - 1 + items.length) % items.length; updateHighlight(items, selectedFamiliarApellido1Index); }
    else if (e.key === 'Enter' && selectedFamiliarApellido1Index >= 0) { e.preventDefault(); seleccionarApellidoFamiliar('apellido1', resultadosFamiliarApellido1[selectedFamiliarApellido1Index]); }
});

document.getElementById('familiar_apellido2').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('familiarApellido2Results');
    if (resultsDiv.style.display !== 'block' || resultadosFamiliarApellido2.length === 0) return;
    const items = document.querySelectorAll('#familiarApellido2Results .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedFamiliarApellido2Index = (selectedFamiliarApellido2Index + 1) % items.length; updateHighlight(items, selectedFamiliarApellido2Index); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedFamiliarApellido2Index = (selectedFamiliarApellido2Index - 1 + items.length) % items.length; updateHighlight(items, selectedFamiliarApellido2Index); }
    else if (e.key === 'Enter' && selectedFamiliarApellido2Index >= 0) { e.preventDefault(); seleccionarApellidoFamiliar('apellido2', resultadosFamiliarApellido2[selectedFamiliarApellido2Index]); }
});

document.getElementById('familiar_colonia').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('familiarColoniaResults');
    if (resultsDiv.style.display !== 'block' || resultadosFamiliarColonias.length === 0) return;
    const items = document.querySelectorAll('#familiarColoniaResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedFamiliarColoniaIndex = (selectedFamiliarColoniaIndex + 1) % items.length; updateHighlight(items, selectedFamiliarColoniaIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedFamiliarColoniaIndex = (selectedFamiliarColoniaIndex - 1 + items.length) % items.length; updateHighlight(items, selectedFamiliarColoniaIndex); }
    else if (e.key === 'Enter' && selectedFamiliarColoniaIndex >= 0) { e.preventDefault(); seleccionarColoniaFamiliar(resultadosFamiliarColonias[selectedFamiliarColoniaIndex]); }
});

document.getElementById('buscarTransporte').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('transporteResults');
    if (resultsDiv.style.display !== 'block' || resultadosTransportes.length === 0) return;
    const items = document.querySelectorAll('#transporteResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedTransporteIndex = (selectedTransporteIndex + 1) % items.length; updateHighlight(items, selectedTransporteIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedTransporteIndex = (selectedTransporteIndex - 1 + items.length) % items.length; updateHighlight(items, selectedTransporteIndex); }
    else if (e.key === 'Enter' && selectedTransporteIndex >= 0) { e.preventDefault(); seleccionarTransporte(resultadosTransportes[selectedTransporteIndex]); }
});

document.getElementById('buscarDeporte').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('deporteResults');
    if (resultsDiv.style.display !== 'block' || resultadosDeportes.length === 0) return;
    const items = document.querySelectorAll('#deporteResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedDeporteIndex = (selectedDeporteIndex + 1) % items.length; updateHighlight(items, selectedDeporteIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedDeporteIndex = (selectedDeporteIndex - 1 + items.length) % items.length; updateHighlight(items, selectedDeporteIndex); }
    else if (e.key === 'Enter' && selectedDeporteIndex >= 0) { e.preventDefault(); seleccionarDeporte(resultadosDeportes[selectedDeporteIndex]); }
});

document.getElementById('estado_civil_padres').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('estadoCivilResults');
    if (resultsDiv.style.display !== 'block' || resultadosEstadoCivil.length === 0) return;
    const items = document.querySelectorAll('#estadoCivilResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedEstadoCivilIndex = (selectedEstadoCivilIndex + 1) % items.length; updateHighlight(items, selectedEstadoCivilIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedEstadoCivilIndex = (selectedEstadoCivilIndex - 1 + items.length) % items.length; updateHighlight(items, selectedEstadoCivilIndex); }
    else if (e.key === 'Enter' && selectedEstadoCivilIndex >= 0) { e.preventDefault(); seleccionarEstadoCivil(resultadosEstadoCivil[selectedEstadoCivilIndex]); }
});

document.getElementById('estado_civil_alumno').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('estadoCivilAlumnoResults');
    if (resultsDiv.style.display !== 'block' || resultadosEstadoCivilAlumno.length === 0) return;
    const items = document.querySelectorAll('#estadoCivilAlumnoResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedEstadoCivilAlumnoIndex = (selectedEstadoCivilAlumnoIndex + 1) % items.length; updateHighlight(items, selectedEstadoCivilAlumnoIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedEstadoCivilAlumnoIndex = (selectedEstadoCivilAlumnoIndex - 1 + items.length) % items.length; updateHighlight(items, selectedEstadoCivilAlumnoIndex); }
    else if (e.key === 'Enter' && selectedEstadoCivilAlumnoIndex >= 0) { e.preventDefault(); seleccionarEstadoCivilAlumno(resultadosEstadoCivilAlumno[selectedEstadoCivilAlumnoIndex]); }
});

document.getElementById('buscarBeca').addEventListener('keydown', function(e) {
    const resultsDiv = document.getElementById('becaResults');
    if (resultsDiv.style.display !== 'block' || resultadosBecas.length === 0) return;
    const items = document.querySelectorAll('#becaResults .search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); selectedBecaIndex = (selectedBecaIndex + 1) % items.length; updateHighlight(items, selectedBecaIndex); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedBecaIndex = (selectedBecaIndex - 1 + items.length) % items.length; updateHighlight(items, selectedBecaIndex); }
    else if (e.key === 'Enter' && selectedBecaIndex >= 0) { e.preventDefault(); seleccionarBeca(resultadosBecas[selectedBecaIndex]); }
    else if (e.key === 'Enter' && selectedBecaIndex === -1 && this.value.trim()) { e.preventDefault(); document.getElementById('btnAgregarBeca').disabled = false; }
});

// Agregar transporte
document.getElementById('btnAgregarTransporte').addEventListener('click', function() {
    const transporteNombre = document.getElementById('buscarTransporte').value.trim();
    if (!transporteNombre) { alert('Ingrese un transporte.'); return; }
    const transporteNormalizado = transporteNombre.charAt(0).toUpperCase() + transporteNombre.slice(1).toLowerCase();
    if (transportesTemporales.some(t => t.toLowerCase() === transporteNormalizado.toLowerCase())) { alert('Este transporte ya está añadido.'); return; }
    transportesTemporales.push(transporteNormalizado);
    renderizarTransportesAsignados();
    document.getElementById('buscarTransporte').value = '';
    document.getElementById('btnAgregarTransporte').disabled = true;
});

// Agregar deporte
document.getElementById('btnAgregarDeporte').addEventListener('click', function() {
    const deporteNombre = document.getElementById('buscarDeporte').value.trim();
    if (!deporteNombre) { alert('Ingrese un deporte.'); return; }
    const deporteNormalizado = deporteNombre.charAt(0).toUpperCase() + deporteNombre.slice(1).toLowerCase();
    if (deportesTemporales.some(d => d.toLowerCase() === deporteNormalizado.toLowerCase())) { alert('Este deporte ya está añadido.'); return; }
    deportesTemporales.push(deporteNormalizado);
    renderizarDeportesAsignados();
    document.getElementById('buscarDeporte').value = '';
    document.getElementById('btnAgregarDeporte').disabled = true;
});

// Agregar beca
document.getElementById('btnAgregarBeca').addEventListener('click', function() {
    const becaId = document.getElementById('becaSeleccionadaId').value;
    const becaNombre = document.getElementById('buscarBeca').value.trim();
    const estatus = document.getElementById('estatusBecaSelect').value;
    if (!becaNombre) { alert('Ingrese el nombre de la beca.'); return; }
    if (becasTemporales.some(b => b.nombre.toLowerCase() === becaNombre.toLowerCase())) { alert('Esta beca ya está asignada.'); return; }
    const becaParaAgregar = { id: becaId || 'new_' + Date.now(), nombre: becaNombre, activa: estatus === '1' };
    fetch('{{ route("alumnos.agregar-beca-temporal") }}', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ id_beca: becaParaAgregar.id, nombre_beca: becaParaAgregar.nombre, activa: becaParaAgregar.activa })
    }).then(response => response.json()).then(data => {
        if (data.success) {
            becasTemporales = data.becas;
            renderizarBecasAsignadas();
            document.getElementById('buscarBeca').value = '';
            document.getElementById('becaSeleccionadaId').value = '';
            document.getElementById('btnAgregarBeca').disabled = true;
            document.getElementById('estatusBecaSelect').value = '1';
        } else { alert('Error: ' + (data.message || 'No se pudo agregar la beca')); }
    });
});

// Agregar familiar
document.getElementById('btnAgregarFamiliar').addEventListener('click', function() {
    if (!validarFamiliarAntesDeAgregar()) return;
    const fecha = obtenerFechaFamiliar();
    if (!fecha) return;
    const familiarVive = document.querySelector('input[name="familiar_vive"]:checked')?.value === '1';
    let comparteDomicilio = false, id_domicilio = null, domicilioData = null;
    if (familiarVive) {
        comparteDomicilio = document.querySelector('input[name="comparte_domicilio"]:checked')?.value === '1';
        const domicilioIdGuardado = document.getElementById('domicilioId').value;
        if (comparteDomicilio) {
            if (domicilioIdGuardado && domicilioIdGuardado !== '') id_domicilio = domicilioIdGuardado;
            else {
                domicilioData = { calle: document.getElementById('calle').value, num_ext: document.getElementById('num_ext').value, num_int: document.getElementById('num_int').value || null, colonia: document.getElementById('colonia').value, localidad: document.getElementById('domicilio_localidad').value, municipio: document.getElementById('domicilio_municipio').value, cp: document.getElementById('cp').value, estado: document.getElementById('domicilio_estado').value, telefono_domicilio: document.getElementById('telefono_domicilio').value || null };
                if (!domicilioData.calle || !domicilioData.num_ext || !domicilioData.colonia || !domicilioData.localidad || !domicilioData.municipio || !domicilioData.cp || !domicilioData.estado) { alert('Complete todos los campos del domicilio del alumno antes de añadir un familiar que comparte domicilio.'); return; }
            }
        } else {
            domicilioData = { calle: document.getElementById('familiar_calle').value, num_ext: document.getElementById('familiar_num_ext').value, num_int: document.getElementById('familiar_num_int').value || null, colonia: document.getElementById('familiar_colonia').value, localidad: document.getElementById('familiar_localidad').value, municipio: document.getElementById('familiar_municipio').value, cp: document.getElementById('familiar_cp').value, estado: document.getElementById('familiar_estado').value, telefono_domicilio: document.getElementById('familiar_telefono_domicilio').value || null };
            if (!domicilioData.calle || !domicilioData.num_ext || !domicilioData.colonia || !domicilioData.localidad || !domicilioData.municipio || !domicilioData.cp || !domicilioData.estado) { alert('Complete todos los campos del domicilio.'); return; }
        }
    }
    const esContacto = document.querySelector('input[name="familiar_contacto"]:checked')?.value === '1';
    const prioridad = document.getElementById('familiar_prioridad').value;
    if (familiarEnEdicion) { eliminarFamiliarTemporal(familiarEnEdicion); familiarEnEdicion = null; }
    const data = {
        parentesco: document.getElementById('parentesco').value, vive: familiarVive, nombre: document.getElementById('familiar_nombre').value,
        apellido1: document.getElementById('familiar_apellido1').value, apellido2: document.getElementById('familiar_apellido2').value || null,
        fecha_nacimiento: fecha, telefono_celular: document.getElementById('familiar_telefono').value || null,
        escolaridad: document.getElementById('familiar_escolaridad').value, ocupacion: document.getElementById('familiar_ocupacion').value || null,
        lugar_trabajo: document.getElementById('familiar_lugar_trabajo').value || null, horario_laboral: document.getElementById('familiar_horario_laboral').value || null,
        domicilio_trabajo: document.getElementById('familiar_domicilio_trabajo').value || null, telefono_trabajo: document.getElementById('familiar_telefono_trabajo').value || null,
        comparte_domicilio: comparteDomicilio, id_domicilio: id_domicilio, es_tutor: document.querySelector('input[name="familiar_tutor"]:checked')?.value === '1',
        es_contacto: esContacto, prioridad_contacto: prioridad || null, domicilio_data: domicilioData
    };
    fetch('{{ route("alumnos.agregar-familiar-temporal") }}', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(data)
    }).then(response => response.json()).then(data => {
        if (data.success) { familiaresTemporales = data.familiares; renderizarFamiliaresAsignados(); limpiarFormularioFamiliar(); }
        else { alert('Error: ' + data.message); }
    });
});

// Permitir agregar con Enter
document.getElementById('buscarDeporte').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); const btn = document.getElementById('btnAgregarDeporte'); if (!btn.disabled && this.value.trim()) btn.click(); }
});

// Ocultar resultados al hacer clic fuera
document.addEventListener('click', function(e) {
    const localidadInput = document.getElementById('localidad'); const localidadResults = document.getElementById('localidadResults');
    if (localidadInput && localidadResults && !localidadInput.contains(e.target) && !localidadResults.contains(e.target)) localidadResults.style.display = 'none';
    const coloniaInput = document.getElementById('colonia'); const coloniaResults = document.getElementById('coloniaResults');
    if (coloniaInput && coloniaResults && !coloniaInput.contains(e.target) && !coloniaResults.contains(e.target)) coloniaResults.style.display = 'none';
    const secundariaInput = document.getElementById('secundaria_nombre'); const secundariaResults = document.getElementById('secundariaResults');
    if (secundariaInput && secundariaResults && !secundariaInput.contains(e.target) && !secundariaResults.contains(e.target)) secundariaResults.style.display = 'none';
    const apellido1Input = document.getElementById('alumno_apellido1'); const apellido1Results = document.getElementById('apellido1Results');
    if (apellido1Input && apellido1Results && !apellido1Input.contains(e.target) && !apellido1Results.contains(e.target)) apellido1Results.style.display = 'none';
    const apellido2Input = document.getElementById('alumno_apellido2'); const apellido2Results = document.getElementById('apellido2Results');
    if (apellido2Input && apellido2Results && !apellido2Input.contains(e.target) && !apellido2Results.contains(e.target)) apellido2Results.style.display = 'none';
    const grupoInput = document.getElementById('buscar_grupo'); const grupoResults = document.getElementById('grupoResults');
    if (grupoInput && grupoResults && !grupoInput.contains(e.target) && !grupoResults.contains(e.target)) grupoResults.style.display = 'none';
    const parentescoInput = document.getElementById('parentesco'); const parentescoResults = document.getElementById('parentescoResults');
    if (parentescoInput && parentescoResults && !parentescoInput.contains(e.target) && !parentescoResults.contains(e.target)) parentescoResults.style.display = 'none';
    const familiarApellido1Input = document.getElementById('familiar_apellido1'); const familiarApellido1Results = document.getElementById('familiarApellido1Results');
    if (familiarApellido1Input && familiarApellido1Results && !familiarApellido1Input.contains(e.target) && !familiarApellido1Results.contains(e.target)) familiarApellido1Results.style.display = 'none';
    const familiarApellido2Input = document.getElementById('familiar_apellido2'); const familiarApellido2Results = document.getElementById('familiarApellido2Results');
    if (familiarApellido2Input && familiarApellido2Results && !familiarApellido2Input.contains(e.target) && !familiarApellido2Results.contains(e.target)) familiarApellido2Results.style.display = 'none';
    const familiarColoniaInput = document.getElementById('familiar_colonia'); const familiarColoniaResults = document.getElementById('familiarColoniaResults');
    if (familiarColoniaInput && familiarColoniaResults && !familiarColoniaInput.contains(e.target) && !familiarColoniaResults.contains(e.target)) familiarColoniaResults.style.display = 'none';
    const transporteInput = document.getElementById('buscarTransporte'); const transporteResults = document.getElementById('transporteResults');
    if (transporteInput && transporteResults && !transporteInput.contains(e.target) && !transporteResults.contains(e.target)) transporteResults.style.display = 'none';
    const estadoCivilInput = document.getElementById('estado_civil_padres'); const estadoCivilResults = document.getElementById('estadoCivilResults');
    if (estadoCivilInput && estadoCivilResults && !estadoCivilInput.contains(e.target) && !estadoCivilResults.contains(e.target)) estadoCivilResults.style.display = 'none';
    const estadoCivilAlumnoInput = document.getElementById('estado_civil_alumno'); const estadoCivilAlumnoResults = document.getElementById('estadoCivilAlumnoResults');
    if (estadoCivilAlumnoInput && estadoCivilAlumnoResults && !estadoCivilAlumnoInput.contains(e.target) && !estadoCivilAlumnoResults.contains(e.target)) estadoCivilAlumnoResults.style.display = 'none';
    const deporteInput = document.getElementById('buscarDeporte'); const deporteResults = document.getElementById('deporteResults');
    if (deporteInput && deporteResults && !deporteInput.contains(e.target) && !deporteResults.contains(e.target)) deporteResults.style.display = 'none';
    const becaInput = document.getElementById('buscarBeca'); const becaResults = document.getElementById('becaResults');
    if (becaInput && becaResults && !becaInput.contains(e.target) && !becaResults.contains(e.target)) becaResults.style.display = 'none';
});

// Configurar toggles de salud
function setupToggleContainer(radioName, containerId, textareaId, counterId) {
    document.querySelectorAll(`input[name="${radioName}"]`).forEach(radio => {
        radio.addEventListener('change', function() {
            const container = document.getElementById(containerId);
            if (this.value === '1') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
                if (textareaId) document.getElementById(textareaId).value = '';
                if (counterId) document.getElementById(counterId).textContent = '0';
            }
        });
    });
}

setupToggleContainer('tiene_cirugia', 'cirugiaContainer', 'cirugia', 'cirugiaCounter');
setupToggleContainer('tiene_alergia', 'alergiaContainer', 'alergia', 'alergiaCounter');
setupToggleContainer('tiene_limitante', 'limitanteContainer', 'limitante_fisico', 'limitanteCounter');
setupToggleContainer('tiene_auditivo', 'auditivoContainer', 'problema_auditivo', 'auditivoCounter');
setupToggleContainer('tiene_adiccion', 'adiccionContainer', 'adiccion', 'adiccionCounter');
setupToggleContainer('tiene_emocional', 'emocionalContainer', 'padecimiento_emocional', 'emocionalCounter');
setupToggleContainer('tiene_medicamento', 'medicamentoContainer', 'medicamento_controlado', 'medicamentoCounter');
setupToggleContainer('tiene_alergia_med', 'alergiaMedContainer', 'alergia_medicamento', 'alergiaMedCounter');
setupToggleContainer('tiene_hospitalizacion', 'hospitalizacionContainer', 'motivo_hospitalizacion', 'hospitalizacionCounter');

// Función para hijos
function actualizarEdadesHijos() {
    const numHijos = parseInt(document.getElementById('num_hijos').value) || 0;
    const edadesInput = document.getElementById('edades_hijos');
    const edadesActuales = edadesInput.value.split(',').map(e => parseInt(e.trim())).filter(e => !isNaN(e));
    if (edadesActuales.length > numHijos) edadesInput.value = edadesActuales.slice(0, numHijos).join(', ');
}
function validarEdades(valor) { document.getElementById('edades_hijos').value = valor.replace(/[^0-9,\s]/g, ''); }

// ===========================================
// BOTÓN DE REGISTRAR
// ===========================================

document.getElementById('btnRegistrarNuevoAlumno').addEventListener('click', async function() {
    try {
        if (!validarFormularioCompleto()) return;
        
        const numControl = document.getElementById('num_control').value;
        if (numControl && numControl.trim() !== '') {
            const disponible = await validarNumeroControl(numControl);
            if (!disponible) {
                alert('El número de control ingresado ya está registrado. Por favor, verifique el dato.');
                const section1 = document.getElementById('section1');
                if (section1 && !section1.classList.contains('expanded')) expandSectionWithScroll('section1');
                const numControlField = document.getElementById('num_control');
                setTimeout(() => { numControlField.scrollIntoView({ behavior: 'smooth', block: 'center' }); resaltarCampo(numControlField); }, 200);
                return;
            }
        }
        
        const lugarData = {
            localidad: document.getElementById('localidad').value, municipio: document.getElementById('municipio').value,
            estado: document.getElementById('estado').value, pais: document.getElementById('pais').value
        };
        if (!lugarData.localidad || !lugarData.municipio || !lugarData.estado || !lugarData.pais) { alert('Complete todos los campos de Lugar de Nacimiento.'); return; }
        
        const domicilioData = {
            calle: document.getElementById('calle').value, num_ext: document.getElementById('num_ext').value,
            num_int: document.getElementById('num_int').value || null, colonia: document.getElementById('colonia').value,
            localidad: document.getElementById('domicilio_localidad').value, municipio: document.getElementById('domicilio_municipio').value,
            cp: document.getElementById('cp').value, estado: document.getElementById('domicilio_estado').value,
            telefono_domicilio: document.getElementById('telefono_domicilio').value || null
        };
        if (!domicilioData.calle || !domicilioData.num_ext || !domicilioData.colonia || !domicilioData.localidad || !domicilioData.municipio || !domicilioData.cp || !domicilioData.estado) { alert('Complete todos los campos obligatorios de Domicilio.'); return; }
        
        const secundariaData = {
            nombre: document.getElementById('secundaria_nombre').value, tipo: document.getElementById('secundaria_tipo').value,
            localidad: document.getElementById('secundaria_localidad').value, municipio: document.getElementById('secundaria_municipio').value,
            estado: document.getElementById('secundaria_estado').value, pais: document.getElementById('secundaria_pais').value
        };
        if (!secundariaData.nombre || !secundariaData.tipo || !secundariaData.localidad || !secundariaData.municipio || !secundariaData.estado || !secundariaData.pais) { alert('Complete todos los campos de Secundaria de Procedencia.'); return; }
        
        if (!document.getElementById('alumno_nombre').value || !document.getElementById('alumno_apellido1').value) { alert('Complete los campos obligatorios: Nombre(s) y Primer Apellido.'); return; }
        
        const lugarResponse = await fetch('{{ route("alumnos.guardar-lugar") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(lugarData) });
        const lugarResult = await lugarResponse.json();
        if (!lugarResult.success) throw new Error('Error al guardar lugar de nacimiento');
        document.getElementById('lugarNacimientoId').value = lugarResult.lugar.id;
        
        const domicilioResponse = await fetch('{{ route("alumnos.guardar-domicilio") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(domicilioData) });
        const domicilioResult = await domicilioResponse.json();
        if (!domicilioResult.success) throw new Error('Error al guardar domicilio');
        document.getElementById('domicilioId').value = domicilioResult.domicilio.id;
        
        const secundariaResponse = await fetch('{{ route("alumnos.guardar-secundaria") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(secundariaData) });
        const secundariaResult = await secundariaResponse.json();
        if (!secundariaResult.success) throw new Error('Error al guardar secundaria');
        document.getElementById('secundariaId').value = secundariaResult.secundaria.id;
        
        const alumnoData = {
            nombre: document.getElementById('alumno_nombre').value, apellido1: document.getElementById('alumno_apellido1').value,
            apellido2: document.getElementById('alumno_apellido2').value || null, num_control: document.getElementById('num_control').value || null,
            telefono_celular: document.getElementById('telefono_celular').value || null, email_personal: document.getElementById('email_personal').value || null,
            email_institucional: document.getElementById('email_institucional').value || null, curp: document.getElementById('curp').value || null,
            nss: document.getElementById('nss').value || null, id_lugar_nacimiento: document.getElementById('lugarNacimientoId').value || null,
            id_domicilio: document.getElementById('domicilioId').value || null, id_secundaria_procedencia: document.getElementById('secundariaId').value || null,
            id_grupo: document.getElementById('id_grupo').value || null
        };
        const alumnoResponse = await fetch('{{ route("alumnos.guardar-alumno") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(alumnoData) });
        const alumnoResult = await alumnoResponse.json();
        
        // Guardar becas
        if (becasTemporales.length > 0) {
            for (const beca of becasTemporales) {
                let idBecaReal = beca.id;
                if (typeof idBecaReal === 'string' && idBecaReal.startsWith('new_')) {
                    const nuevaBecaResponse = await fetch('{{ route("becas.guardar") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ tipo_beca: beca.nombre, descripcion: 'Creada desde registro de alumno' }) });
                    const nuevaBecaResult = await nuevaBecaResponse.json();
                    if (nuevaBecaResult.success) idBecaReal = nuevaBecaResult.beca.id;
                    else continue;
                }
                await fetch('{{ route("alumnos.guardar-beca-alumno") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ id_alumno: alumnoResult.alumno.id, id_beca: idBecaReal, activa: beca.activa }) });
            }
        }
        
        if (familiaresTemporales.length === 0) { alert('Debe añadir al menos un familiar para poder registrar al alumno.'); return; }
        const tieneFamiliarVivo = familiaresTemporales.some(f => f.familiar.vive === true || f.familiar.vive === 1 || f.familiar.vive === '1');
        if (!tieneFamiliarVivo) { alert('Debe añadir al menos un familiar que esté vivo para poder registrar al alumno.'); return; }
        
        // Guardar resto de secciones (2.2, 3, 4, 5, 6)
        const serviciosSeleccionados = [];
        if (document.getElementById('servicioLuz').checked) serviciosSeleccionados.push(1);
        if (document.getElementById('servicioAgua').checked) serviciosSeleccionados.push(2);
        if (document.getElementById('servicioDrenaje').checked) serviciosSeleccionados.push(3);
        if (document.getElementById('servicioAlumbrado').checked) serviciosSeleccionados.push(4);
        
        const datosFamiliaresData = {
            id_alumno: alumnoResult.alumno.id, estado_civil_padres: document.getElementById('estado_civil_padres').value || null,
            ingreso_familiar_aprox: document.getElementById('ingreso_familiar').value ? parseInt(document.getElementById('ingreso_familiar').value) : null,
            gasto_familiar_aprox: document.getElementById('gasto_familiar').value ? parseInt(document.getElementById('gasto_familiar').value) : null,
            casa_propia: document.querySelector('input[name="casa_propia"]:checked')?.value === '1',
            auto_propio: document.querySelector('input[name="auto_propio"]:checked')?.value === '1',
            servicios: serviciosSeleccionados
        };
        if (datosFamiliaresData.estado_civil_padres || datosFamiliaresData.ingreso_familiar_aprox || datosFamiliaresData.gasto_familiar_aprox || serviciosSeleccionados.length > 0) {
            await fetch('{{ route("alumnos.guardar-datos-familiares") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(datosFamiliaresData) });
        }
        
        const infoSocioecoData = {
            id_alumno: alumnoResult.alumno.id, auto_propio_alumno: document.querySelector('input[name="auto_propio_alumno"]:checked')?.value === '1',
            transportes: transportesTemporales.length > 0 ? transportesTemporales : null,
            traslado_horas: document.getElementById('traslado_horas').value ? parseInt(document.getElementById('traslado_horas').value) : null,
            traslado_minutos: document.getElementById('traslado_minutos').value ? parseInt(document.getElementById('traslado_minutos').value) : null,
            estado_civil_alumno: document.getElementById('estado_civil_alumno').value || null,
            num_hijos: document.getElementById('hijosSi').checked ? (parseInt(document.getElementById('num_hijos').value) || 0) : 0,
            edades_hijos: document.getElementById('hijosSi').checked ? document.getElementById('edades_hijos').value || null : null,
            monto_apoyo: document.getElementById('apoyoSi').checked ? (parseInt(document.getElementById('monto_apoyo').value) || null) : null,
            gasto_comida_transporte: document.getElementById('gasto_comida_transporte').value ? parseInt(document.getElementById('gasto_comida_transporte').value) : null,
            comidas_diarias: parseInt(document.getElementById('comidas_diarias').value) || 3
        };
        await fetch('{{ route("alumnos.guardar-info-socioeco") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(infoSocioecoData) });
        
        const dispositivosSeleccionados = [];
        if (document.getElementById('dispositivoCelular').checked) dispositivosSeleccionados.push(1);
        if (document.getElementById('dispositivoComputadora').checked) dispositivosSeleccionados.push(2);
        if (document.getElementById('dispositivoInternet').checked) dispositivosSeleccionados.push(3);
        if (document.getElementById('dispositivoTablet').checked) dispositivosSeleccionados.push(4);
        const datosAcademicosData = { id_alumno: alumnoResult.alumno.id, dispositivos: dispositivosSeleccionados, otro_dispositivo: document.getElementById('otro_dispositivo').value || null };
        if (dispositivosSeleccionados.length > 0 || datosAcademicosData.otro_dispositivo) {
            await fetch('{{ route("alumnos.guardar-datos-academicos") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(datosAcademicosData) });
        }
        
        const tieneProblema = document.getElementById('problemaSi').checked;
        const caracteristicasSeleccionadas = [];
        if (tieneProblema) {
            const caracteristicasIds = ['problemaDislexia', 'problemaVisuales', 'problemaAuditivo', 'problemaNoEntender', 'problemaLentitud', 'problemaLenguaje', 'problemaComunicacion', 'problemaDiscriminacion', 'problemaGestos', 'problemaDistraccion', 'problemaTDAH', 'problemaDiscapacidad'];
            caracteristicasIds.forEach((id, index) => { if (document.getElementById(id).checked) caracteristicasSeleccionadas.push(index + 1); });
        }
        const problemaAprendizajeData = { id_alumno: alumnoResult.alumno.id, tiene_problema: tieneProblema, caracteristicas: caracteristicasSeleccionadas, otro_problema: document.getElementById('otro_problema').value || null };
        await fetch('{{ route("alumnos.guardar-problema-aprendizaje") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(problemaAprendizajeData) });
        
        const sintomasSeleccionados = [];
        if (document.getElementById('sintomasSi').checked) {
            if (document.getElementById('sintomaAnsiedad').checked) sintomasSeleccionados.push(1);
            if (document.getElementById('sintomaEstres').checked) sintomasSeleccionados.push(2);
            if (document.getElementById('sintomaDepresion').checked) sintomasSeleccionados.push(3);
            if (document.getElementById('sintomaCulpa').checked) sintomasSeleccionados.push(4);
            if (document.getElementById('sintomaTristeza').checked) sintomasSeleccionados.push(5);
        }
        const datosSaludData = {
            id_alumno: alumnoResult.alumno.id, estatura: parseInt(document.getElementById('estatura').value) || 0,
            peso: parseFloat(document.getElementById('peso').value) || 0, tipo_sangre: document.getElementById('tipo_sangre').value,
            cuadro_basico_vacunas: document.getElementById('vacunasSi').checked, usa_anteojos: document.getElementById('anteojosSi').checked,
            graduacion_anteojos: document.getElementById('anteojosSi').checked ? (parseFloat(document.getElementById('graduacion_anteojos').value) || null) : null,
            tiene_cirugia: document.getElementById('cirugiaSi').checked, cirugia: document.getElementById('cirugiaSi').checked ? document.getElementById('cirugia').value || null : null,
            tiene_alergia: document.getElementById('alergiaSi').checked, alergia: document.getElementById('alergiaSi').checked ? document.getElementById('alergia').value || null : null,
            tiene_limitante: document.getElementById('limitanteSi').checked, limitante_fisico: document.getElementById('limitanteSi').checked ? document.getElementById('limitante_fisico').value || null : null,
            tiene_auditivo: document.getElementById('auditivoSi').checked, problema_auditivo: document.getElementById('auditivoSi').checked ? document.getElementById('problema_auditivo').value || null : null,
            tiene_adiccion: document.getElementById('adiccionSi').checked, adiccion: document.getElementById('adiccionSi').checked ? document.getElementById('adiccion').value || null : null,
            tiene_emocional: document.getElementById('emocionalSi').checked, padecimiento_emocional: document.getElementById('emocionalSi').checked ? document.getElementById('padecimiento_emocional').value || null : null,
            enfermedad_actual: document.getElementById('enfermedad_actual').value || null, tiene_sintomas: document.getElementById('sintomasSi').checked,
            sintomas: sintomasSeleccionados, otro_sintoma: document.getElementById('sintomasSi').checked ? document.getElementById('otro_sintoma').value || null : null,
            tiene_medicamento: document.getElementById('medicamentoSi').checked, medicamento_controlado: document.getElementById('medicamentoSi').checked ? document.getElementById('medicamento_controlado').value || null : null,
            tiene_alergia_med: document.getElementById('alergiaMedSi').checked, alergia_medicamento: document.getElementById('alergiaMedSi').checked ? document.getElementById('alergia_medicamento').value || null : null,
            tiene_hospitalizacion: document.getElementById('hospitalizacionSi').checked, motivo_hospitalizacion: document.getElementById('hospitalizacionSi').checked ? document.getElementById('motivo_hospitalizacion').value || null : null,
            diabetes: document.getElementById('diabetesSi').checked, hipertension: document.getElementById('hipertensionSi').checked,
            dolores_cabeza: document.getElementById('cabezaSi').checked, dolores_estomago: document.getElementById('estomagoSi').checked,
            frecuencia_medico: obtenerFrecuencia('medico'), frecuencia_dentista: obtenerFrecuencia('dentista')
        };
        await fetch('{{ route("alumnos.guardar-datos-salud") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(datosSaludData) });
        
        const actividadesData = {
            id_alumno: alumnoResult.alumno.id, pasatiempo_favorito: document.getElementById('pasatiempo_favorito').value || null,
            horas_pasatiempo: document.getElementById('horas_pasatiempo').value ? parseInt(document.getElementById('horas_pasatiempo').value) : null,
            deportes: deportesTemporales.length > 0 ? deportesTemporales : null,
            horas_deporte: document.getElementById('horas_deporte').value ? parseInt(document.getElementById('horas_deporte').value) : null,
            horas_tv: document.getElementById('horas_tv').value ? parseInt(document.getElementById('horas_tv').value) : null,
            horas_compu: document.getElementById('horas_compu').value ? parseInt(document.getElementById('horas_compu').value) : null,
            uso_compu: document.getElementById('uso_compu').value || null, chatea: document.getElementById('chatearSi').checked,
            temas_chat: document.getElementById('chatearSi').checked ? document.getElementById('temas_chat').value || null : null
        };
        await fetch('{{ route("alumnos.guardar-actividades-recreativas") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(actividadesData) });
        
        if (alumnoResult.success) {
            if (alumnoResult.id_domicilio) document.getElementById('domicilioId').value = alumnoResult.id_domicilio;
            if (document.getElementById('trabajoSi').checked) {
                const trabajoData = {
                    id_alumno: alumnoResult.alumno.id, lugar_trabajo: document.getElementById('lugar_trabajo').value || null,
                    horario_laboral: document.getElementById('horario_laboral').value || null,
                    domicilio_trabajo: document.getElementById('domicilio_trabajo').value || null,
                    telefono_trabajo: document.getElementById('telefono_trabajo').value || null
                };
                if (trabajoData.lugar_trabajo || trabajoData.horario_laboral || trabajoData.domicilio_trabajo || trabajoData.telefono_trabajo) {
                    await fetch('{{ route("alumnos.guardar-trabajo") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(trabajoData) });
                }
            }
            await fetch('{{ route("alumnos.limpiar-becas-temporales") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            familiaresTemporales = []; becasTemporales = []; transportesTemporales = []; deportesTemporales = [];
            renderizarFamiliaresAsignados(); renderizarBecasAsignadas(); renderizarTransportesAsignados(); renderizarDeportesAsignados();
            alert('Alumno registrado correctamente. Foto generada: ' + alumnoResult.foto_generada);
            limpiarFormulario();
            // Scroll suave al inicio de la página
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else { throw new Error(alumnoResult.message || 'Error al guardar alumno'); }
    } catch (error) { console.error('Error:', error); alert('Error al guardar los datos: ' + error.message); }
});
</script>
@endpush