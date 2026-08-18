<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\ConsultaRealizada;
use App\Models\DatosAcademicos;
use App\Models\ProblemaAprendizaje;
use App\Models\DatosSalud;
use App\Models\DatosFamiliares;
use Carbon\Carbon;

class FichaDatosService
{
    public function formatearDatos(Alumno $alumno, ConsultaRealizada $consulta)
    {
        // Formatear fecha
        $fechaTexto = $consulta->consultado_en->isoFormat('DD [de] MMMM [del] YYYY');
        $horaTexto = $consulta->consultado_en->format('h:i:s A');
        
        // Procesar dispositivos
        $dispositivosArray = [];
        if ($alumno->datosAcademicos && $alumno->datosAcademicos->dispositivos) {
            $dispositivosArray = DatosAcademicos::binarioToDispositivos($alumno->datosAcademicos->dispositivos);
        }
        
        // Procesar características de aprendizaje
        $caracteristicasArray = [];
        if ($alumno->problemaAprendizaje && $alumno->problemaAprendizaje->caracteristicas_especificas) {
            $caracteristicasArray = ProblemaAprendizaje::binarioToProblemas($alumno->problemaAprendizaje->caracteristicas_especificas);
        }
        
        // Procesar síntomas
        $sintomasArray = [];
        if ($alumno->datosSalud && $alumno->datosSalud->sintomas_cuales) {
            $sintomasArray = DatosSalud::binarioToSintomas($alumno->datosSalud->sintomas_cuales);
        }
        
        return [
            'folio' => $consulta->folio,
            'fecha' => $fechaTexto,
            'hora' => $horaTexto,
            'usuario' => $consulta->usuario->nombre_completo . ' -- ' . $consulta->usuario->num_empleado,
            
            // Datos del alumno
            'num_control' => $alumno->num_control ?? '—',
            'estatus' => $alumno->estatus ? 'Activo' : 'Inactivo',
            'fecha_nacimiento' => $this->obtenerFechaNacimiento($alumno->curp),
            'edad' => $this->calcularEdad($alumno->curp),
            'nombre_completo' => $alumno->nombre_completo,
            
            // Lugar de nacimiento
            'lugar_nacimiento' => $alumno->lugarNacimiento,
            
            // Domicilio
            'domicilio' => $alumno->domicilio,
            
            // Secundaria
            'secundaria' => $alumno->secundaria,
            
            // Contacto
            'telefono_celular' => $alumno->telefono_celular,
            'email_personal' => $alumno->email_personal,
            'email_institucional' => $alumno->email_institucional,
            'curp' => $alumno->curp,
            'nss' => $alumno->nss,
            
            // Grupo
            'grupo' => $alumno->grupo,
            
            // Becas (solo activas)
            'becas' => $alumno->becas->filter(function($b) { return $b->activa; })->map(function($b) {
                return $b->beca ? $b->beca->tipo_beca : 'Beca';
            })->toArray(),
            
            // Trabajo
            'trabajo' => $alumno->trabajo,
            
            // Familiares (ordenados por prioridad)
            'familiares' => $alumno->familiares->sortBy(function($f) {
                return $f->pivot->contacto_emergencia;
            }),
            
            // Datos familiares
            'datos_familiares' => $alumno->datosFamiliares,
            
            // Servicios de la casa (convertir binario a array)
            'servicios_array' => $alumno->datosFamiliares && $alumno->datosFamiliares->servicios_casa ? 
                DatosFamiliares::binarioToServicios($alumno->datosFamiliares->servicios_casa) : [],
            
            // Info socioeconómica
            'info_socioeco' => $alumno->infoSocioeco,
            
            // Transporte (puede ser array o string)
            'transportes' => $alumno->infoSocioeco ? $alumno->infoSocioeco->transporte : [],
            
            // Datos académicos
            'datos_academicos' => $alumno->datosAcademicos,
            'dispositivos_array' => $dispositivosArray,
            
            // Problemas aprendizaje
            'problema_aprendizaje' => $alumno->problemaAprendizaje,
            'caracteristicas_array' => $caracteristicasArray,
            
            // Datos salud
            'datos_salud' => $alumno->datosSalud,
            'sintomas_array' => $sintomasArray,
            
            // Actividades recreativas
            'actividades' => $alumno->actividadesRecreativas,
        ];
    }
    
    private function obtenerFechaNacimiento($curp)
    {
        if (!$curp || strlen($curp) < 10) return '—';
        
        $dia = substr($curp, 8, 2);
        $mes = substr($curp, 6, 2);
        $anio = substr($curp, 4, 2);
        $anioCompleto = intval($anio) <= 24 ? 2000 + intval($anio) : 1900 + intval($anio);
        
        return "{$dia}/{$mes}/{$anioCompleto}";
    }

    private function calcularEdad($curp)
    {
        if (!$curp || strlen($curp) < 10) return '—';
        
        $dia = substr($curp, 8, 2);
        $mes = substr($curp, 6, 2);
        $anio = substr($curp, 4, 2);
        $anioCompleto = intval($anio) <= 24 ? 2000 + intval($anio) : 1900 + intval($anio);
        
        $fechaNacimiento = \DateTime::createFromFormat('d/m/Y', "{$dia}/{$mes}/{$anioCompleto}");
        $hoy = new \DateTime();
        $edad = $hoy->diff($fechaNacimiento)->y;
        
        return $edad;
    }
}