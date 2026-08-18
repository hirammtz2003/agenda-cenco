<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\ConsultaRealizada;
use App\Models\Domicilio;
use App\Models\Grupo;
use App\Services\FichaDatosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class DatosEstadisticasController extends Controller
{
    protected $fichaService;
    
    public function __construct(FichaDatosService $fichaService)
    {
        $this->fichaService = $fichaService;
    }

    // Verificar si el usuario tiene acceso a este módulo
    private function puedeAccederModulo()
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        
        $privilegios = $user->privilegios ?? 'NNNNN';
        
        // Verificar posiciones 1, 2, 3, 4 para C o G
        $pos1 = $privilegios[1] ?? 'N';
        $pos2 = $privilegios[2] ?? 'N';
        $pos3 = $privilegios[3] ?? 'N';
        $pos4 = $privilegios[4] ?? 'N';
        
        return in_array($pos1, ['C', 'G']) || 
            in_array($pos2, ['C', 'G']) || 
            in_array($pos3, ['C', 'G']) || 
            in_array($pos4, ['C', 'G']);
    }

    public function index()
    {
        if (!$this->puedeAccederModulo()) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }
        
        return view('usuarios.datos-estadisticas.index');
    }

    // Verificar si puede ver búsqueda individual
    private function puedeVerIndividual()
    {
        $user = Auth::user();
        $privilegios = $user->privilegios ?? 'NNNNN';
        
        // Posición 1 (General) o Posición 2 (Grado)
        $pos1 = $privilegios[1] ?? 'N';
        $pos2 = $privilegios[2] ?? 'N';
        
        return in_array($pos1, ['C', 'G']) || in_array($pos2, ['C', 'G']);
    }

    // Obtener IDs de grupos a los que el usuario tiene acceso
    private function obtenerIdsGruposPermitidos()
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }
        
        $privilegios = $user->privilegios ?? 'NNNNN';
        $pos2 = $privilegios[2] ?? 'N';  // Grado (Trabajo Social)
        $pos3 = $privilegios[3] ?? 'N';  // Grupo (Docente)
        $pos4 = $privilegios[4] ?? 'N';
        
        $esGrado = in_array($pos2, ['C', 'G']);
        $esGrupo = in_array($pos3, ['C', 'G']) || in_array($pos4, ['C', 'G']);
        
        $gruposIds = [];
        
        if ($esGrado) {
            // Trabajo Social: grupos donde es tutor
            $grupos = Grupo::where('id_tutor', $user->id)->get();
            foreach ($grupos as $grupo) {
                $gruposIds[] = $grupo->id;
            }
        } elseif ($esGrupo) {
            // Docente: grupo donde es asesor
            $grupo = Grupo::where('id_asesor', $user->id)->first();
            if ($grupo) {
                $gruposIds[] = $grupo->id;
            }
        }
        
        return $gruposIds;
    }
    
    public function datosPrioritarios()
    {
        if (!$this->puedeAccederModulo()) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }
        
        return view('usuarios.datos-estadisticas.datos-prioritarios');
    }

    public function estadisticas()
    {
        $user = Auth::user();
        $privilegios = $user->privilegios ?? 'NNNNN';
        $pos1 = $privilegios[1] ?? 'N';
        
        // Solo usuarios con permiso General (posición 1) pueden ver estadísticas
        if (!in_array($pos1, ['C', 'G'])) {
            abort(403, 'No tienes permisos para acceder a informes de estadísticas.');
        }
        
        return view('usuarios.datos-estadisticas.estadisticas');
    }
    
    public function buscarAlumno(Request $request)
    {
        try {
            if (!$this->puedeVerIndividual()) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos'], 403);
            }
            
            $request->validate(['busqueda' => 'required|string|min:2']);
            $busqueda = $request->busqueda;
            
            $query = Alumno::query();
            
            $user = Auth::user();
            $privilegios = $user->privilegios ?? 'NNNNN';
            $pos1 = $privilegios[1] ?? 'N';
            
            if (!in_array($pos1, ['C', 'G'])) {
                $gruposPermitidos = $this->obtenerIdsGruposPermitidos();
                if (!empty($gruposPermitidos)) {
                    $query->whereIn('id_grupo', $gruposPermitidos);
                } else {
                    return response()->json(['success' => true, 'alumnos' => []]);
                }
            }
            
            $query->where(function($q) use ($busqueda) {
                $q->where('num_control', 'LIKE', "%{$busqueda}%")
                ->orWhere('nombre', 'LIKE', "%{$busqueda}%")
                ->orWhere('apellido1', 'LIKE', "%{$busqueda}%")
                ->orWhere('apellido2', 'LIKE', "%{$busqueda}%")
                ->orWhere('curp', 'LIKE', "%{$busqueda}%");
            });
            
            $alumnos = $query->limit(10)->get(['id', 'num_control', 'nombre', 'apellido1', 'apellido2', 'estatus']);
            
            return response()->json([
                'success' => true,
                'alumnos' => $alumnos->map(function($alumno) {
                    return [
                        'id' => $alumno->id,
                        'texto' => "{$alumno->num_control} - {$alumno->nombre} {$alumno->apellido1}",
                        'estatus' => $alumno->estatus
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en buscarAlumno: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function generarPDF(Request $request)
    {
        try {
            // Verificar si puede ver búsqueda individual
            if (!$this->puedeVerIndividual()) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para generar fichas individuales'], 403);
            }
            
            $user = Auth::user();
            if (!\Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta'], 401);
            }
            
            $request->validate(['alumno_id' => 'required|exists:alumnos,id']);
            
            // Verificar que el usuario tenga acceso a este alumno específico
            $alumno = Alumno::findOrFail($request->alumno_id);
            $privilegios = $user->privilegios ?? 'NNNNN';
            $pos1 = $privilegios[1] ?? 'N';
            
            if (!in_array($pos1, ['C', 'G'])) {
                $gruposPermitidos = $this->obtenerIdsGruposPermitidos();
                if (!in_array($alumno->id_grupo, $gruposPermitidos)) {
                    return response()->json(['success' => false, 'message' => 'No tienes acceso a este alumno'], 403);
                }
            }
            
            // Registrar consulta
            $folio = ConsultaRealizada::generarFolio();
            $consulta = ConsultaRealizada::create([
                'folio' => $folio,
                'alumno_id' => $alumno->id,
                'usuario_id' => Auth::id(),
                'consultado_en' => now(),
            ]);
            
            // Cargar relaciones
            $alumno->load([
                'grupo', 'lugarNacimiento', 'domicilio', 'secundaria',
                'becas.beca', 'trabajo', 'datosFamiliares', 'infoSocioeco',
                'datosAcademicos', 'problemaAprendizaje', 'datosSalud',
                'actividadesRecreativas', 'familiares.domicilio'
            ]);
            
            // Formatear datos
            $datos = $this->fichaService->formatearDatos($alumno, $consulta);
            
            // AGREGAR LA FOTO DESPUÉS de formatear los datos (no antes)
            $rutaFoto = public_path('fotos/' . $alumno->foto . '.jpg');
            $fotoBase64 = null;
            
            if ($alumno->foto && file_exists($rutaFoto)) {
                $tipo = pathinfo($rutaFoto, PATHINFO_EXTENSION);
                $data = file_get_contents($rutaFoto);
                $fotoBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode($data);
            } else {
                // Imagen por defecto
                $rutaFotoDefault = public_path('imagenes/sin-foto.jpg');
                if (file_exists($rutaFotoDefault)) {
                    $data = file_get_contents($rutaFotoDefault);
                    $fotoBase64 = 'data:image/jpeg;base64,' . base64_encode($data);
                }
            }
            
            // Añadir la foto al array de datos
            $datos['fotoBase64'] = $fotoBase64;
            
            $pdf = Pdf::loadView('pdfs.ficha_datos_individuales', $datos);
            $pdf->setPaper('letter', 'portrait');
            
            return $pdf->download("ficha_{$folio}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Error en generarPDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    // Busca localidades para autocompletado
    public function buscarLocalidades(Request $request)
    {
        $request->validate(['busqueda' => 'required|string|min:2']);
        $busqueda = $request->busqueda;
        
        $localidades = Domicilio::where('localidad', 'LIKE', "%{$busqueda}%")
            ->select('localidad')
            ->distinct()
            ->limit(10)
            ->pluck('localidad');
        
        return response()->json([
            'success' => true,
            'localidades' => $localidades
        ]);
    }

    // Aplica filtros y devuelve cantidad de alumnos
    public function aplicarFiltros(Request $request)
    {
        try {
            $query = Alumno::query();
            
            $user = Auth::user();
            $privilegios = $user->privilegios ?? 'NNNNN';
            $pos1 = $privilegios[1] ?? 'N';
            
            if (!in_array($pos1, ['C', 'G'])) {
                $gruposPermitidos = $this->obtenerIdsGruposPermitidos();
                if (!empty($gruposPermitidos)) {
                    $query->whereIn('id_grupo', $gruposPermitidos);
                } else {
                    return response()->json(['success' => true, 'total' => 0]);
                }
            }
            
            if ($request->semestre || $request->grupo_letra || $request->carrera) {
                $query->whereHas('grupo', function($q) use ($request) {
                    if ($request->semestre) $q->where('semestre', $request->semestre);
                    if ($request->grupo_letra) $q->where('grupo', $request->grupo_letra);
                    if ($request->carrera) $q->where('carrera', $request->carrera);
                });
            }
            
            $query->where('estatus', true);
            
            if ($request->opcion_prioritaria === 'domicilio' && !empty($request->localidad)) {
                $query->whereHas('domicilio', function($q) use ($request) {
                    $q->where('localidad', 'LIKE', "%{$request->localidad}%");
                });
            }
            
            $total = $query->count();
            
            return response()->json(['success' => true, 'total' => $total]);
            
        } catch (\Exception $e) {
            \Log::error('Error en aplicarFiltros: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Genera listado de alumnos en PDF
    public function generarListadoPDF(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Verificar contraseña
            if (!\Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta'], 401);
            }
            
            $filtros = $request->filtros;
            $query = Alumno::with(['grupo.asesor', 'domicilio', 'familiares' => function($q) {
                $q->wherePivot('contacto_emergencia', '>', 0)
                ->orderByPivot('contacto_emergencia', 'asc')
                ->limit(2);
            }, 'familiares.domicilio']);
            
            // Aplicar restricciones según permisos
            $privilegios = $user->privilegios ?? 'NNNNN';
            $pos1 = $privilegios[1] ?? 'N';
            
            if (!in_array($pos1, ['C', 'G'])) {
                $gruposPermitidos = $this->obtenerIdsGruposPermitidos();
                if (!empty($gruposPermitidos)) {
                    $query->whereIn('id_grupo', $gruposPermitidos);
                } else {
                    return response()->json(['success' => false, 'message' => 'No tienes acceso a ningún grupo'], 403);
                }
            }
            
            // Aplicar filtros de búsqueda
            if ($filtros['semestre'] || $filtros['grupo_letra'] || $filtros['carrera']) {
                $query->whereHas('grupo', function($q) use ($filtros) {
                    if ($filtros['semestre']) {
                        $q->where('semestre', $filtros['semestre']);
                    }
                    if ($filtros['grupo_letra']) {
                        $q->where('grupo', $filtros['grupo_letra']);
                    }
                    if ($filtros['carrera']) {
                        $q->where('carrera', $filtros['carrera']);
                    }
                });
            }
            
            $query->where('estatus', true);
            
            if ($filtros['opcion_prioritaria'] === 'domicilio' && !empty($filtros['localidad'])) {
                $query->whereHas('domicilio', function($q) use ($filtros) {
                    $q->where('localidad', 'LIKE', "%{$filtros['localidad']}%");
                });
            }
            
            $alumnos = $query->get();
            
            // Agrupar por grupo (resto del código igual)
            $grupos = [];
            foreach ($alumnos as $alumno) {
                if (!$alumno->grupo) continue;
                $grupoKey = $alumno->grupo->id;
                if (!isset($grupos[$grupoKey])) {
                    $grupos[$grupoKey] = [
                        'grupo' => $alumno->grupo,
                        'asesor' => $alumno->grupo->asesor,
                        'alumnos' => []
                    ];
                }
                $grupos[$grupoKey]['alumnos'][] = $alumno;
            }
            
            // Registrar consulta
            $folio = ConsultaRealizada::generarFolio();
            $consulta = ConsultaRealizada::create([
                'folio' => $folio,
                'alumno_id' => null,
                'usuario_id' => Auth::id(),
                'consultado_en' => now(),
            ]);
            
            $datos = [
                'folio' => $folio,
                'fecha' => $consulta->consultado_en->isoFormat('DD [de] MMMM [del] YYYY'),
                'hora' => $consulta->consultado_en->format('h:i:s A'),
                'usuario' => $user->nombre_completo . ' -- ' . $user->num_empleado,
                'filtros' => $filtros,
                'grupos' => $grupos,
                'total_general' => $alumnos->count(),
                'opcion_prioritaria' => $filtros['opcion_prioritaria'],
            ];
            
            $pdf = Pdf::loadView('pdfs.listado_alumnos', $datos);
            $pdf->setPaper('letter', 'landscape');
            
            return $pdf->download("listado_{$folio}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Error en generarListadoPDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }
}