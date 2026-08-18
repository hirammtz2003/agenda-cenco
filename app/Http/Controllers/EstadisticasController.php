<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\ConsultaRealizada;
use App\Models\Grupo;
use App\Models\Beca;  // ← IMPORTANTE: Agregar esta línea
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class EstadisticasController extends Controller
{
    private function puedeAcceder()
    {
        $user = Auth::user();
        if (!$user) return false;
        
        $privilegios = $user->privilegios ?? 'NNNNN';
        $pos1 = $privilegios[1] ?? 'N'; // Posición 1 = General
        
        return in_array($pos1, ['C', 'G']);
    }
    
    public function index()
    {
        if (!$this->puedeAcceder()) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }
        
        return view('usuarios.datos-estadisticas.estadisticas');
    }
    
    public function generarInformeGrupos(Request $request)
    {
        try {
            // Verificar permisos
            if (!$this->puedeAcceder()) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos'], 403);
            }
            
            $user = Auth::user();
            
            // Validar contraseña
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta'], 401);
            }
            
            // Obtener datos: alumnos activos con sus grupos
            $alumnos = Alumno::with(['grupo'])
                ->where('estatus', true)
                ->whereHas('grupo')
                ->get();
            
            // Estructura: carrera -> semestre -> grupo -> cantidad
            $datos = [];
            $totalGlobal = 0;
            
            // Lista de semestres ordenados
            $semestres = ['1°', '2°', '3°', '4°', '5°', '6°'];
            $gruposLetra = ['A', 'B'];
            
            foreach ($alumnos as $alumno) {
                if (!$alumno->grupo) continue;
                
                $carrera = $alumno->grupo->carrera;
                $semestre = $alumno->grupo->semestre;
                $grupoLetra = $alumno->grupo->grupo;
                
                if (!isset($datos[$carrera])) {
                    $datos[$carrera] = [];
                }
                if (!isset($datos[$carrera][$semestre])) {
                    $datos[$carrera][$semestre] = ['A' => 0, 'B' => 0];
                }
                
                $datos[$carrera][$semestre][$grupoLetra]++;
                $totalGlobal++;
            }
            
            // Preparar datos para la tabla y gráficas
            $carreras = array_keys($datos);
            sort($carreras);
            
            // Preparar datos para gráficas
            $colores = [
                'Soporte y Mantenimiento de Equipo de Cómputo' => '#28a745',
                'Soporte y Gestión de Tecnologías Informáticas' => '#17a2b8',
                'Enfermería General' => '#fd7e14',
                'Ventas' => '#ffc107',
                'Diseño Gráfico Digital' => '#6f42c1'
            ];
            
            // Generar datos para gráfica de barras (total por carrera)
            $carreraTotales = [];
            foreach ($datos as $carrera => $semestresData) {
                $total = 0;
                foreach ($semestresData as $semestre => $gruposData) {
                    $total += $gruposData['A'] + $gruposData['B'];
                }
                $carreraTotales[$carrera] = $total;
            }
            
            // Ordenar de mayor a menor
            arsort($carreraTotales);
            
            // Datos para gráfica por semestre
            $semestreTotales = [];
            foreach ($semestres as $semestre) {
                $total = 0;
                foreach ($datos as $carrera => $semestresData) {
                    if (isset($semestresData[$semestre])) {
                        $total += $semestresData[$semestre]['A'] + $semestresData[$semestre]['B'];
                    }
                }
                $semestreTotales[$semestre] = $total;
            }
            
            // Registrar consulta
            $folio = ConsultaRealizada::generarFolio();
            $consulta = ConsultaRealizada::create([
                'folio' => $folio,
                'alumno_id' => null,
                'usuario_id' => Auth::id(),
                'consultado_en' => now(),
            ]);
            
            // Datos para la vista
            $datosPdf = [
                'folio' => $folio,
                'fecha' => $consulta->consultado_en->isoFormat('DD [de] MMMM [del] YYYY'),
                'hora' => $consulta->consultado_en->format('h:i:s A'),
                'usuario' => $user->nombre_completo . ' -- ' . $user->num_empleado,
                'datos' => $datos,
                'carreras' => $carreras,
                'semestres' => $semestres,
                'gruposLetra' => $gruposLetra,
                'totalGlobal' => $totalGlobal,
                'carreraTotales' => $carreraTotales,
                'semestreTotales' => $semestreTotales,
                'colores' => $colores,
            ];
            
            $pdf = Pdf::loadView('pdfs.estadistica_grupos', $datosPdf);
            $pdf->setPaper('letter', 'landscape');
            
            return $pdf->download("estadistica_grupos_{$folio}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Error en generarInformeGrupos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generarInformeSecundarias(Request $request)
    {
        try {
            if (!$this->puedeAcceder()) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos'], 403);
            }
            
            $user = Auth::user();
            
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta'], 401);
            }
            
            // Obtener TODOS los alumnos activos con su secundaria y grupo
            $alumnos = Alumno::with(['secundaria', 'grupo'])
                ->where('estatus', true)
                ->whereNotNull('id_secundaria_procedencia')
                ->get();
            
            // Estructura para almacenar datos
            $secundariasData = [];
            $totalGlobal = 0;
            
            // Definir grados
            $grados = [
                '1° y 2° Semestre' => ['1°', '2°'],
                '3° y 4° Semestre' => ['3°', '4°'],
                '5° y 6° Semestre' => ['5°', '6°']
            ];
            
            // Inicializar arrays
            foreach ($alumnos as $alumno) {
                if (!$alumno->secundaria) continue;
                
                $nombreSecundaria = $alumno->secundaria->nombre;
                $semestre = $alumno->grupo ? $alumno->grupo->semestre : null;
                
                if (!isset($secundariasData[$nombreSecundaria])) {
                    $secundariasData[$nombreSecundaria] = [
                        'total' => 0,
                        'grado1' => 0,
                        'grado2' => 0,
                        'grado3' => 0
                    ];
                }
                
                $secundariasData[$nombreSecundaria]['total']++;
                $totalGlobal++;
                
                // Clasificar por grado
                if (in_array($semestre, $grados['1° y 2° Semestre'])) {
                    $secundariasData[$nombreSecundaria]['grado1']++;
                } elseif (in_array($semestre, $grados['3° y 4° Semestre'])) {
                    $secundariasData[$nombreSecundaria]['grado2']++;
                } elseif (in_array($semestre, $grados['5° y 6° Semestre'])) {
                    $secundariasData[$nombreSecundaria]['grado3']++;
                }
            }
            
            // Ordenar por total descendente
            arsort($secundariasData);
            
            // Calcular totales por grado
            $totalesGrado = [
                'grado1' => array_sum(array_column($secundariasData, 'grado1')),
                'grado2' => array_sum(array_column($secundariasData, 'grado2')),
                'grado3' => array_sum(array_column($secundariasData, 'grado3'))
            ];
            
            // Colores fijos para las secundarias (basados en el nombre)
            $colores = [];
            $colorPalette = [
                '#28a745', '#17a2b8', '#fd7e14', '#ffc107', '#6f42c1',
                '#20c997', '#dc3545', '#007bff', '#e83e8c', '#6c757d',
                '#343a40', '#f39c12', '#2ecc71', '#e74c3c', '#3498db'
            ];
            
            $index = 0;
            foreach (array_keys($secundariasData) as $nombre) {
                $colores[$nombre] = $colorPalette[$index % count($colorPalette)];
                $index++;
            }
            
            // Registrar consulta
            $folio = ConsultaRealizada::generarFolio();
            $consulta = ConsultaRealizada::create([
                'folio' => $folio,
                'alumno_id' => null,
                'usuario_id' => Auth::id(),
                'consultado_en' => now(),
            ]);
            
            $datosPdf = [
                'folio' => $folio,
                'fecha' => $consulta->consultado_en->isoFormat('DD [de] MMMM [del] YYYY'),
                'hora' => $consulta->consultado_en->format('h:i:s A'),
                'usuario' => $user->nombre_completo . ' -- ' . $user->num_empleado,
                'secundariasData' => $secundariasData,
                'totalGlobal' => $totalGlobal,
                'totalesGrado' => $totalesGrado,
                'colores' => $colores,
                'grados' => $grados
            ];
            
            $pdf = Pdf::loadView('pdfs.estadistica_secundarias', $datosPdf);
            $pdf->setPaper('letter', 'landscape');
            
            return $pdf->download("estadistica_secundarias_{$folio}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Error en generarInformeSecundarias: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generarInformeBecas(Request $request)
    {
        try {
            if (!$this->puedeAcceder()) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos'], 403);
            }
            
            $user = Auth::user();
            
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta'], 401);
            }
            
            // Obtener todos los alumnos activos
            $alumnos = Alumno::with(['becas.beca', 'infoSocioeco'])
                ->where('estatus', true)
                ->get();
            
            // 1. DATOS DE BECAS POR PROGRAMA (TODAS las becas)
            // Obtener TODAS las becas registradas en el sistema
            $todasLasBecas = Beca::all();
            
            $becasData = [];
            $alumnosConBecaIds = [];
            $conteoBecasPorAlumno = [];
            
            // Inicializar todas las becas con 0 alumnos
            foreach ($todasLasBecas as $beca) {
                $becasData[$beca->tipo_beca] = [
                    'descripcion' => $beca->descripcion ?? '',
                    'cantidad' => 0
                ];
            }
            
            // Contar asignaciones reales (solo becas activas)
            foreach ($alumnos as $alumno) {
                $becasActivas = 0;
                $tieneBecaActiva = false;
                
                foreach ($alumno->becas as $beca) {
                    if ($beca->activa) {
                        $tieneBecaActiva = true;
                        $becasActivas++;
                        $nombreBeca = $beca->beca->tipo_beca;
                        
                        if (isset($becasData[$nombreBeca])) {
                            $becasData[$nombreBeca]['cantidad']++;
                        }
                    }
                }
                
                if ($tieneBecaActiva) {
                    $alumnosConBecaIds[] = $alumno->id;
                    $conteoBecasPorAlumno[$alumno->id] = $becasActivas;
                }
            }
            
            // Ordenar por cantidad descendente (las que tienen 0 al final)
            uasort($becasData, function($a, $b) {
                if ($a['cantidad'] == $b['cantidad']) return 0;
                return ($a['cantidad'] > $b['cantidad']) ? -1 : 1;
            });
            
            // 2. ESTADÍSTICAS GENERALES
            $totalAlumnos = $alumnos->count();
            $alumnosConBeca = count($alumnosConBecaIds);
            $alumnosSinBeca = $totalAlumnos - $alumnosConBeca;
            
            $porcentajeConBeca = $totalAlumnos > 0 ? round(($alumnosConBeca / $totalAlumnos) * 100, 1) : 0;
            $porcentajeSinBeca = $totalAlumnos > 0 ? round(($alumnosSinBeca / $totalAlumnos) * 100, 1) : 0;
            
            // 3. ALUMNOS CON MÁS DE 1 BECA ACTIVA
            $alumnosConUnaBeca = 0;
            $alumnosConMultiplesBecas = 0;
            
            foreach ($conteoBecasPorAlumno as $cantidad) {
                if ($cantidad == 1) {
                    $alumnosConUnaBeca++;
                } else {
                    $alumnosConMultiplesBecas++;
                }
            }
            
            $porcentajeUnaBeca = $alumnosConBeca > 0 ? round(($alumnosConUnaBeca / $alumnosConBeca) * 100, 1) : 0;
            $porcentajeMultiplesBecas = $alumnosConBeca > 0 ? round(($alumnosConMultiplesBecas / $alumnosConBeca) * 100, 1) : 0;
            
            // 4. ALUMNOS CON BECAS INACTIVAS (sin ninguna activa)
            $alumnosConBecasInactivas = 0;
            foreach ($alumnos as $alumno) {
                $tieneActiva = false;
                $tieneInactiva = false;
                foreach ($alumno->becas as $beca) {
                    if ($beca->activa) {
                        $tieneActiva = true;
                    } else {
                        $tieneInactiva = true;
                    }
                }
                if ($tieneInactiva && !$tieneActiva) {
                    $alumnosConBecasInactivas++;
                }
            }
            
            // 5. CORRELACIÓN BECA VS APOYO ECONÓMICO
            $conBecaYApoyo = 0;
            $conBecaSinApoyo = 0;
            $sinBecaConApoyo = 0;
            $sinBecaSinApoyo = 0;
            
            foreach ($alumnos as $alumno) {
                $tieneBecaActiva = in_array($alumno->id, $alumnosConBecaIds);
                $recibeApoyo = ($alumno->infoSocioeco && $alumno->infoSocioeco->monto_apoyo > 0);
                
                if ($tieneBecaActiva) {
                    if ($recibeApoyo) {
                        $conBecaYApoyo++;
                    } else {
                        $conBecaSinApoyo++;
                    }
                } else {
                    if ($recibeApoyo) {
                        $sinBecaConApoyo++;
                    } else {
                        $sinBecaSinApoyo++;
                    }
                }
            }
            
            // Calcular porcentajes para gráficas
            $totalConBeca = $conBecaYApoyo + $conBecaSinApoyo;
            $porcentajeConBecaApoyo = $totalConBeca > 0 ? round(($conBecaYApoyo / $totalConBeca) * 100, 1) : 0;
            $porcentajeConBecaSinApoyo = $totalConBeca > 0 ? round(($conBecaSinApoyo / $totalConBeca) * 100, 1) : 0;
            
            $totalSinBeca = $sinBecaConApoyo + $sinBecaSinApoyo;
            $porcentajeSinBecaApoyo = $totalSinBeca > 0 ? round(($sinBecaConApoyo / $totalSinBeca) * 100, 1) : 0;
            $porcentajeSinBecaSinApoyo = $totalSinBeca > 0 ? round(($sinBecaSinApoyo / $totalSinBeca) * 100, 1) : 0;
            
            // Registrar consulta
            $folio = ConsultaRealizada::generarFolio();
            $consulta = ConsultaRealizada::create([
                'folio' => $folio,
                'alumno_id' => null,
                'usuario_id' => Auth::id(),
                'consultado_en' => now(),
            ]);
            
            $datosPdf = [
                'folio' => $folio,
                'fecha' => $consulta->consultado_en->isoFormat('DD [de] MMMM [del] YYYY'),
                'hora' => $consulta->consultado_en->format('h:i:s A'),
                'usuario' => $user->nombre_completo . ' -- ' . $user->num_empleado,
                'becasData' => $becasData,
                'totalAlumnos' => $totalAlumnos,
                'alumnosConBeca' => $alumnosConBeca,
                'alumnosSinBeca' => $alumnosSinBeca,
                'porcentajeConBeca' => $porcentajeConBeca,
                'porcentajeSinBeca' => $porcentajeSinBeca,
                'alumnosConUnaBeca' => $alumnosConUnaBeca,
                'alumnosConMultiplesBecas' => $alumnosConMultiplesBecas,
                'porcentajeUnaBeca' => $porcentajeUnaBeca,
                'porcentajeMultiplesBecas' => $porcentajeMultiplesBecas,
                'alumnosConBecasInactivas' => $alumnosConBecasInactivas,
                'conBecaYApoyo' => $conBecaYApoyo,
                'conBecaSinApoyo' => $conBecaSinApoyo,
                'sinBecaConApoyo' => $sinBecaConApoyo,
                'sinBecaSinApoyo' => $sinBecaSinApoyo,
                'porcentajeConBecaApoyo' => $porcentajeConBecaApoyo,
                'porcentajeConBecaSinApoyo' => $porcentajeConBecaSinApoyo,
                'porcentajeSinBecaApoyo' => $porcentajeSinBecaApoyo,
                'porcentajeSinBecaSinApoyo' => $porcentajeSinBecaSinApoyo,
            ];
            
            $pdf = Pdf::loadView('pdfs.estadistica_becas', $datosPdf);
            $pdf->setPaper('letter', 'portrait');
            
            return $pdf->download("estadistica_becas_{$folio}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Error en generarInformeBecas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generarInformeDomicilios(Request $request)
    {
        try {
            if (!$this->puedeAcceder()) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos'], 403);
            }
            
            $user = Auth::user();
            
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta'], 401);
            }
            
            // Obtener alumnos activos con sus relaciones
            $alumnos = Alumno::with(['domicilio', 'infoSocioeco'])
                ->where('estatus', true)
                ->get();
            
            // ===========================================
            // 1. DATOS POR LOCALIDAD (agrupando localidad + municipio)
            // ===========================================
            $localidadesData = [];
            
            foreach ($alumnos as $alumno) {
                if (!$alumno->domicilio) continue;
                
                $key = $alumno->domicilio->localidad . ' - ' . $alumno->domicilio->municipio;
                $tiempoTotal = 0;
                
                if ($alumno->infoSocioeco) {
                    $horas = $alumno->infoSocioeco->traslado_horas ?? 0;
                    $minutos = $alumno->infoSocioeco->traslado_minutos ?? 0;
                    $tiempoTotal = ($horas * 60) + $minutos;
                }
                
                if (!isset($localidadesData[$key])) {
                    $localidadesData[$key] = [
                        'localidad' => $alumno->domicilio->localidad,
                        'municipio' => $alumno->domicilio->municipio,
                        'cantidad' => 0,
                        'tiempos' => []
                    ];
                }
                
                $localidadesData[$key]['cantidad']++;
                if ($tiempoTotal > 0) {
                    $localidadesData[$key]['tiempos'][] = $tiempoTotal;
                }
            }
            
            // Calcular min y max por localidad
            foreach ($localidadesData as &$data) {
                $data['tiempo_min'] = !empty($data['tiempos']) ? min($data['tiempos']) : null;
                $data['tiempo_max'] = !empty($data['tiempos']) ? max($data['tiempos']) : null;
            }
            
            // Ordenar por cantidad descendente
            uasort($localidadesData, function($a, $b) {
                return $b['cantidad'] <=> $a['cantidad'];
            });
            
            // ===========================================
            // 2. GRÁFICA: PROCEDENCIA (Municipio Río Grande)
            // ===========================================
            $enRGGrandeLocalidad = 0;  // Localidad "Río Grande" en municipio "Río Grande"
            $enRGMunicipioOtraLocalidad = 0;  // Municipio "Río Grande" pero otra localidad
            $enOtroMunicipio = 0;  // Otro municipio
            
            foreach ($alumnos as $alumno) {
                if (!$alumno->domicilio) continue;
                
                $municipio = $alumno->domicilio->municipio;
                $localidad = $alumno->domicilio->localidad;
                
                if ($municipio == 'Río Grande' || $municipio == 'Rio Grande') {
                    if ($localidad == 'Río Grande' || $localidad == 'Rio Grande') {
                        $enRGGrandeLocalidad++;
                    } else {
                        $enRGMunicipioOtraLocalidad++;
                    }
                } else {
                    $enOtroMunicipio++;
                }
            }
            
            $totalAlumnosConDomicilio = $enRGGrandeLocalidad + $enRGMunicipioOtraLocalidad + $enOtroMunicipio;
            $porcentajeRGLocalidad = $totalAlumnosConDomicilio > 0 ? round(($enRGGrandeLocalidad / $totalAlumnosConDomicilio) * 100, 1) : 0;
            $porcentajeRGOtraLocalidad = $totalAlumnosConDomicilio > 0 ? round(($enRGMunicipioOtraLocalidad / $totalAlumnosConDomicilio) * 100, 1) : 0;
            $porcentajeOtroMunicipio = $totalAlumnosConDomicilio > 0 ? round(($enOtroMunicipio / $totalAlumnosConDomicilio) * 100, 1) : 0;
            
            // ===========================================
            // 3. GRÁFICA: TIEMPOS DE TRASLADO
            // ===========================================
            $trasladoLargo = 0;  // 1 hora o más
            $trasladoCorto = 0;  // menos de 1 hora
            
            foreach ($alumnos as $alumno) {
                if (!$alumno->infoSocioeco) {
                    $trasladoCorto++;
                    continue;
                }
                
                $horas = $alumno->infoSocioeco->traslado_horas ?? 0;
                $minutos = $alumno->infoSocioeco->traslado_minutos ?? 0;
                
                if ($horas >= 1 || ($horas == 0 && $minutos >= 60)) {
                    $trasladoLargo++;
                } else {
                    $trasladoCorto++;
                }
            }
            
            $totalConTraslado = $trasladoLargo + $trasladoCorto;
            $porcentajeTrasladoLargo = $totalConTraslado > 0 ? round(($trasladoLargo / $totalConTraslado) * 100, 1) : 0;
            $porcentajeTrasladoCorto = $totalConTraslado > 0 ? round(($trasladoCorto / $totalConTraslado) * 100, 1) : 0;
            
            // ===========================================
            // 4. GRÁFICA: TIPOS DE TRANSPORTE
            // ===========================================
            $unSoloTransporte = 0;
            $multiplesTransportes = 0;
            $transportesContados = [];
            
            foreach ($alumnos as $alumno) {
                if (!$alumno->infoSocioeco) continue;
                
                $transportes = $alumno->infoSocioeco->transporte ?? [];
                if (empty($transportes)) continue;
                
                if (count($transportes) == 1) {
                    $unSoloTransporte++;
                } else {
                    $multiplesTransportes++;
                }
                
                foreach ($transportes as $transporte) {
                    $nombre = trim($transporte);
                    if (!isset($transportesContados[$nombre])) {
                        $transportesContados[$nombre] = 0;
                    }
                    $transportesContados[$nombre]++;
                }
            }
            
            $totalConTransporte = $unSoloTransporte + $multiplesTransportes;
            $porcentajeUnTransporte = $totalConTransporte > 0 ? round(($unSoloTransporte / $totalConTransporte) * 100, 1) : 0;
            $porcentajeMultiplesTransportes = $totalConTransporte > 0 ? round(($multiplesTransportes / $totalConTransporte) * 100, 1) : 0;
            
            // Ordenar transportes por cantidad descendente
            arsort($transportesContados);
            
            // ===========================================
            // REGISTRAR CONSULTA
            // ===========================================
            $folio = ConsultaRealizada::generarFolio();
            $consulta = ConsultaRealizada::create([
                'folio' => $folio,
                'alumno_id' => null,
                'usuario_id' => Auth::id(),
                'consultado_en' => now(),
            ]);
            
            $datosPdf = [
                'folio' => $folio,
                'fecha' => $consulta->consultado_en->isoFormat('DD [de] MMMM [del] YYYY'),
                'hora' => $consulta->consultado_en->format('h:i:s A'),
                'usuario' => $user->nombre_completo . ' -- ' . $user->num_empleado,
                'localidadesData' => $localidadesData,
                'totalAlumnos' => $alumnos->count(),
                // Gráfica procedencia
                'enRGGrandeLocalidad' => $enRGGrandeLocalidad,
                'enRGMunicipioOtraLocalidad' => $enRGMunicipioOtraLocalidad,
                'enOtroMunicipio' => $enOtroMunicipio,
                'porcentajeRGLocalidad' => $porcentajeRGLocalidad,
                'porcentajeRGOtraLocalidad' => $porcentajeRGOtraLocalidad,
                'porcentajeOtroMunicipio' => $porcentajeOtroMunicipio,
                // Gráfica traslados
                'trasladoLargo' => $trasladoLargo,
                'trasladoCorto' => $trasladoCorto,
                'porcentajeTrasladoLargo' => $porcentajeTrasladoLargo,
                'porcentajeTrasladoCorto' => $porcentajeTrasladoCorto,
                // Gráfica transporte
                'unSoloTransporte' => $unSoloTransporte,
                'multiplesTransportes' => $multiplesTransportes,
                'porcentajeUnTransporte' => $porcentajeUnTransporte,
                'porcentajeMultiplesTransportes' => $porcentajeMultiplesTransportes,
                'transportesContados' => $transportesContados,
            ];
            
            $pdf = Pdf::loadView('pdfs.estadistica_domicilios', $datosPdf);
            $pdf->setPaper('letter', 'landscape');
            
            return $pdf->download("estadistica_domicilios_{$folio}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Error en generarInformeDomicilios: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generarInformeSocioeconomico(Request $request)
    {
        try {
            if (!$this->puedeAcceder()) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos'], 403);
            }
            
            $user = Auth::user();
            
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta'], 401);
            }
            
            // Obtener todos los alumnos activos
            $alumnos = Alumno::with(['infoSocioeco', 'datosFamiliares', 'trabajo', 'datosAcademicos', 'familiares'])
                ->where('estatus', true)
                ->get();
            
            // ===========================================
            // 1. Auto propio (alumno)
            // ===========================================
            $autoPropioSi = 0;
            $autoPropioNo = 0;
            
            // 2. Auto propio (familia)
            $autoFamiliaSi = 0;
            $autoFamiliaNo = 0;
            
            // 3. Hijos
            $conHijos = 0;
            $sinHijos = 0;
            
            // 4. Apoyo económico
            $conApoyo = 0;
            $sinApoyo = 0;
            
            // 5. Gasto comida/transporte (rangos)
            $gastoRangos = [
                '$0' => 0,
                '$1 - $50' => 0,
                '$51 - $100' => 0,
                '$101 - $150' => 0,
                '$151 - $200' => 0,
                '$201 o más' => 0
            ];
            
            // 6. Comparte domicilio con familiar
            $comparteDomicilio = 0;
            $noComparteDomicilio = 0;
            
            // 7. Casa propia
            $casaPropiaSi = 0;
            $casaPropiaNo = 0;
            
            // 8. Trabajo
            $conTrabajo = 0;
            $sinTrabajo = 0;
            
            // 9. Comidas diarias
            $comidas3 = 0;
            $comidasMenos3 = 0;
            $comidasMas3 = 0;
            
            // 10. Internet (posición 2 del binario)
            $conInternet = 0;
            $sinInternet = 0;
            
            // 11. Teléfono celular personal
            $conTelefono = 0;
            $sinTelefono = 0;
            
            // 12. Dispositivos para tareas (celular, computadora, tablet)
            $conDispositivos = 0;
            $sinDispositivos = 0;
            
            foreach ($alumnos as $alumno) {
                // 1. Auto propio alumno
                if ($alumno->infoSocioeco) {
                    if ($alumno->infoSocioeco->auto_propio_alumno) {
                        $autoPropioSi++;
                    } else {
                        $autoPropioNo++;
                    }
                } else {
                    $autoPropioNo++;
                }
                
                // 2. Auto propio familia
                if ($alumno->datosFamiliares) {
                    if ($alumno->datosFamiliares->auto_propio_familia) {
                        $autoFamiliaSi++;
                    } else {
                        $autoFamiliaNo++;
                    }
                } else {
                    $autoFamiliaNo++;
                }
                
                // 3. Hijos
                if ($alumno->infoSocioeco && $alumno->infoSocioeco->num_hijos > 0) {
                    $conHijos++;
                } else {
                    $sinHijos++;
                }
                
                // 4. Apoyo económico
                if ($alumno->infoSocioeco && $alumno->infoSocioeco->monto_apoyo > 0) {
                    $conApoyo++;
                } else {
                    $sinApoyo++;
                }
                
                // 5. Gasto comida/transporte
                $gasto = $alumno->infoSocioeco ? $alumno->infoSocioeco->gasto_comida_transporte : null;
                if ($gasto === null || $gasto == 0) {
                    $gastoRangos['$0']++;
                } elseif ($gasto <= 50) {
                    $gastoRangos['$1 - $50']++;
                } elseif ($gasto <= 100) {
                    $gastoRangos['$51 - $100']++;
                } elseif ($gasto <= 150) {
                    $gastoRangos['$101 - $150']++;
                } elseif ($gasto <= 200) {
                    $gastoRangos['$151 - $200']++;
                } else {
                    $gastoRangos['$201 o más']++;
                }
                
                // 6. Comparte domicilio con familiar
                $domicilioAlumno = $alumno->id_domicilio;
                $comparte = false;
                foreach ($alumno->familiares as $familiar) {
                    if ($familiar->id_domicilio == $domicilioAlumno && $domicilioAlumno !== null) {
                        $comparte = true;
                        break;
                    }
                }
                if ($comparte) {
                    $comparteDomicilio++;
                } else {
                    $noComparteDomicilio++;
                }
                
                // 7. Casa propia
                if ($alumno->datosFamiliares && $alumno->datosFamiliares->casa_propia) {
                    $casaPropiaSi++;
                } else {
                    $casaPropiaNo++;
                }
                
                // 8. Trabajo
                if ($alumno->trabajo && (
                    $alumno->trabajo->lugar_trabajo || 
                    $alumno->trabajo->horario_laboral || 
                    $alumno->trabajo->domicilio_trabajo || 
                    $alumno->trabajo->telefono_trabajo
                )) {
                    $conTrabajo++;
                } else {
                    $sinTrabajo++;
                }
                
                // 9. Comidas diarias
                $comidas = $alumno->infoSocioeco ? $alumno->infoSocioeco->comidas_diarias : null;
                if ($comidas == 3) {
                    $comidas3++;
                } elseif ($comidas < 3) {
                    $comidasMenos3++;
                } else {
                    $comidasMas3++;
                }
                
                // 10. Internet (posición 2 del binario de dispositivos)
                $tieneInternet = false;
                if ($alumno->datosAcademicos && $alumno->datosAcademicos->dispositivos) {
                    $binario = str_pad($alumno->datosAcademicos->dispositivos, 4, '0', STR_PAD_RIGHT);
                    $tieneInternet = substr($binario, 2, 1) === '1';
                }
                if ($tieneInternet) {
                    $conInternet++;
                } else {
                    $sinInternet++;
                }
                
                // 11. Teléfono celular personal
                if ($alumno->telefono_celular) {
                    $conTelefono++;
                } else {
                    $sinTelefono++;
                }
                
                // 12. Dispositivos para tareas (celular, computadora, tablet)
                $tieneDispositivos = false;
                if ($alumno->datosAcademicos && $alumno->datosAcademicos->dispositivos) {
                    $binario = str_pad($alumno->datosAcademicos->dispositivos, 4, '0', STR_PAD_RIGHT);
                    // Posiciones 0,1,3: Celular, Computadora, Tablet
                    if (substr($binario, 0, 1) === '1' || substr($binario, 1, 1) === '1' || substr($binario, 3, 1) === '1') {
                        $tieneDispositivos = true;
                    }
                }
                if ($tieneDispositivos) {
                    $conDispositivos++;
                } else {
                    $sinDispositivos++;
                }
            }
            
            $totalAlumnos = $alumnos->count();
            
            // Calcular porcentajes
            $porcentajes = [
                'autoPropioSi' => $totalAlumnos > 0 ? round(($autoPropioSi / $totalAlumnos) * 100, 1) : 0,
                'autoPropioNo' => $totalAlumnos > 0 ? round(($autoPropioNo / $totalAlumnos) * 100, 1) : 0,
                'autoFamiliaSi' => $totalAlumnos > 0 ? round(($autoFamiliaSi / $totalAlumnos) * 100, 1) : 0,
                'autoFamiliaNo' => $totalAlumnos > 0 ? round(($autoFamiliaNo / $totalAlumnos) * 100, 1) : 0,
                'conHijos' => $totalAlumnos > 0 ? round(($conHijos / $totalAlumnos) * 100, 1) : 0,
                'sinHijos' => $totalAlumnos > 0 ? round(($sinHijos / $totalAlumnos) * 100, 1) : 0,
                'conApoyo' => $totalAlumnos > 0 ? round(($conApoyo / $totalAlumnos) * 100, 1) : 0,
                'sinApoyo' => $totalAlumnos > 0 ? round(($sinApoyo / $totalAlumnos) * 100, 1) : 0,
                'comparteDomicilio' => $totalAlumnos > 0 ? round(($comparteDomicilio / $totalAlumnos) * 100, 1) : 0,
                'noComparteDomicilio' => $totalAlumnos > 0 ? round(($noComparteDomicilio / $totalAlumnos) * 100, 1) : 0,
                'casaPropiaSi' => $totalAlumnos > 0 ? round(($casaPropiaSi / $totalAlumnos) * 100, 1) : 0,
                'casaPropiaNo' => $totalAlumnos > 0 ? round(($casaPropiaNo / $totalAlumnos) * 100, 1) : 0,
                'conTrabajo' => $totalAlumnos > 0 ? round(($conTrabajo / $totalAlumnos) * 100, 1) : 0,
                'sinTrabajo' => $totalAlumnos > 0 ? round(($sinTrabajo / $totalAlumnos) * 100, 1) : 0,
                'comidas3' => $totalAlumnos > 0 ? round(($comidas3 / $totalAlumnos) * 100, 1) : 0,
                'comidasMenos3' => $totalAlumnos > 0 ? round(($comidasMenos3 / $totalAlumnos) * 100, 1) : 0,
                'comidasMas3' => $totalAlumnos > 0 ? round(($comidasMas3 / $totalAlumnos) * 100, 1) : 0,
                'conInternet' => $totalAlumnos > 0 ? round(($conInternet / $totalAlumnos) * 100, 1) : 0,
                'sinInternet' => $totalAlumnos > 0 ? round(($sinInternet / $totalAlumnos) * 100, 1) : 0,
                'conTelefono' => $totalAlumnos > 0 ? round(($conTelefono / $totalAlumnos) * 100, 1) : 0,
                'sinTelefono' => $totalAlumnos > 0 ? round(($sinTelefono / $totalAlumnos) * 100, 1) : 0,
                'conDispositivos' => $totalAlumnos > 0 ? round(($conDispositivos / $totalAlumnos) * 100, 1) : 0,
                'sinDispositivos' => $totalAlumnos > 0 ? round(($sinDispositivos / $totalAlumnos) * 100, 1) : 0,
            ];
            
            // Registrar consulta
            $folio = ConsultaRealizada::generarFolio();
            $consulta = ConsultaRealizada::create([
                'folio' => $folio,
                'alumno_id' => null,
                'usuario_id' => Auth::id(),
                'consultado_en' => now(),
            ]);
            
            $datosPdf = [
                'folio' => $folio,
                'fecha' => $consulta->consultado_en->isoFormat('DD [de] MMMM [del] YYYY'),
                'hora' => $consulta->consultado_en->format('h:i:s A'),
                'usuario' => $user->nombre_completo . ' -- ' . $user->num_empleado,
                'totalAlumnos' => $totalAlumnos,
                'autoPropioSi' => $autoPropioSi,
                'autoPropioNo' => $autoPropioNo,
                'autoFamiliaSi' => $autoFamiliaSi,
                'autoFamiliaNo' => $autoFamiliaNo,
                'conHijos' => $conHijos,
                'sinHijos' => $sinHijos,
                'conApoyo' => $conApoyo,
                'sinApoyo' => $sinApoyo,
                'gastoRangos' => $gastoRangos,
                'comparteDomicilio' => $comparteDomicilio,
                'noComparteDomicilio' => $noComparteDomicilio,
                'casaPropiaSi' => $casaPropiaSi,
                'casaPropiaNo' => $casaPropiaNo,
                'conTrabajo' => $conTrabajo,
                'sinTrabajo' => $sinTrabajo,
                'comidas3' => $comidas3,
                'comidasMenos3' => $comidasMenos3,
                'comidasMas3' => $comidasMas3,
                'conInternet' => $conInternet,
                'sinInternet' => $sinInternet,
                'conTelefono' => $conTelefono,
                'sinTelefono' => $sinTelefono,
                'conDispositivos' => $conDispositivos,
                'sinDispositivos' => $sinDispositivos,
                'porcentajes' => $porcentajes,
            ];
            
            $pdf = Pdf::loadView('pdfs.estadistica_socioeconomica', $datosPdf);
            $pdf->setPaper('letter', 'portrait');
            
            return $pdf->download("estadistica_socioeconomica_{$folio}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Error en generarInformeSocioeconomico: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generarInformeSalud(Request $request)
    {
        try {
            if (!$this->puedeAcceder()) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos'], 403);
            }
            
            $user = Auth::user();
            
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta'], 401);
            }
            
            // Obtener todos los alumnos activos con sus relaciones
            $alumnos = Alumno::with(['problemaAprendizaje', 'datosSalud'])
                ->where('estatus', true)
                ->get();
            
            // ===========================================
            // 1. Problemas de aprendizaje
            // ===========================================
            $conProblemasAprendizaje = 0;
            $sinProblemasAprendizaje = 0;
            
            // 2. Limitante físico
            $conLimitanteFisico = 0;
            $sinLimitanteFisico = 0;
            
            // 3. Problema auditivo
            $conProblemaAuditivo = 0;
            $sinProblemaAuditivo = 0;
            
            // 4. Adicción
            $conAdiccion = 0;
            $sinAdiccion = 0;
            
            // 5. Padecimiento emocional
            $conPadecimientoEmocional = 0;
            $sinPadecimientoEmocional = 0;
            
            // 6. Enfermedad actual
            $conEnfermedadActual = 0;
            $sinEnfermedadActual = 0;
            
            // 7. Síntomas de salud mental
            $conSintomas = 0;
            $sinSintomas = 0;
            
            // 8. Medicamento controlado
            $conMedicamento = 0;
            $sinMedicamento = 0;
            
            // 9. Alergia a medicamento
            $conAlergiaMedicamento = 0;
            $sinAlergiaMedicamento = 0;
            
            foreach ($alumnos as $alumno) {
                // 1. Problemas de aprendizaje
                $tieneProblemaAprendizaje = false;
                if ($alumno->problemaAprendizaje) {
                    // Verificar características específicas (binario)
                    if ($alumno->problemaAprendizaje->caracteristicas_especificas) {
                        $binario = str_pad($alumno->problemaAprendizaje->caracteristicas_especificas, 12, '0', STR_PAD_RIGHT);
                        if (strpos($binario, '1') !== false) {
                            $tieneProblemaAprendizaje = true;
                        }
                    }
                    // Verificar otro problema
                    if ($alumno->problemaAprendizaje->otro_problema) {
                        $tieneProblemaAprendizaje = true;
                    }
                }
                if ($tieneProblemaAprendizaje) {
                    $conProblemasAprendizaje++;
                } else {
                    $sinProblemasAprendizaje++;
                }
                
                // 2. Limitante físico
                if ($alumno->datosSalud && $alumno->datosSalud->limitante_fisico) {
                    $conLimitanteFisico++;
                } else {
                    $sinLimitanteFisico++;
                }
                
                // 3. Problema auditivo
                if ($alumno->datosSalud && $alumno->datosSalud->problema_auditivo) {
                    $conProblemaAuditivo++;
                } else {
                    $sinProblemaAuditivo++;
                }
                
                // 4. Adicción
                if ($alumno->datosSalud && $alumno->datosSalud->adiccion) {
                    $conAdiccion++;
                } else {
                    $sinAdiccion++;
                }
                
                // 5. Padecimiento emocional
                if ($alumno->datosSalud && $alumno->datosSalud->padecimiento_emocional) {
                    $conPadecimientoEmocional++;
                } else {
                    $sinPadecimientoEmocional++;
                }
                
                // 6. Enfermedad actual
                if ($alumno->datosSalud && $alumno->datosSalud->enfermedad_actual) {
                    $conEnfermedadActual++;
                } else {
                    $sinEnfermedadActual++;
                }
                
                // 7. Síntomas de salud mental
                $tieneSintomas = false;
                if ($alumno->datosSalud) {
                    // Verificar síntomas cual es (binario)
                    if ($alumno->datosSalud->sintomas_cuales) {
                        $binario = str_pad($alumno->datosSalud->sintomas_cuales, 5, '0', STR_PAD_RIGHT);
                        if (strpos($binario, '1') !== false) {
                            $tieneSintomas = true;
                        }
                    }
                    // Verificar otro síntoma
                    if ($alumno->datosSalud->otro_sintoma) {
                        $tieneSintomas = true;
                    }
                }
                if ($tieneSintomas) {
                    $conSintomas++;
                } else {
                    $sinSintomas++;
                }
                
                // 8. Medicamento controlado
                if ($alumno->datosSalud && $alumno->datosSalud->medicamento_controlado) {
                    $conMedicamento++;
                } else {
                    $sinMedicamento++;
                }
                
                // 9. Alergia a medicamento
                if ($alumno->datosSalud && $alumno->datosSalud->alergia_medicamento) {
                    $conAlergiaMedicamento++;
                } else {
                    $sinAlergiaMedicamento++;
                }
            }
            
            $totalAlumnos = $alumnos->count();
            
            // Calcular porcentajes
            $porcentajes = [
                'problemasAprendizaje' => $totalAlumnos > 0 ? round(($conProblemasAprendizaje / $totalAlumnos) * 100, 1) : 0,
                'sinProblemasAprendizaje' => $totalAlumnos > 0 ? round(($sinProblemasAprendizaje / $totalAlumnos) * 100, 1) : 0,
                'limitanteFisico' => $totalAlumnos > 0 ? round(($conLimitanteFisico / $totalAlumnos) * 100, 1) : 0,
                'sinLimitanteFisico' => $totalAlumnos > 0 ? round(($sinLimitanteFisico / $totalAlumnos) * 100, 1) : 0,
                'problemaAuditivo' => $totalAlumnos > 0 ? round(($conProblemaAuditivo / $totalAlumnos) * 100, 1) : 0,
                'sinProblemaAuditivo' => $totalAlumnos > 0 ? round(($sinProblemaAuditivo / $totalAlumnos) * 100, 1) : 0,
                'adiccion' => $totalAlumnos > 0 ? round(($conAdiccion / $totalAlumnos) * 100, 1) : 0,
                'sinAdiccion' => $totalAlumnos > 0 ? round(($sinAdiccion / $totalAlumnos) * 100, 1) : 0,
                'padecimientoEmocional' => $totalAlumnos > 0 ? round(($conPadecimientoEmocional / $totalAlumnos) * 100, 1) : 0,
                'sinPadecimientoEmocional' => $totalAlumnos > 0 ? round(($sinPadecimientoEmocional / $totalAlumnos) * 100, 1) : 0,
                'enfermedadActual' => $totalAlumnos > 0 ? round(($conEnfermedadActual / $totalAlumnos) * 100, 1) : 0,
                'sinEnfermedadActual' => $totalAlumnos > 0 ? round(($sinEnfermedadActual / $totalAlumnos) * 100, 1) : 0,
                'sintomas' => $totalAlumnos > 0 ? round(($conSintomas / $totalAlumnos) * 100, 1) : 0,
                'sinSintomas' => $totalAlumnos > 0 ? round(($sinSintomas / $totalAlumnos) * 100, 1) : 0,
                'medicamento' => $totalAlumnos > 0 ? round(($conMedicamento / $totalAlumnos) * 100, 1) : 0,
                'sinMedicamento' => $totalAlumnos > 0 ? round(($sinMedicamento / $totalAlumnos) * 100, 1) : 0,
                'alergiaMedicamento' => $totalAlumnos > 0 ? round(($conAlergiaMedicamento / $totalAlumnos) * 100, 1) : 0,
                'sinAlergiaMedicamento' => $totalAlumnos > 0 ? round(($sinAlergiaMedicamento / $totalAlumnos) * 100, 1) : 0,
            ];
            
            // Registrar consulta
            $folio = ConsultaRealizada::generarFolio();
            $consulta = ConsultaRealizada::create([
                'folio' => $folio,
                'alumno_id' => null,
                'usuario_id' => Auth::id(),
                'consultado_en' => now(),
            ]);
            
            $datosPdf = [
                'folio' => $folio,
                'fecha' => $consulta->consultado_en->isoFormat('DD [de] MMMM [del] YYYY'),
                'hora' => $consulta->consultado_en->format('h:i:s A'),
                'usuario' => $user->nombre_completo . ' -- ' . $user->num_empleado,
                'totalAlumnos' => $totalAlumnos,
                'conProblemasAprendizaje' => $conProblemasAprendizaje,
                'sinProblemasAprendizaje' => $sinProblemasAprendizaje,
                'conLimitanteFisico' => $conLimitanteFisico,
                'sinLimitanteFisico' => $sinLimitanteFisico,
                'conProblemaAuditivo' => $conProblemaAuditivo,
                'sinProblemaAuditivo' => $sinProblemaAuditivo,
                'conAdiccion' => $conAdiccion,
                'sinAdiccion' => $sinAdiccion,
                'conPadecimientoEmocional' => $conPadecimientoEmocional,
                'sinPadecimientoEmocional' => $sinPadecimientoEmocional,
                'conEnfermedadActual' => $conEnfermedadActual,
                'sinEnfermedadActual' => $sinEnfermedadActual,
                'conSintomas' => $conSintomas,
                'sinSintomas' => $sinSintomas,
                'conMedicamento' => $conMedicamento,
                'sinMedicamento' => $sinMedicamento,
                'conAlergiaMedicamento' => $conAlergiaMedicamento,
                'sinAlergiaMedicamento' => $sinAlergiaMedicamento,
                'porcentajes' => $porcentajes,
            ];
            
            $pdf = Pdf::loadView('pdfs.estadistica_salud', $datosPdf);
            $pdf->setPaper('letter', 'portrait');
            
            return $pdf->download("estadistica_salud_{$folio}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Error en generarInformeSalud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }
}