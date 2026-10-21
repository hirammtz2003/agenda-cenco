@extends('layouts.app')

@section('title', 'Horario General - SADHCC')

@php
    $breadcrumbs = [
        ['name' => 'Inicio', 'url' => route('welcome'), 'icon' => 'fa-home'],
        ['name' => 'Horario General', 'url' => null, 'icon' => 'fa-calendar-week']
    ];
@endphp

@push('styles')
<style>
    .module-section {
        background-color: #f8f9fa;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .cuadricula {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        background: white;
    }
    .cuadricula th, .cuadricula td {
        border: 1px solid #dee2e6;
        text-align: center;
        vertical-align: middle;
        padding: 0.5rem 0.25rem;
        font-size: 0.78rem;
    }
    .cuadricula thead th {
        background-color: #212529;
        color: white;
        font-weight: 600;
    }
    .cuadricula thead th.mes-header {
        background-color: #495057;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    .cuadricula tbody td.col-hora {
        background-color: #e9ecef;
        font-weight: 600;
        width: 110px;
        font-size: 0.75rem;
    }
    .cuadricula tbody td.celda {
        height: 70px;
    }
    .celda-ocupada {
        background-color: #d1e7dd;
        border-left: 4px solid #198754;
        text-align: left !important;
        padding: 0.4rem 0.5rem !important;
    }
    .celda-vacia {
        background-color: #ffffff;
    }
    .celda-inhabil {
        background-color: #adb5bd;
        color: #fff;
        font-weight: 600;
    }
    .celda-inhabil::after {
        content: "Inhábil";
    }
    .celda-ocupada .info-docente {
        font-weight: 600;
        font-size: 0.75rem;
        color: #0f5132;
    }
    .celda-ocupada .info-materia {
        font-size: 0.72rem;
        color: #495057;
        font-style: italic;
    }
    .celda-ocupada .info-grupo {
        font-size: 0.68rem;
        color: #6c757d;
    }
    .celda-reservada {
        background-color: #adb5bd;
        color: white;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .fila-receso td {
        background-color: #f8d7da;
        color: #842029;
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 0.85rem;
        height: 30px;
    }
    .nav-semana {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-top: 1rem;
    }
    .nav-semana button {
        min-width: 40px;
    }
    .nav-semana .rango {
        font-weight: 600;
        color: #495057;
    }
    .sin-labs {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    @include('partials.breadcrumb')

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="border-bottom pb-3">
                <i class="fas fa-calendar-week me-2 text-dark"></i>
                HORARIO GENERAL
            </h2>
        </div>
    </div>

    <div id="contenedorHorarios">
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
            <p class="text-muted">Cargando horarios...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const contenedor = document.getElementById('contenedorHorarios');
    const semanasPorLab = {}; // { idLab: 'YYYY-MM-DD' (lunes de la semana mostrada) }

    // ===== CARGA INICIAL =====
    // Traemos la semana actual y todos los labs juntos
    cargarSemanaTodos();

    function cargarSemanaTodos(fecha = null) {
        const params = new URLSearchParams();
        if (fecha) params.append('fecha', fecha);

        contenedor.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                <p class="text-muted">Cargando horarios...</p>
            </div>`;

        fetch(`{{ route("admin.horario-general.datos") }}?${params}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Guardar la semana base para cada lab
                data.laboratorios.forEach(item => {
                    semanasPorLab[item.laboratorio.id] = data.semana.lunes;
                });
                render(data);
            } else {
                contenedor.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${data.message || 'Error al cargar los horarios.'}
                    </div>`;
            }
        })
        .catch(e => {
            console.error(e);
            contenedor.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error de conexión.
                </div>`;
        });
    }

    // ===== RECARGA INDIVIDUAL POR LAB =====
    function recargarLaboratorio(idLab, fecha) {
        const params = new URLSearchParams();
        if (fecha) params.append('fecha', fecha);

        fetch(`{{ route("admin.horario-general.datos") }}?${params}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const item = data.laboratorios.find(l => l.laboratorio.id === idLab);
                if (!item) return;

                semanasPorLab[idLab] = data.semana.lunes;

                // Reemplazar solo el module-section de ese lab
                const section = document.getElementById(`lab-section-${idLab}`);
                if (section) {
                    section.outerHTML = renderLaboratorio(item, data);
                    bindEventosLab(idLab, data);
                }
            }
        })
        .catch(e => console.error(e));
    }

    // ===== RENDER GLOBAL =====
    function render(data) {
        if (!data.laboratorios || data.laboratorios.length === 0) {
            contenedor.innerHTML = `
                <div class="module-section">
                    <div class="sin-labs">
                        <i class="fas fa-flask fa-3x mb-3"></i>
                        <p>No hay laboratorios registrados.</p>
                    </div>
                </div>`;
            return;
        }

        let html = '';
        data.laboratorios.forEach(item => {
            html += renderLaboratorio(item, data);
        });
        contenedor.innerHTML = html;

        // Bind eventos
        data.laboratorios.forEach(item => {
            bindEventosLab(item.laboratorio.id, data);
        });
    }

    // ===== RENDER UN LAB =====
    function renderLaboratorio(item, data) {
        const lab = item.laboratorio;
        const cuad = item.cuadricula;

        // Headers
        let headers = `
            <tr>
                <th class="mes-header" colspan="6">${data.semana.mesHeader}</th>
            </tr>
            <tr>
                <th>Hora</th>
                ${data.dias.map(d => `<th>${d.nombre} ${d.numero}</th>`).join('')}
            </tr>
        `;

        // Body
        let body = '';
        for (let h = 1; h <= 8; h++) {
            const fila = cuad[h];

            if (h === 4) {
                body += `<tr class="fila-receso"><td colspan="6">RECESO</td></tr>`;
            }

            body += `<tr>
                <td class="col-hora">${horaLegible(h)}</td>
                ${data.dias.map(d => renderCelda(fila[d.fecha])).join('')}
            </tr>`;
        }

        return `
            <div class="module-section" id="lab-section-${lab.id}">
                <h5 class="mb-3">
                    <i class="fas fa-flask me-2 text-info"></i>
                    ${lab.nombre}
                </h5>
                <div class="table-responsive">
                    <table class="cuadricula">
                        <thead>${headers}</thead>
                        <tbody>${body}</tbody>
                    </table>
                </div>
                <div class="nav-semana">
                    <button type="button" class="btn btn-outline-primary" data-nav="anterior" data-lab="${lab.id}">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="rango">${formatearRango(data.semana.lunes, data.semana.viernes)}</span>
                    <button type="button" class="btn btn-outline-primary" data-nav="siguiente" data-lab="${lab.id}">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ms-3" data-nav="semana-actual" data-lab="${lab.id}">
                        <i class="fas fa-calendar-day me-1"></i>Volver a semana actual
                    </button>
                </div>
            </div>
        `;
    }

    // ===== BIND EVENTOS DE UN LAB =====
    function bindEventosLab(idLab, dataActual) {
        const section = document.getElementById(`lab-section-${idLab}`);
        if (!section) return;

        section.querySelectorAll('[data-nav]').forEach(btn => {
            btn.addEventListener('click', function() {
                const accion = this.dataset.nav;
                const fechaBase = semanasPorLab[idLab];

                if (accion === 'semana-actual') {
                    recargarLaboratorio(idLab, null);
                    return;
                }

                const nueva = new Date(fechaBase + 'T12:00:00');
                nueva.setDate(nueva.getDate() + (accion === 'siguiente' ? 7 : -7));
                const yyyy = nueva.getFullYear();
                const mm = String(nueva.getMonth() + 1).padStart(2, '0');
                const dd = String(nueva.getDate()).padStart(2, '0');
                recargarLaboratorio(idLab, `${yyyy}-${mm}-${dd}`);
            });
        });
    }

    // ===== RENDER CELDA =====
    function renderCelda(celda) {
        if (!celda) return `<td class="celda celda-vacia"></td>`;

        if (celda.estado === 'inhabil') {
            return `<td class="celda celda-inhabil"></td>`;
        }

        if (celda.estado === 'reservada') {
            return `<td class="celda celda-reservada">Reservada</td>`;
        }

        if (celda.estado === 'ocupado') {
            return `
                <td class="celda celda-ocupada">
                    <div class="info-docente">${celda.docente || '—'}</div>
                    <div class="info-materia">${celda.materia || '—'}</div>
                    <div class="info-grupo">${celda.grupo || '—'}</div>
                </td>`;
        }

        return `<td class="celda celda-vacia"></td>`;
    }

    // ===== HELPERS =====
    function horaLegible(h) {
        const horas = {
            1: '1 (8:00-8:50)', 2: '2 (8:50-9:40)', 3: '3 (9:40-10:30)',
            4: '4 (11:00-11:50)', 5: '5 (11:50-12:40)', 6: '6 (12:40-13:30)',
            7: '7 (13:30-14:20)', 8: '8 (14:20-15:00)',
        };
        return horas[h] || h;
    }

    function formatearRango(inicio, fin) {
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const [aY, aM, aD] = inicio.split('-').map(Number);
        const [bY, bM, bD] = fin.split('-').map(Number);
        const mesA = meses[aM - 1];
        const mesB = meses[bM - 1];
        if (mesA === mesB) {
            return `${aD} al ${bD} de ${mesA} de ${aY}`;
        }
        return `${aD} de ${mesA} al ${bD} de ${mesB} de ${bY}`;
    }
});
</script>
@endpush