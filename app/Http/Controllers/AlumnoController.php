<?php

namespace App\Http\Controllers;

use App\Models\LugarNacimiento;
use App\Models\Domicilio;
use App\Models\SecundariaProcedencia;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Beca;
use App\Models\TrabajoAlumno;
use App\Models\Familiar;
use App\Models\AlumnoFamiliar;
use App\Models\DatosFamiliares;
use App\Models\InfoSocioeco;
use App\Models\DatosAcademicos;
use App\Models\ProblemaAprendizaje;
use App\Models\DatosSalud;
use App\Models\ActividadesRecreativas;
use App\Helpers\PrivilegiosHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AlumnoController extends Controller
{
    private function puedeAccederModulo()
    {
        $user = Auth::user();
        return PrivilegiosHelper::consultaGeneral($user) ||
               PrivilegiosHelper::consultaGrado($user) ||
               PrivilegiosHelper::consultaGrupo($user) ||
               PrivilegiosHelper::consultaIndividual($user);
    }

    private function puedeRegistrar()
    {
        $user = Auth::user();
        return PrivilegiosHelper::gestionGeneral($user) ||
            PrivilegiosHelper::gestionGrado($user) ||
            PrivilegiosHelper::gestionGrupo($user);
    }

    public function index()
    {
        if (!$this->puedeAccederModulo()) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }

        $user = Auth::user();
        $puedeRegistrar = $this->puedeRegistrar();
        $puedeConsultar = true;

        return view('usuarios.gestion-alumnos.index', compact('puedeRegistrar', 'puedeConsultar'));
    }

    public function create()
    {
        if (!$this->puedeRegistrar()) {
            abort(403, 'No tienes permisos para registrar alumnos.');
        }
        return view('usuarios.gestion-alumnos.registro');
    }

    public function validarNumeroControl(Request $request)
    {
        $request->validate([
            'num_control' => 'required|string|max:14'
        ]);
        
        $existe = Alumno::where('num_control', $request->num_control)->exists();
        
        return response()->json([
            'disponible' => !$existe
        ]);
    }

    public function buscarLugarNacimiento(Request $request)
    {
        $request->validate(['busqueda' => 'required|string|min:2']);
        $lugares = LugarNacimiento::where('localidad', 'LIKE', "%{$request->busqueda}%")->limit(10)->get();
        return response()->json(['success' => true, 'lugares' => $lugares]);
    }

    public function obtenerLugarNacimiento($id)
    {
        return response()->json(['success' => true, 'lugar' => LugarNacimiento::findOrFail($id)]);
    }

    public function guardarLugarNacimiento(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos.'], 403);
        }

        $request->validate([
            'localidad' => 'required|string|max:30',
            'municipio' => 'required|string|max:30',
            'estado' => 'required|string|max:20',
            'pais' => 'required|string|max:15',
        ]);

        $lugar = LugarNacimiento::where('localidad', $request->localidad)
            ->where('municipio', $request->municipio)
            ->where('estado', $request->estado)
            ->where('pais', $request->pais)
            ->first();

        if (!$lugar) {
            $lugar = LugarNacimiento::create($request->all());
        }

        return response()->json(['success' => true, 'message' => 'Lugar de nacimiento procesado.', 'lugar' => $lugar]);
    }

    public function buscarDomicilio(Request $request)
    {
        $request->validate(['busqueda' => 'required|string|min:2']);
        $domicilios = Domicilio::select('colonia', 'localidad', 'municipio', 'estado', 'cp')
            ->where('colonia', 'like', "%{$request->busqueda}%")
            ->distinct()
            ->limit(10)
            ->get();
        return response()->json(['success' => true, 'domicilios' => $domicilios]);
    }

    public function obtenerDomicilio($id)
    {
        return response()->json(['success' => true, 'domicilio' => Domicilio::findOrFail($id)]);
    }

    public function guardarDomicilio(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos.'], 403);
        }

        $request->validate([
            'calle' => 'required|string|max:40',
            'num_ext' => 'required|string|max:10',
            'num_int' => 'nullable|string|max:10',
            'colonia' => 'required|string|max:40',
            'localidad' => 'required|string|max:30',
            'municipio' => 'required|string|max:30',
            'cp' => 'required|string|max:5',
            'estado' => 'required|string|max:20',
            'telefono_domicilio' => 'nullable|string|max:10',
        ]);

        $domicilio = Domicilio::create($request->all());
        return response()->json(['success' => true, 'message' => 'Domicilio guardado.', 'domicilio' => $domicilio]);
    }

    public function buscarSecundaria(Request $request)
    {
        try {
            $request->validate(['busqueda' => 'required|string|min:2']);
            $secundarias = SecundariaProcedencia::where('nombre', 'LIKE', '%' . $request->busqueda . '%')
                ->select('id', 'nombre', 'tipo', 'localidad', 'municipio', 'estado', 'pais')
                ->limit(10)
                ->get();
            return response()->json(['success' => true, 'secundarias' => $secundarias]);
        } catch (\Exception $e) {
            Log::error('Error en buscarSecundaria: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor'], 500);
        }
    }

    public function obtenerSecundaria($id)
    {
        return response()->json(['success' => true, 'secundaria' => SecundariaProcedencia::findOrFail($id)]);
    }

    public function guardarSecundaria(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos.'], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:General,Técnica,Telesecundaria,Abierta,Privada,Otro',
            'localidad' => 'required|string|max:30',
            'municipio' => 'required|string|max:30',
            'estado' => 'required|string|max:20',
            'pais' => 'required|string|max:15',
        ]);

        $secundaria = SecundariaProcedencia::where('nombre', $request->nombre)
            ->where('localidad', $request->localidad)
            ->where('municipio', $request->municipio)
            ->where('estado', $request->estado)
            ->first();

        if (!$secundaria) {
            $secundaria = SecundariaProcedencia::create($request->all());
        }

        return response()->json(['success' => true, 'message' => 'Secundaria procesada.', 'secundaria' => $secundaria]);
    }

    public function buscarApellido(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:apellido1,apellido2',
            'busqueda' => 'required|string|min:2'
        ]);

        $campo = $request->tipo;
        $resultados = Alumno::where($campo, 'LIKE', $request->busqueda . '%')
            ->select($campo)
            ->distinct()
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'resultados' => $resultados->pluck($campo)]);
    }

    public function buscarGrupos(Request $request)
    {
        try {
            $request->validate(['busqueda' => 'nullable|string']);
            $query = Grupo::with(['asesor', 'tutor']);

            if ($request->filled('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('semestre', 'LIKE', "%{$busqueda}%")
                      ->orWhere('grupo', 'LIKE', "%{$busqueda}%")
                      ->orWhere('carrera', 'LIKE', "%{$busqueda}%");
                });
            }

            $grupos = $query->limit(10)->get();
            return response()->json([
                'success' => true,
                'grupos' => $grupos->map(function($g) {
                    return [
                        'id' => $g->id,
                        'nombre' => $g->semestre . ' ' . $g->grupo . ' - ' . $g->carrera,
                        'asesor' => $g->asesor ? $g->asesor->getNombreCompletoAttribute() : 'No asignado',
                        'tutor' => $g->tutor ? $g->tutor->getNombreCompletoAttribute() : 'No asignado',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error en buscarGrupos: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor'], 500);
        }
    }

    private function generarNombreFoto($nombre, $apellido1)
    {
        // Función auxiliar para eliminar acentos y caracteres especiales
        $normalizar = function($texto) {
            // Convertir a minúsculas primero para mejor manejo
            $texto = mb_strtolower($texto, 'UTF-8');
            
            // Usar iconv para transliterar (convertir acentos a letras sin acento)
            // //TRANSLIT intenta convertir caracteres, //IGNORE omite los que no puede
            $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
            
            // Reemplazar espacios y caracteres no alfanuméricos por X
            $texto = preg_replace('/[^a-z0-9]/', 'X', $texto);
            
            // Convertir a mayúsculas
            return strtoupper($texto);
        };
        
        // Normalizar nombre y apellido
        $nombreNormalizado = $normalizar($nombre);
        $apellido1Normalizado = $normalizar($apellido1);
        
        // Tomar primeros 2 caracteres del nombre y primeros 3 del apellido
        $prefijo = substr($nombreNormalizado, 0, 2) . substr($apellido1Normalizado, 0, 3);
        
        // Buscar el último número secuencial para este prefijo
        $ultimo = Alumno::where('foto', 'LIKE', $prefijo . '%')->orderBy('foto', 'desc')->first();
        
        if ($ultimo) {
            $ultimoNumero = intval(substr($ultimo->foto, 5, 2));
            $nuevoNumero = str_pad($ultimoNumero + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $nuevoNumero = '00';
        }
        
        return $prefijo . $nuevoNumero;
    }

    public function guardarAlumno(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos.'], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:30',
            'apellido1' => 'required|string|max:20',
            'apellido2' => 'nullable|string|max:20',
            'num_control' => 'nullable|string|max:14|unique:alumnos,num_control',
            'telefono_celular' => 'nullable|string|max:10',
            'email_personal' => 'nullable|email|max:50',
            'email_institucional' => 'nullable|email|max:50',
            'curp' => 'nullable|string|max:18|unique:alumnos,curp',
            'nss' => 'nullable|string|max:11',
            'id_lugar_nacimiento' => 'nullable|exists:lugar_nacimiento,id',
            'id_domicilio' => 'nullable|exists:domicilio,id',
            'id_secundaria_procedencia' => 'nullable|exists:secundaria_procedencia,id',
            'id_grupo' => 'nullable|exists:grupos,id',
        ]);

        // Si el domicilio del alumno no fue guardado pero hay datos pendientes en sesión
        $id_domicilio = $request->id_domicilio;
        $domicilioPendiente = session('domicilio_alumno_pendiente');
        
        if (!$id_domicilio && $domicilioPendiente) {
            $domicilio = Domicilio::create($domicilioPendiente);
            $id_domicilio = $domicilio->id;
            session()->forget('domicilio_alumno_pendiente');
        }

        $foto = $this->generarNombreFoto($request->nombre, $request->apellido1);

        $alumno = Alumno::create([
            'foto' => $foto,
            'num_control' => $request->num_control,
            'nombre' => $request->nombre,
            'apellido1' => $request->apellido1,
            'apellido2' => $request->apellido2,
            'id_lugar_nacimiento' => $request->id_lugar_nacimiento,
            'id_domicilio' => $id_domicilio,
            'id_secundaria_procedencia' => $request->id_secundaria_procedencia,
            'id_grupo' => $request->id_grupo,
            'telefono_celular' => $request->telefono_celular,
            'email_personal' => $request->email_personal,
            'email_institucional' => $request->email_institucional,
            'curp' => $request->curp,
            'nss' => $request->nss,
            'estatus' => true,
        ]);

        $mensaje = 'Alumno registrado correctamente.';
        $becasGuardadas = [];

        // Guardar becas
        $becasTemporales = session('becas_temporales', []);
        if (!empty($becasTemporales)) {
            foreach ($becasTemporales as $becaTemp) {
                try {
                    \DB::table('alumno_beca')->updateOrInsert(
                        ['id_alumno' => $alumno->id, 'id_beca' => $becaTemp['id']],
                        ['activa' => true]
                    );
                    $becasGuardadas[] = $becaTemp['nombre'];
                } catch (\Exception $e) {
                    Log::error('Error al guardar beca: ' . $e->getMessage());
                }
            }
            session()->forget('becas_temporales');
        }

        // Guardar familiares
        $familiaresTemporales = session('familiares_temporales', []);
        $familiaresGuardados = [];

        if (!empty($familiaresTemporales)) {
            // Primero, actualizar el ID del domicilio para los familiares que comparten domicilio
            foreach ($familiaresTemporales as $key => $familiarTemp) {
                $comparte = $familiarTemp['relacion']['comparte_domicilio'] ?? false;
                
                // Si el familiar comparte domicilio
                if ($comparte) {
                    // Usar el ID del domicilio del alumno (que ya debería estar definido)
                    if ($id_domicilio) {
                        $familiaresTemporales[$key]['familiar']['id_domicilio'] = $id_domicilio;
                    }
                    // También actualizar si hay domicilio pendiente en sesión (por si acaso)
                    elseif ($domicilioPendiente && isset($familiaresTemporales[$key]['familiar']['id_domicilio']) && !$familiaresTemporales[$key]['familiar']['id_domicilio']) {
                        // Esto ya se manejó antes, pero por si acaso
                    }
                }
            }
            
            // Guardar la sesión actualizada
            session(['familiares_temporales' => $familiaresTemporales]);
            
            // Ahora guardar los familiares
            foreach ($familiaresTemporales as $familiarTemp) {
                try {
                    $familiarData = $familiarTemp['familiar'];
                    
                    $familiar = Familiar::create($familiarData);
                    AlumnoFamiliar::create([
                        'id_alumno' => $alumno->id,
                        'id_familiar' => $familiar->id,
                        'parentesco' => $familiarTemp['relacion']['parentesco'],
                        'tutor' => $familiarTemp['relacion']['tutor'],
                        'contacto_emergencia' => $familiarTemp['relacion']['contacto_emergencia'],
                    ]);
                    $familiaresGuardados[] = $familiar->nombre . ' ' . $familiar->apellido1;
                } catch (\Exception $e) {
                    Log::error('Error al guardar familiar: ' . $e->getMessage());
                }
            }
            session()->forget('familiares_temporales');
        }

        if (!empty($becasGuardadas)) {
            $mensaje .= ' Becas asignadas: ' . implode(', ', $becasGuardadas);
        }
        if (!empty($familiaresGuardados)) {
            $mensaje .= ' Familiares añadidos: ' . implode(', ', $familiaresGuardados);
        }

        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'alumno' => $alumno,
            'foto_generada' => $foto,
            'id_domicilio' => $alumno->id_domicilio
        ]);
    }

    public function buscarBecas(Request $request)
    {
        $request->validate(['busqueda' => 'required|string|min:2']);
        $becas = Beca::where('tipo_beca', 'LIKE', "%{$request->busqueda}%")
            ->select('id', 'tipo_beca')
            ->limit(10)
            ->get();
        return response()->json(['success' => true, 'becas' => $becas]);
    }

    public function agregarBecaTemporal(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos.'], 403);
        }
        
        $request->validate([
            'id_beca' => 'required',
            'nombre_beca' => 'required|string|max:100',
            'activa' => 'required|boolean'
        ]);
        
        // Obtener becas temporales de la sesión
        $becasTemporales = session('becas_temporales', []);
        
        // Verificar si ya existe
        $existe = false;
        foreach ($becasTemporales as $beca) {
            if ($beca['nombre'] === $request->nombre_beca) {
                $existe = true;
                break;
            }
        }
        
        if ($existe) {
            return response()->json([
                'success' => false, 
                'message' => 'Esta beca ya ha sido añadida.'
            ]);
        }
        
        // Agregar nueva beca temporal
        $becasTemporales[] = [
            'id' => $request->id_beca,
            'nombre' => $request->nombre_beca,
            'activa' => $request->activa
        ];
        
        session(['becas_temporales' => $becasTemporales]);
        
        return response()->json([
            'success' => true,
            'becas' => $becasTemporales,
            'message' => 'Beca añadida temporalmente.'
        ]);
    }

    public function eliminarBecaTemporal(Request $request)
    {
        $request->validate(['id_beca' => 'required']);
        $becasTemporales = session('becas_temporales', []);
        $becasTemporales = array_filter($becasTemporales, function($beca) use ($request) {
            return $beca['id'] != $request->id_beca;
        });
        session(['becas_temporales' => array_values($becasTemporales)]);
        return response()->json(['success' => true, 'becas' => array_values($becasTemporales)]);
    }

    public function limpiarBecasTemporales()
    {
        session()->forget('becas_temporales');
        return response()->json(['success' => true]);
    }

    public function guardarBecaAlumno(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos.'], 403);
        }

        $request->validate([
            'id_alumno' => 'required|exists:alumnos,id',
            'id_beca' => 'required|exists:becas,id',
            'activa' => 'required|boolean'
        ]);

        try {
            \DB::table('alumno_beca')->updateOrInsert(
                [
                    'id_alumno' => $request->id_alumno,
                    'id_beca' => $request->id_beca
                ],
                ['activa' => $request->activa]
            );
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error al guardar beca-alumno: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function guardarTrabajo(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos.'], 403);
        }

        $request->validate([
            'id_alumno' => 'required|exists:alumnos,id',
            'lugar_trabajo' => 'nullable|string|max:30',
            'horario_laboral' => 'nullable|string|max:50',
            'domicilio_trabajo' => 'nullable|string|max:100',
            'telefono_trabajo' => 'nullable|string|max:10',
        ]);

        $trabajo = TrabajoAlumno::where('id_alumno', $request->id_alumno)->first();

        if ($trabajo) {
            $trabajo->update($request->except('id_alumno'));
            $mensaje = 'Información laboral actualizada.';
        } else {
            $trabajo = TrabajoAlumno::create($request->all());
            $mensaje = 'Información laboral guardada.';
        }

        return response()->json(['success' => true, 'message' => $mensaje, 'trabajo' => $trabajo]);
    }

    public function buscarParentesco(Request $request)
    {
        $request->validate(['busqueda' => 'required|string|min:2']);
        $parentescos = AlumnoFamiliar::where('parentesco', 'LIKE', "%{$request->busqueda}%")
            ->select('parentesco')
            ->distinct()
            ->limit(10)
            ->get();
        return response()->json(['success' => true, 'resultados' => $parentescos->pluck('parentesco')]);
    }

    public function agregarFamiliarTemporal(Request $request)
    {
        try {
            $validated = $request->validate([
                'parentesco' => 'required|string|max:20',
                'vive' => 'required|boolean',
                'nombre' => 'required|string|max:30',
                'apellido1' => 'required|string|max:20',
                'apellido2' => 'nullable|string|max:20',
                'fecha_nacimiento' => 'required|date',
                'telefono_celular' => 'nullable|string|max:10',
                'escolaridad' => 'nullable|string',
                'ocupacion' => 'nullable|string|max:30',
                'lugar_trabajo' => 'nullable|string|max:30',
                'horario_laboral' => 'nullable|string|max:50',
                'domicilio_trabajo' => 'nullable|string|max:100',
                'telefono_trabajo' => 'nullable|string|max:10',
                'comparte_domicilio' => 'required|boolean',
                'es_tutor' => 'required|boolean',
                'es_contacto' => 'required|boolean',
                'prioridad_contacto' => 'nullable|integer|min:0'
            ]);

            // Validación adicional: si es contacto, prioridad debe ser > 0
            if ($request->es_contacto && (!$request->has('prioridad_contacto') || $request->prioridad_contacto <= 0)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Si autoriza como contacto de emergencia, debe asignar una prioridad mayor a 0.'
                ], 422);
            }

            // Validación adicional: si NO es contacto, prioridad debe ser 0 o null
            if (!$request->es_contacto) {
                $prioridad = 0;
            } else {
                $prioridad = $request->prioridad_contacto;
            }

            $id_domicilio = null;

            if ($request->comparte_domicilio) {
                // Solo validar domicilio si el familiar está vivo
                if ($request->vive) {
                    $id_domicilio = $request->input('id_domicilio');
                    
                    if (!$id_domicilio && $request->has('domicilio_data')) {
                        session(['domicilio_alumno_pendiente' => $request->domicilio_data]);
                        $id_domicilio = null;
                    } elseif (!$id_domicilio) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Primero debe completar los datos del domicilio del alumno.'
                        ], 422);
                    }
                }
                // Si no vive, el domicilio se queda como null
            } elseif ($request->has('domicilio_data') && $request->vive) {
                // Solo crear domicilio si el familiar está vivo
                $domicilioData = $request->domicilio_data;
                $domicilio = Domicilio::where('calle', $domicilioData['calle'])
                    ->where('num_ext', $domicilioData['num_ext'])
                    ->where('colonia', $domicilioData['colonia'])
                    ->where('cp', $domicilioData['cp'])
                    ->first();
                
                if (!$domicilio) {
                    $domicilio = Domicilio::create($domicilioData);
                }
                $id_domicilio = $domicilio->id;
            }

            $familiarData = [
                'vive' => $request->vive,
                'nombre' => $request->nombre,
                'apellido1' => $request->apellido1,
                'apellido2' => $request->apellido2,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono_celular' => $request->telefono_celular,
                'id_domicilio' => $id_domicilio,
                'escolaridad' => $request->escolaridad,
                'ocupacion' => $request->ocupacion,
                'lugar_trabajo' => $request->lugar_trabajo,
                'horario_laboral' => $request->horario_laboral,
                'domicilio_trabajo' => $request->domicilio_trabajo,
                'telefono_trabajo' => $request->telefono_trabajo,
            ];

            $familiaresTemporales = session('familiares_temporales', []);
            
            // Verificar prioridad única (solo para contactos activos)
            if ($prioridad > 0) {
                $prioridadesExistentes = array_filter($familiaresTemporales, function($f) {
                    return $f['relacion']['contacto_emergencia'] > 0;
                });
                
                foreach ($prioridadesExistentes as $f) {
                    if ($f['relacion']['contacto_emergencia'] == $prioridad) {
                        return response()->json([
                            'success' => false,
                            'message' => "La prioridad {$prioridad} ya está asignada a otro familiar."
                        ], 422);
                    }
                }
            }
            
            $tempId = count($familiaresTemporales) + 1;
            
            $familiaresTemporales[] = [
                'temp_id' => $tempId,
                'familiar' => $familiarData,
                'relacion' => [
                    'parentesco' => $request->parentesco,
                    'tutor' => $request->es_tutor,
                    'contacto_emergencia' => $prioridad,
                    'comparte_domicilio' => $request->comparte_domicilio
                ]
            ];
            
            session(['familiares_temporales' => $familiaresTemporales]);

            return response()->json([
                'success' => true,
                'familiares' => $familiaresTemporales
            ]);

        } catch (\Exception $e) {
            Log::error('Error en agregarFamiliarTemporal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function eliminarFamiliarTemporal(Request $request)
    {
        $request->validate(['temp_id' => 'required|integer']);
        $familiaresTemporales = session('familiares_temporales', []);
        $familiaresTemporales = array_filter($familiaresTemporales, function($f) use ($request) {
            return $f['temp_id'] != $request->temp_id;
        });
        session(['familiares_temporales' => array_values($familiaresTemporales)]);
        return response()->json(['success' => true, 'familiares' => array_values($familiaresTemporales)]);
    }

    public function obtenerFamiliarTemporal($tempId)
    {
        $familiaresTemporales = session('familiares_temporales', []);
        $familiar = null;
        foreach ($familiaresTemporales as $f) {
            if ($f['temp_id'] == $tempId) {
                $familiar = $f;
                break;
            }
        }
        return response()->json(['success' => true, 'familiar' => $familiar]);
    }

    public function limpiarFamiliaresTemporales()
    {
        session()->forget('familiares_temporales');
        return response()->json(['success' => true]);
    }

    // SUBSECCIÓN 2.2 - INFORMACIÓN FAMILIAR
    // Buscar estado civil de padres para autocompletado
    public function buscarEstadoCivil(Request $request)
    {
        $request->validate(['busqueda' => 'required|string|min:2']);
        
        $estadosCiviles = DatosFamiliares::where('estado_civil_padres', 'LIKE', "%{$request->busqueda}%")
            ->select('estado_civil_padres')
            ->distinct()
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'resultados' => $estadosCiviles->pluck('estado_civil_padres')
        ]);
    }

    // Guardar datos familiares
    public function guardarDatosFamiliares(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta operación.'
            ], 403);
        }
        
        $request->validate([
            'id_alumno' => 'required|exists:alumnos,id',
            'estado_civil_padres' => 'nullable|string|max:15',
            'ingreso_familiar_aprox' => 'nullable|integer|min:0',
            'gasto_familiar_aprox' => 'nullable|integer|min:0',
            'casa_propia' => 'required|boolean',
            'auto_propio' => 'required|boolean',
            'servicios' => 'nullable|array',
            'servicios.*' => 'in:1,2,3,4'
        ]);
        
        // Convertir servicios a binario
        $serviciosBinario = null;
        if ($request->has('servicios') && !empty($request->servicios)) {
            $serviciosBinario = DatosFamiliares::serviciosToBinario($request->servicios);
        }
        
        $datosFamiliares = DatosFamiliares::updateOrCreate(
            ['id_alumno' => $request->id_alumno],
            [
                'estado_civil_padres' => $request->estado_civil_padres,
                'ingreso_familiar_aprox' => $request->ingreso_familiar_aprox,
                'gasto_familiar_aprox' => $request->gasto_familiar_aprox,
                'casa_propia' => $request->casa_propia,
                'servicios_casa' => $serviciosBinario,
                'auto_propio_familia' => $request->auto_propio,
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Información familiar guardada correctamente.',
            'datos' => $datosFamiliares
        ]);
    }

    // SECCIÓN 3 - INFORMACIÓN SOCIOECONÓMICA PERSONAL
    // Buscar transportes existentes para autocompletado
    public function buscarTransportes(Request $request)
    {
        $request->validate(['busqueda' => 'required|string|min:2']);
        
        // Buscar en todos los registros de info_socioeco donde transporte contenga el término
        $transportes = InfoSocioeco::where('transporte', 'LIKE', "%{$request->busqueda}%")
            ->get()
            ->flatMap(function($item) {
                return $item->transporte ?? [];
            })
            ->unique()
            ->filter(function($transporte) use ($request) {
                return stripos($transporte, $request->busqueda) !== false;
            })
            ->values()
            ->take(10);
        
        return response()->json([
            'success' => true,
            'resultados' => $transportes
        ]);
    }

    // Buscar estado civil del alumno para autocompletado
    public function buscarEstadoCivilAlumno(Request $request)
    {
        $request->validate(['busqueda' => 'required|string|min:2']);
        
        $estadosCiviles = InfoSocioeco::where('estado_civil_alumno', 'LIKE', "%{$request->busqueda}%")
            ->select('estado_civil_alumno')
            ->distinct()
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'resultados' => $estadosCiviles->pluck('estado_civil_alumno')
        ]);
    }

    // Guardar información socioeconómica
    public function guardarInfoSocioeco(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta operación.'
            ], 403);
        }
        
        $request->validate([
            'id_alumno' => 'required|exists:alumnos,id',
            'auto_propio_alumno' => 'required|boolean',
            'transportes' => 'nullable|array',
            'transportes.*' => 'string|max:30',
            'traslado_horas' => 'nullable|integer|min:0|max:23',
            'traslado_minutos' => 'nullable|integer|min:0|max:59',
            'estado_civil_alumno' => 'nullable|string|max:15',
            'num_hijos' => 'nullable|integer|min:0',
            'edades_hijos' => 'nullable|string|max:20',
            'monto_apoyo' => 'nullable|integer|min:0',
            'gasto_comida_transporte' => 'nullable|integer|min:0',
            'comidas_diarias' => 'nullable|integer|min:1|max:10',
        ]);
        
        $datos = InfoSocioeco::updateOrCreate(
            ['id_alumno' => $request->id_alumno],
            [
                'auto_propio_alumno' => $request->auto_propio_alumno,
                'transporte' => $request->transportes,
                'traslado_horas' => $request->traslado_horas,
                'traslado_minutos' => $request->traslado_minutos,
                'estado_civil_alumno' => $request->estado_civil_alumno,
                'num_hijos' => $request->num_hijos ?? 0,
                'edades_hijos' => $request->edades_hijos,
                'monto_apoyo' => $request->monto_apoyo,
                'gasto_comida_transporte' => $request->gasto_comida_transporte,
                'comidas_diarias' => $request->comidas_diarias ?? 3,
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Información socioeconómica guardada correctamente.',
            'datos' => $datos
        ]);
    }

    // SECCIÓN 4 - DATOS ACADÉMICOS
    // Guardar datos académicos
    public function guardarDatosAcademicos(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta operación.'
            ], 403);
        }
        
        $request->validate([
            'id_alumno' => 'required|exists:alumnos,id',
            'dispositivos' => 'nullable|array',
            'dispositivos.*' => 'in:1,2,3,4',
            'otro_dispositivo' => 'nullable|string|max:30',
        ]);
        
        // Convertir dispositivos a binario
        $dispositivosBinario = null;
        if ($request->has('dispositivos') && !empty($request->dispositivos)) {
            $dispositivosBinario = DatosAcademicos::dispositivosToBinario($request->dispositivos);
        }
        
        $datosAcademicos = DatosAcademicos::updateOrCreate(
            ['id_alumno' => $request->id_alumno],
            [
                'dispositivos' => $dispositivosBinario,
                'otro_dispositivo' => $request->otro_dispositivo,
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Datos académicos guardados correctamente.',
            'datos' => $datosAcademicos
        ]);
    }

    // Guardar problemas de aprendizaje
    public function guardarProblemaAprendizaje(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta operación.'
            ], 403);
        }
        
        $request->validate([
            'id_alumno' => 'required|exists:alumnos,id',
            'tiene_problema' => 'required|boolean',
            'caracteristicas' => 'nullable|array',
            'caracteristicas.*' => 'in:1,2,3,4,5,6,7,8,9,10,11,12',
            'otro_problema' => 'nullable|string|max:255',
        ]);
        
        // Solo guardar si el usuario indicó que tiene problema
        if ($request->tiene_problema) {
            $caracteristicasBinario = null;
            if ($request->has('caracteristicas') && !empty($request->caracteristicas)) {
                $caracteristicasBinario = ProblemaAprendizaje::problemasToBinario($request->caracteristicas);
            }
            
            $problemaAprendizaje = ProblemaAprendizaje::updateOrCreate(
                ['id_alumno' => $request->id_alumno],
                [
                    'caracteristicas_especificas' => $caracteristicasBinario,
                    'otro_problema' => $request->otro_problema,
                ]
            );
        } else {
            // Si no tiene problema, eliminar registro existente
            ProblemaAprendizaje::where('id_alumno', $request->id_alumno)->delete();
            $problemaAprendizaje = null;
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Información de problemas de aprendizaje guardada correctamente.',
            'datos' => $problemaAprendizaje
        ]);
    }

    // SECCIÓN 5 - DATOS GENERALES DE SALUD
    // Guardar datos de salud
    public function guardarDatosSalud(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta operación.'
            ], 403);
        }
        
        $request->validate([
            'id_alumno' => 'required|exists:alumnos,id',
            'estatura' => 'required|integer|min:50|max:250',
            'peso' => 'required|numeric|min:10|max:300',
            'tipo_sangre' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'cuadro_basico_vacunas' => 'required|boolean',
            'usa_anteojos' => 'required|boolean',
            'graduacion_anteojos' => 'nullable|numeric|min:0|max:10',
            'cirugia' => 'nullable|string|max:255',
            'tiene_cirugia' => 'required|boolean',
            'alergia' => 'nullable|string|max:255',
            'tiene_alergia' => 'required|boolean',
            'limitante_fisico' => 'nullable|string|max:255',
            'tiene_limitante' => 'required|boolean',
            'problema_auditivo' => 'nullable|string|max:255',
            'tiene_auditivo' => 'required|boolean',
            'adiccion' => 'nullable|string|max:255',
            'tiene_adiccion' => 'required|boolean',
            'padecimiento_emocional' => 'nullable|string|max:255',
            'tiene_emocional' => 'required|boolean',
            'enfermedad_actual' => 'nullable|string|max:255',
            'tiene_sintomas' => 'required|boolean',
            'sintomas' => 'nullable|array',
            'sintomas.*' => 'in:1,2,3,4,5',
            'otro_sintoma' => 'nullable|string|max:255',
            'medicamento_controlado' => 'nullable|string|max:255',
            'tiene_medicamento' => 'required|boolean',
            'alergia_medicamento' => 'nullable|string|max:255',
            'tiene_alergia_med' => 'required|boolean',
            'motivo_hospitalizacion' => 'nullable|string|max:255',
            'tiene_hospitalizacion' => 'required|boolean',
            'diabetes' => 'required|boolean',
            'hipertension' => 'required|boolean',
            'dolores_cabeza' => 'required|boolean',
            'dolores_estomago' => 'required|boolean',
            'frecuencia_medico' => 'required|integer|min:-1',
            'frecuencia_dentista' => 'required|integer|min:-1',
        ]);
        
        // Convertir síntomas a binario
        $sintomasBinario = null;
        if ($request->tiene_sintomas && $request->has('sintomas') && !empty($request->sintomas)) {
            $sintomasBinario = DatosSalud::sintomasToBinario($request->sintomas);
        }
        
        $datosSalud = DatosSalud::updateOrCreate(
            ['id_alumno' => $request->id_alumno],
            [
                'estatura' => $request->estatura,
                'peso' => $request->peso,
                'tipo_sangre' => $request->tipo_sangre,
                'frecuencia_dentista' => $request->frecuencia_dentista,
                'graduacion_anteojos' => $request->usa_anteojos ? $request->graduacion_anteojos : null,
                'cuadro_basico_vacunas' => $request->cuadro_basico_vacunas,
                'cirugia' => $request->tiene_cirugia ? $request->cirugia : null,
                'alergia' => $request->tiene_alergia ? $request->alergia : null,
                'limitante_fisico' => $request->tiene_limitante ? $request->limitante_fisico : null,
                'problema_auditivo' => $request->tiene_auditivo ? $request->problema_auditivo : null,
                'adiccion' => $request->tiene_adiccion ? $request->adiccion : null,
                'padecimiento_emocional' => $request->tiene_emocional ? $request->padecimiento_emocional : null,
                'enfermedad_actual' => $request->enfermedad_actual,
                'sintomas_cuales' => $sintomasBinario,
                'otro_sintoma' => $request->tiene_sintomas ? $request->otro_sintoma : null,
                'medicamento_controlado' => $request->tiene_medicamento ? $request->medicamento_controlado : null,
                'alergia_medicamento' => $request->tiene_alergia_med ? $request->alergia_medicamento : null,
                'diabetes' => $request->diabetes,
                'hipertension' => $request->hipertension,
                'motivo_hospitalizacion' => $request->tiene_hospitalizacion ? $request->motivo_hospitalizacion : null,
                'dolores_cabeza' => $request->dolores_cabeza,
                'dolores_estomago' => $request->dolores_estomago,
                'frecuencia_medico' => $request->frecuencia_medico,
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Datos de salud guardados correctamente.',
            'datos' => $datosSalud
        ]);
    }

    // SECCIÓN 6 - ACTIVIDADES RECREATIVAS
    // Buscar deportes existentes para autocompletado (insensible a acentos)
    public function buscarDeportes(Request $request)
    {
        $request->validate(['busqueda' => 'required|string|min:2']);
        
        // Normalizar búsqueda para insensibilidad a acentos
        $busqueda = $request->busqueda;
        
        // Buscar en todos los registros de actividades_recreativas donde deporte_practicado contenga el término
        $deportes = ActividadesRecreativas::where('deporte_practicado', 'LIKE', "%{$busqueda}%")
            ->get()
            ->flatMap(function($item) {
                return $item->deporte_practicado ?? [];
            })
            ->unique(function($deporte) use ($busqueda) {
                // Para comparación insensible a acentos y mayúsculas
                $deporteNormalizado = $this->normalizarTexto($deporte);
                $busquedaNormalizada = $this->normalizarTexto($busqueda);
                return $deporteNormalizado;
            })
            ->filter(function($deporte) use ($busqueda) {
                $deporteNormalizado = $this->normalizarTexto($deporte);
                $busquedaNormalizada = $this->normalizarTexto($busqueda);
                return strpos($deporteNormalizado, $busquedaNormalizada) !== false;
            })
            ->values()
            ->take(10);
        
        return response()->json([
            'success' => true,
            'resultados' => $deportes
        ]);
    }

    // Función auxiliar para normalizar texto (eliminar acentos)
    private function normalizarTexto($texto)
    {
        if (!$texto) return '';
        $texto = mb_strtolower($texto, 'UTF-8');
        $acentos = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'ñ' => 'n', 'Ñ' => 'n'
        ];
        return strtr($texto, $acentos);
    }

    // Guardar actividades recreativas
    public function guardarActividadesRecreativas(Request $request)
    {
        if (!$this->puedeRegistrar()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta operación.'
            ], 403);
        }
        
        $request->validate([
            'id_alumno' => 'required|exists:alumnos,id',
            'pasatiempo_favorito' => 'nullable|string|max:100',
            'horas_pasatiempo' => 'nullable|integer|min:0|max:168',
            'deportes' => 'nullable|array',
            'deportes.*' => 'string|max:50',
            'horas_deporte' => 'nullable|integer|min:0|max:168',
            'horas_tv' => 'nullable|integer|min:0|max:24',
            'horas_compu' => 'nullable|integer|min:0|max:24',
            'uso_compu' => 'nullable|string|max:50',
            'chatea' => 'required|boolean',
            'temas_chat' => 'nullable|string|max:255',
        ]);
        
        $datos = ActividadesRecreativas::updateOrCreate(
            ['id_alumno' => $request->id_alumno],
            [
                'pasatiempo_favorito' => $request->pasatiempo_favorito,
                'horas_pasatiempo_dedicadas' => $request->horas_pasatiempo,
                'deporte_practicado' => $request->deportes,
                'horas_deporte_dedicadas' => $request->horas_deporte,
                'horas_dia_tv' => $request->horas_tv,
                'horas_dia_compu' => $request->horas_compu,
                'uso_frecuente_compu' => $request->uso_compu,
                'temas_quien_chat' => $request->chatea ? $request->temas_chat : null,
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Actividades recreativas guardadas correctamente.',
            'datos' => $datos
        ]);
    }

    // CONSULTA Y EDICIÓN GENERAL
    // Muestra la vista de consulta y edición general de alumnos
    public function consultaGeneral(Request $request)
    {
        $user = Auth::user();
        
        // Verificar permiso base (debe tener al menos un permiso de consulta o gestión en posiciones 1-4)
        $tienePermisoAlumnos = $this->tienePermisoAlumnos($user);
        
        if (!$tienePermisoAlumnos) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }
        
        // Determinar tipo de permiso del usuario (consulta)
        $permisoGeneral = PrivilegiosHelper::consultaGeneral($user);   // Posición 1 (C o G)
        $permisoGrado = PrivilegiosHelper::consultaGrado($user);       // Posición 2 (C o G)
        $permisoGrupo = PrivilegiosHelper::consultaGrupo($user);       // Posición 3 (C o G)
        $permisoIndividual = PrivilegiosHelper::consultaIndividual($user); // Posición 4 (C o G)
        
        // Capacidades de edición (solo G en las posiciones correspondientes)
        $puedeEditarGeneral = PrivilegiosHelper::gestionGeneral($user);   // G en posición 1
        $puedeEditarGrado = PrivilegiosHelper::gestionGrado($user);       // G en posición 2
        $puedeEditarGrupo = PrivilegiosHelper::gestionGrupo($user);       // G en posición 3
        
        // El usuario puede editar si tiene al menos un permiso de gestión en posiciones 1, 2 o 3
        $puedeEditar = $puedeEditarGeneral || $puedeEditarGrado || $puedeEditarGrupo;
        
        // El usuario puede consultar si tiene al menos un permiso C o G
        $puedeConsultar = $permisoGeneral || $permisoGrado || $permisoGrupo || $permisoIndividual;
        
        $mostrarInactivos = false;
        
        // Construir query base
        $query = Alumno::with(['grupo']);
        
        // ===========================================
        // APLICAR FILTROS SEGÚN PERMISOS
        // ===========================================
        
        if ($permisoGeneral) {
            // Gestión/Consulta General: ve todos los alumnos
            $mostrarInactivos = $request->boolean('mostrar_inactivos', false);
            if (!$mostrarInactivos) {
                $query->where('estatus', true);
            }
        } 
        elseif ($permisoGrado) {
            // Gestión/Consulta por Grado (Trabajo Social): ver alumnos de grupos donde es tutor
            $gruposPermitidos = $this->obtenerIdsGruposPermitidos($user, 'grado');
            
            if (!empty($gruposPermitidos)) {
                $query->where(function($q) use ($gruposPermitidos) {
                    $q->whereIn('id_grupo', $gruposPermitidos);
                    // También incluir alumnos sin grupo (para que puedan ser asignados)
                    $q->orWhereNull('id_grupo');
                });
            } else {
                // Si no tiene grupos asignados como tutor, no mostrar nada
                $query->whereRaw('1 = 0');
            }
            
            $mostrarInactivos = $request->boolean('mostrar_inactivos', false);
            if (!$mostrarInactivos) {
                $query->where('estatus', true);
            }
        }
        elseif ($permisoGrupo) {
            // Gestión/Consulta por Grupo (Docente): solo ver alumnos de su grupo donde es asesor
            $gruposPermitidos = $this->obtenerIdsGruposPermitidos($user, 'grupo');
            
            if (!empty($gruposPermitidos)) {
                $query->whereIn('id_grupo', $gruposPermitidos);
            } else {
                // Si no tiene grupo asignado como asesor, no mostrar nada
                $query->whereRaw('1 = 0');
            }
            
            // Para consulta por grupo, NO mostrar inactivos (solo activos)
            $query->where('estatus', true);
            $mostrarInactivos = false;
        }
        elseif ($permisoIndividual) {
            // Consulta Individual: solo ver alumnos activos (búsqueda individual)
            $query->where('estatus', true);
            $mostrarInactivos = false;
        }
        else {
            // Si llegamos aquí, el usuario tiene permisos pero no coincide con ningún tipo
            // Esto puede pasar si está en transición o tiene configuración mixta
            // Por defecto, mostramos solo alumnos activos
            $query->where('estatus', true);
        }
        
        // ===========================================
        // APLICAR FILTROS DE BÚSQUEDA
        // ===========================================

        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('num_control', 'LIKE', "%{$busqueda}%")
                ->orWhere('nombre', 'LIKE', "%{$busqueda}%")
                ->orWhere('apellido1', 'LIKE', "%{$busqueda}%")
                ->orWhere('apellido2', 'LIKE', "%{$busqueda}%")
                ->orWhereHas('grupo', function($q2) use ($busqueda) {
                    $q2->where('semestre', 'LIKE', "%{$busqueda}%")
                        ->orWhere('grupo', 'LIKE', "%{$busqueda}%")
                        ->orWhere('carrera', 'LIKE', "%{$busqueda}%");
                });
            });
        }

        // Filtros de grupo (solo si tiene permisos generales o de grado)
        if ($permisoGeneral || $permisoGrado) {
            if ($request->filled('semestre')) {
                $query->whereHas('grupo', function($q) use ($request) {
                    $q->where('semestre', $request->semestre);
                });
            }
            
            if ($request->filled('grupo_letra')) {
                $query->whereHas('grupo', function($q) use ($request) {
                    $q->where('grupo', $request->grupo_letra);
                });
            }
            
            if ($request->filled('carrera')) {
                $query->whereHas('grupo', function($q) use ($request) {
                    $q->where('carrera', $request->carrera);
                });
            }
        }

        // Filtro para mostrar solo alumnos sin grupo
        if ($request->boolean('mostrar_sin_grupo')) {
            $query->whereNull('id_grupo');
        }

        // Ordenamiento - CORREGIDO: id_asc por defecto
        $orden = $request->get('orden', 'id_asc');
        switch ($orden) {
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'nombre_asc':
                $query->orderBy('nombre')->orderBy('apellido1');
                break;
            case 'apellido_asc':
                $query->orderBy('apellido1')->orderBy('apellido2')->orderBy('nombre');
                break;
            case 'num_control_asc':
                $query->orderBy('num_control');
                break;
            default:
                $query->orderBy('id', 'asc');
        }
        
        $alumnos = $query->paginate(50);
        $totalEncontrados = $alumnos->total();
        
        return view('usuarios.gestion-alumnos.consulta-edicion-general', compact(
            'alumnos', 'totalEncontrados', 'puedeEditar', 'puedeConsultar', 
            'permisoGeneral', 'permisoGrado', 'permisoGrupo', 'permisoIndividual',
            'puedeEditarGeneral', 'puedeEditarGrado', 'puedeEditarGrupo', 'mostrarInactivos'
        ));
    }

    // Verifica si el usuario tiene algún permiso en alumnos (posiciones 1-4)
    private function tienePermisoAlumnos($user)
    {
        if (!$user || !isset($user->privilegios) || strlen($user->privilegios) < 5) {
            return false;
        }
        
        $privilegios = $user->privilegios;
        
        // Verificar posiciones 1 a 4 para C o G
        for ($i = 1; $i <= 4; $i++) {
            if (in_array($privilegios[$i], ['C', 'G'])) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Obtiene los IDs de los grupos que el usuario puede gestionar/consultar
     * - Para permiso GRADO: grupos donde es tutor (Trabajo Social)
     * - Para permiso GRUPO: grupo donde es asesor (Docente)
     */
    private function obtenerIdsGruposPermitidos($user, $tipoPermiso)
    {
        $gruposIds = [];
        
        if ($tipoPermiso === 'grado') {
            // Permiso GRADO: usuario es tutor de grupos (Trabajo Social)
            $grupos = Grupo::where('id_tutor', $user->id)->get();
            foreach ($grupos as $grupo) {
                $gruposIds[] = $grupo->id;
            }
        } elseif ($tipoPermiso === 'grupo') {
            // Permiso GRUPO: usuario es asesor de un grupo (Docente)
            $grupo = Grupo::where('id_asesor', $user->id)->first();
            if ($grupo) {
                $gruposIds[] = $grupo->id;
            }
        }
        
        return $gruposIds;
    }

    /**
     * Obtiene el ID del grupo asociado al usuario (para permiso por grupo)
     * Un usuario con permiso de grupo es asesor o tutor de un grupo específico
     */
    private function obtenerIdGrupoDelUsuario($user)
    {
        // Buscar si el usuario es asesor de algún grupo
        $grupoComoAsesor = Grupo::where('id_asesor', $user->id)->first();
        if ($grupoComoAsesor) {
            return $grupoComoAsesor->id;
        }
        
        // Buscar si el usuario es tutor de algún grupo
        $grupoComoTutor = Grupo::where('id_tutor', $user->id)->first();
        if ($grupoComoTutor) {
            return $grupoComoTutor->id;
        }
        
        return null;
    }

    // Busca grupos para la tabla de consulta general
    public function buscarGruposTabla(Request $request)
    {
        try {
            $request->validate(['busqueda' => 'nullable|string']);
            $query = Grupo::with(['asesor', 'tutor']);

            if ($request->filled('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('semestre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('grupo', 'LIKE', "%{$busqueda}%")
                    ->orWhere('carrera', 'LIKE', "%{$busqueda}%");
                });
            }

            $grupos = $query->limit(10)->get();
            return response()->json([
                'success' => true,
                'grupos' => $grupos->map(function($g) {
                    return [
                        'id' => $g->id,
                        'nombre' => $g->semestre . ' ' . $g->grupo . ' - ' . $g->carrera,
                        'semestre' => $g->semestre,
                        'grupo' => $g->grupo,
                        'carrera' => $g->carrera,
                        'asesor' => $g->asesor ? $g->asesor->getNombreCompletoAttribute() : 'No asignado',
                        'tutor' => $g->tutor ? $g->tutor->getNombreCompletoAttribute() : 'No asignado',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error en buscarGruposTabla: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor'], 500);
        }
    }

    // Actualizar datos básicos de un alumno (para edición en tabla)
    public function actualizarAlumnoTabla(Request $request)
    {
        // Verificar si el usuario tiene permisos de gestión
        $user = Auth::user();
        $tieneGestion = PrivilegiosHelper::gestionGeneral($user) ||
                        PrivilegiosHelper::gestionGrado($user) ||
                        PrivilegiosHelper::gestionGrupo($user);
        
        if (!$tieneGestion) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para editar.'], 403);
        }
        
        $request->validate([
            'current_password' => 'required|current_password',
            'alumnos' => 'required|array',
            'alumnos.*.id' => 'required|exists:alumnos,id',
            'alumnos.*.num_control' => 'nullable|string|max:14',
            'alumnos.*.nombre' => 'required|string|max:30',
            'alumnos.*.apellido1' => 'required|string|max:20',
            'alumnos.*.apellido2' => 'nullable|string|max:20',
            'alumnos.*.id_grupo' => 'nullable|exists:grupos,id',
        ]);
        
        $actualizados = [];
        $errores = [];
        
        foreach ($request->alumnos as $data) {
            try {
                $alumno = Alumno::findOrFail($data['id']);
                
                // Verificar que el usuario tenga acceso a este alumno según sus permisos
                if (!$this->usuarioPuedeEditarAlumno($user, $alumno)) {
                    $errores[] = "No tienes permisos para editar al alumno ID {$data['id']}.";
                    continue;
                }
                
                // Verificar unicidad de num_control si cambió
                if (isset($data['num_control']) && $data['num_control'] !== $alumno->num_control && !empty($data['num_control'])) {
                    $existe = Alumno::where('num_control', $data['num_control'])
                        ->where('id', '!=', $alumno->id)
                        ->exists();
                    if ($existe) {
                        $errores[] = "El número de control {$data['num_control']} ya existe.";
                        continue;
                    }
                }
                
                $alumno->update([
                    'num_control' => $data['num_control'] ?? null,
                    'nombre' => $data['nombre'],
                    'apellido1' => $data['apellido1'],
                    'apellido2' => $data['apellido2'] ?? null,
                    'id_grupo' => $data['id_grupo'] ?? null,
                ]);
                
                $actualizados[] = $alumno->id;
            } catch (\Exception $e) {
                $errores[] = "Error al actualizar alumno ID {$data['id']}: {$e->getMessage()}";
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => count($actualizados) . ' alumno(s) actualizado(s) correctamente.',
            'actualizados' => $actualizados,
            'errores' => $errores
        ]);
    }

    // Verifica si un usuario puede editar a un alumno específico según sus permisos
    private function usuarioPuedeEditarAlumno($user, $alumno)
    {
        // Gestión General: puede editar a todos
        if (PrivilegiosHelper::gestionGeneral($user)) {
            return true;
        }
        
        // Gestión por Grado (Trabajo Social): puede editar alumnos de grupos donde es tutor
        if (PrivilegiosHelper::gestionGrado($user)) {
            $gruposPermitidos = $this->obtenerIdsGruposPermitidos($user, 'grado');
            
            // Alumnos sin grupo pueden ser editados
            if (is_null($alumno->id_grupo)) {
                return true;
            }
            
            // Alumnos con grupo perteneciente a grupos donde es tutor
            if (in_array($alumno->id_grupo, $gruposPermitidos)) {
                return true;
            }
        }
        
        // Gestión por Grupo (Docente): puede editar alumnos de su grupo donde es asesor
        if (PrivilegiosHelper::gestionGrupo($user)) {
            $gruposPermitidos = $this->obtenerIdsGruposPermitidos($user, 'grupo');
            if (!empty($gruposPermitidos) && in_array($alumno->id_grupo, $gruposPermitidos)) {
                return true;
            }
        }
        
        return false;
    }

    // Cambiar estatus de un alumno
    public function cambiarEstatusAlumno(Request $request)
    {
        $user = Auth::user();
        
        $puedeCambiarEstatus = PrivilegiosHelper::gestionGeneral($user) ||
                            PrivilegiosHelper::gestionGrado($user);
        
        if (!$puedeCambiarEstatus) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para cambiar estatus.'], 403);
        }
        
        $request->validate([
            'current_password' => 'required|current_password',
            'id' => 'required|exists:alumnos,id',
            'estatus' => 'required|boolean'
        ]);
        
        $alumno = Alumno::findOrFail($request->id);
        
        if (!$this->usuarioPuedeEditarAlumno($user, $alumno)) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para modificar este alumno.'], 403);
        }
        
        // Si se desactiva, limpiar grupo y eliminar becas
        if ($request->estatus == 0) {
            $alumno->id_grupo = null;
            
            // Eliminar todas las becas asociadas al alumno
            \DB::table('alumno_beca')->where('id_alumno', $alumno->id)->delete();
        }
        
        $alumno->estatus = $request->estatus;
        $alumno->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Estatus actualizado correctamente.'
        ]);
    }

    // Muestra la vista de consulta y edición individual de alumnos
    public function consultaIndividual(Request $request)
    {
        $user = Auth::user();
        
        // Verificar permiso base (debe tener al menos un permiso de consulta o gestión en posiciones 1-4)
        $tienePermisoAlumnos = $this->tienePermisoAlumnos($user);
        
        if (!$tienePermisoAlumnos) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }
        
        // Determinar tipo de permiso del usuario (consulta)
        $permisoGeneral = PrivilegiosHelper::consultaGeneral($user);   // Posición 1 (C o G)
        $permisoGrado = PrivilegiosHelper::consultaGrado($user);       // Posición 2 (C o G)
        $permisoGrupo = PrivilegiosHelper::consultaGrupo($user);       // Posición 3 (C o G)
        $permisoIndividual = PrivilegiosHelper::consultaIndividual($user); // Posición 4 (C o G)
        
        // Capacidades de edición (solo G en las posiciones correspondientes)
        $puedeEditarGeneral = PrivilegiosHelper::gestionGeneral($user);   // G en posición 1
        $puedeEditarGrado = PrivilegiosHelper::gestionGrado($user);       // G en posición 2
        $puedeEditarGrupo = PrivilegiosHelper::gestionGrupo($user);       // G en posición 3
        $puedeEditarIndividual = PrivilegiosHelper::gestionIndividual($user); // G en posición 4
        
        // El usuario puede editar si tiene al menos un permiso de gestión (incluyendo individual)
        $puedeEditar = $puedeEditarGeneral || $puedeEditarGrado || $puedeEditarGrupo || $puedeEditarIndividual;
        
        // El usuario puede consultar si tiene al menos un permiso C o G en posiciones 1-4
        $puedeConsultar = $permisoGeneral || $permisoGrado || $permisoGrupo || $permisoIndividual;
        
        // Variable para guardar el alumno consultado
        $alumnoConsultado = null;
        $alumnoId = $request->get('alumno_id');
        
        if ($alumnoId) {
            $query = Alumno::with([
                'grupo', 'lugarNacimiento', 'domicilio', 'secundaria'
            ]);
            
            // Aplicar restricciones según permisos
            if ($permisoGeneral) {
                // Gestión/Consulta General: ve todos los alumnos
                $query->where('id', $alumnoId);
                $mostrarInactivos = true;
            } 
            elseif ($permisoGrado) {
                // Gestión/Consulta por Grado (Trabajo Social): ve alumnos de grupos donde es tutor
                $gruposPermitidos = $this->obtenerIdsGruposPermitidos($user, 'grado');
                
                if (!empty($gruposPermitidos)) {
                    $query->where('id', $alumnoId)
                        ->where(function($q) use ($gruposPermitidos) {
                            $q->whereIn('id_grupo', $gruposPermitidos)
                            ->orWhereNull('id_grupo');  // También ve alumnos sin grupo (activos)
                        });
                } else {
                    // Si no tiene grupos asignados, no puede ver ningún alumno
                    $query->whereRaw('1 = 0');
                }
                // NOTA: Los usuarios con permiso de grado también pueden ver inactivos
            } 
            elseif ($permisoGrupo) {
                // Gestión/Consulta por Grupo (Docente): solo ve alumnos de su grupo donde es asesor
                $gruposPermitidos = $this->obtenerIdsGruposPermitidos($user, 'grupo');
                
                if (!empty($gruposPermitidos)) {
                    $query->where('id', $alumnoId)
                        ->whereIn('id_grupo', $gruposPermitidos)
                        ->where('estatus', true); // Solo activos
                } else {
                    $query->whereRaw('1 = 0');
                }
            } 
            elseif ($permisoIndividual) {
                // Consulta Individual: solo ve alumnos activos
                $query->where('id', $alumnoId)->where('estatus', true);
            }
            
            $alumnoConsultado = $query->first();
            
            if (!$alumnoConsultado && $alumnoId) {
                session()->flash('error', 'No se encontró el alumno o no tienes permisos para verlo.');
            }
        }
        
        return view('usuarios.gestion-alumnos.consulta-edicion-individual', compact(
            'puedeEditar', 'puedeConsultar', 
            'permisoGeneral', 'permisoGrado', 'permisoGrupo', 'permisoIndividual',
            'puedeEditarGeneral', 'puedeEditarGrado', 'puedeEditarGrupo', 'puedeEditarIndividual',
            'alumnoConsultado'
        ));
    }

    // Busca alumnos para autocompletado en consulta individual
    public function buscarAlumnosAuto(Request $request)
    {
        $user = Auth::user();
        
        // Verificar permisos
        if (!$this->tienePermisoAlumnos($user)) {
            return response()->json(['success' => false, 'message' => 'Sin permisos']);
        }
        
        $request->validate(['busqueda' => 'required|string|min:2']);
        $busqueda = $request->busqueda;
        
        $query = Alumno::with('grupo');
        
        // Aplicar restricciones según permisos
        $permisoGeneral = PrivilegiosHelper::consultaGeneral($user);
        $permisoGrado = PrivilegiosHelper::consultaGrado($user);
        $permisoGrupo = PrivilegiosHelper::consultaGrupo($user);
        $permisoIndividual = PrivilegiosHelper::consultaIndividual($user);
        
        $mostrarInactivos = $request->boolean('mostrar_inactivos', false);
        
        if ($permisoGeneral) {
            // Gestión/Consulta General: todos los alumnos
            if (!$mostrarInactivos) {
                $query->where('estatus', true);
            }
        } 
        elseif ($permisoGrado) {
            // Gestión/Consulta por Grado: alumnos de grupos donde es tutor + alumnos sin grupo
            $gruposPermitidos = $this->obtenerIdsGruposPermitidos($user, 'grado');
            
            if (!empty($gruposPermitidos)) {
                $query->where(function($q) use ($gruposPermitidos) {
                    $q->whereIn('id_grupo', $gruposPermitidos)
                    ->orWhereNull('id_grupo');
                });
            } else {
                $query->whereRaw('1 = 0');
            }
            
            // Los usuarios con permiso de grado pueden ver inactivos si se solicita
            if (!$mostrarInactivos) {
                $query->where('estatus', true);
            }
        } 
        elseif ($permisoGrupo) {
            // Gestión/Consulta por Grupo: solo alumnos de su grupo (solo activos)
            $gruposPermitidos = $this->obtenerIdsGruposPermitidos($user, 'grupo');
            
            if (!empty($gruposPermitidos)) {
                $query->whereIn('id_grupo', $gruposPermitidos)
                    ->where('estatus', true);
            } else {
                $query->whereRaw('1 = 0');
            }
        } 
        elseif ($permisoIndividual) {
            // Consulta Individual: solo alumnos activos
            $query->where('estatus', true);
        }
        
        // Búsqueda por múltiples campos
        $query->where(function($q) use ($busqueda) {
            $q->where('num_control', 'LIKE', "%{$busqueda}%")
            ->orWhere('nombre', 'LIKE', "%{$busqueda}%")
            ->orWhere('apellido1', 'LIKE', "%{$busqueda}%")
            ->orWhere('apellido2', 'LIKE', "%{$busqueda}%")
            ->orWhere('curp', 'LIKE', "%{$busqueda}%");
        });
        
        $alumnos = $query->limit(10)->get()->map(function($alumno) {
            return [
                'id' => $alumno->id,
                'num_control' => $alumno->num_control,
                'nombre' => $alumno->nombre,
                'apellido1' => $alumno->apellido1,
                'apellido2' => $alumno->apellido2,
                'grupo' => $alumno->grupo ? $alumno->grupo->semestre . ' ' . $alumno->grupo->grupo : null,
                'estatus' => $alumno->estatus,
            ];
        });
        
        return response()->json(['success' => true, 'alumnos' => $alumnos]);
    }

    // Guarda la preferencia de modo de secciones múltiples
    public function toggleSectionsMode(Request $request)
    {
        session(['modo_secciones_multiple' => $request->modo_multiple]);
        return response()->json(['success' => true]);
    }

    // Obtiene la preferencia de modo de secciones múltiples
    public function getSectionsMode()
    {
        return response()->json(['modo_multiple' => session('modo_secciones_multiple', false)]);
    }

    // Obtiene todos los datos de un alumno para la consulta individual
    public function obtenerAlumnoCompleto($id)
    {
        try {
            $user = Auth::user();
            
            // Verificar permisos
            if (!$this->tienePermisoAlumnos($user)) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }
            
            $query = Alumno::with([
                'grupo', 
                'lugarNacimiento', 
                'domicilio', 
                'secundaria',
                'becas.beca',
                'trabajo',
                'datosFamiliares',
                'infoSocioeco',
                'datosAcademicos',
                'problemaAprendizaje',
                'datosSalud',        // ← Agrega esta
                'actividadesRecreativas',  // ← Agrega esta
                'familiares.domicilio'  // ← Agrega esta relación
            ]);
            
            // Aplicar restricciones según permisos
            $permisoGeneral = PrivilegiosHelper::consultaGeneral($user);
            $permisoGrado = PrivilegiosHelper::consultaGrado($user);
            $permisoGrupo = PrivilegiosHelper::consultaGrupo($user);
            
            if ($permisoGeneral) {
                $query->where('id', $id);
            } 
            elseif ($permisoGrado) {
                $gruposPermitidos = $this->obtenerIdsGruposPermitidos($user, 'grado');
                $query->where('id', $id)
                    ->where(function($q) use ($gruposPermitidos) {
                        $q->whereIn('id_grupo', $gruposPermitidos)
                            ->orWhereNull('id_grupo');
                    });
            } 
            elseif ($permisoGrupo) {
                $gruposPermitidos = $this->obtenerIdsGruposPermitidos($user, 'grupo');
                $query->where('id', $id)
                    ->whereIn('id_grupo', $gruposPermitidos);
            } else {
                $query->where('id', $id);
            }
            
            $alumno = $query->first();
            
            if (!$alumno) {
                return response()->json(['success' => false, 'message' => 'Alumno no encontrado'], 404);
            }
            
            // Cargar datos adicionales
            $response = [
                'success' => true,
                'alumno' => [
                    'id' => $alumno->id,
                    'foto' => $alumno->foto,
                    'num_control' => $alumno->num_control,
                    'nombre' => $alumno->nombre,
                    'apellido1' => $alumno->apellido1,
                    'apellido2' => $alumno->apellido2,
                    'curp' => $alumno->curp,
                    'nss' => $alumno->nss,
                    'telefono_celular' => $alumno->telefono_celular,
                    'email_personal' => $alumno->email_personal,
                    'email_institucional' => $alumno->email_institucional,
                    'estatus' => $alumno->estatus,
                    'id_grupo' => $alumno->id_grupo,
                    'grupo' => $alumno->grupo ? [
                        'id' => $alumno->grupo->id,
                        'semestre' => $alumno->grupo->semestre,
                        'grupo' => $alumno->grupo->grupo,
                        'carrera' => $alumno->grupo->carrera,
                    ] : null,
                    'lugar_nacimiento' => $alumno->lugarNacimiento,
                    'domicilio' => $alumno->domicilio,
                    'secundaria' => $alumno->secundaria,
                ]
            ];

            // Y después, al construir la respuesta, agrega las becas formateadas:
            if ($alumno->becas && $alumno->becas->count() > 0) {
                $becasFormateadas = [];
                foreach ($alumno->becas as $alumnoBeca) {
                    $becasFormateadas[] = [
                        'id' => $alumnoBeca->id_beca,
                        'nombre' => $alumnoBeca->beca ? $alumnoBeca->beca->tipo_beca : 'Beca',
                        'activa' => $alumnoBeca->activa,
                        'alumno_beca_id' => $alumnoBeca->id  // ID de la relación para futuras ediciones
                    ];
                }
                $response['alumno']['becas'] = $becasFormateadas;
            }

            // Después de obtener el alumno, formatea los familiares:
            if ($alumno->familiares && $alumno->familiares->count() > 0) {
                $familiaresFormateados = [];
                foreach ($alumno->familiares as $familiar) {
                    $familiaresFormateados[] = [
                        'id' => $familiar->id,
                        'temp_id' => null, // No aplica para consulta
                        'familiar' => [
                            'vive' => $familiar->vive,
                            'nombre' => $familiar->nombre,
                            'apellido1' => $familiar->apellido1,
                            'apellido2' => $familiar->apellido2,
                            'fecha_nacimiento' => $familiar->fecha_nacimiento,
                            'telefono_celular' => $familiar->telefono_celular,
                            'id_domicilio' => $familiar->id_domicilio,
                            'escolaridad' => $familiar->escolaridad,
                            'ocupacion' => $familiar->ocupacion,
                            'lugar_trabajo' => $familiar->lugar_trabajo,
                            'horario_laboral' => $familiar->horario_laboral,
                            'domicilio_trabajo' => $familiar->domicilio_trabajo,
                            'telefono_trabajo' => $familiar->telefono_trabajo,
                            'domicilio' => $familiar->domicilio ? [
                                'calle' => $familiar->domicilio->calle,
                                'num_ext' => $familiar->domicilio->num_ext,
                                'num_int' => $familiar->domicilio->num_int,
                                'colonia' => $familiar->domicilio->colonia,
                                'localidad' => $familiar->domicilio->localidad,
                                'municipio' => $familiar->domicilio->municipio,
                                'cp' => $familiar->domicilio->cp,
                                'estado' => $familiar->domicilio->estado,
                                'telefono_domicilio' => $familiar->domicilio->telefono_domicilio,
                            ] : null
                        ],
                        'relacion' => [
                            'parentesco' => $familiar->pivot->parentesco,
                            'tutor' => (bool)$familiar->pivot->tutor,
                            'contacto_emergencia' => (int)$familiar->pivot->contacto_emergencia,
                            'comparte_domicilio' => ($familiar->id_domicilio && $alumno->id_domicilio && $familiar->id_domicilio == $alumno->id_domicilio)
                        ]
                    ];
                }
                $response['alumno']['familiares'] = $familiaresFormateados;
            }
            
            // Cargar datos de otras tablas si existen
            $datosFamiliares = DatosFamiliares::where('id_alumno', $id)->first();
            if ($datosFamiliares) {
                $response['alumno']['datos_familiares'] = $datosFamiliares;
            }
            
            $infoSocioeco = InfoSocioeco::where('id_alumno', $id)->first();
            if ($infoSocioeco) {
                $response['alumno']['info_socioeco'] = $infoSocioeco;
            }
            
            $datosAcademicos = DatosAcademicos::where('id_alumno', $id)->first();
            if ($datosAcademicos) {
                $response['alumno']['datos_academicos'] = $datosAcademicos;
            }
            
            $problemaAprendizaje = ProblemaAprendizaje::where('id_alumno', $id)->first();
            if ($problemaAprendizaje) {
                $response['alumno']['problema_aprendizaje'] = $problemaAprendizaje;
            }
            
            $datosSalud = DatosSalud::where('id_alumno', $id)->first();
            if ($datosSalud) {
                $response['alumno']['datos_salud'] = $datosSalud;
            }
            
            $actividades = ActividadesRecreativas::where('id_alumno', $id)->first();
            if ($actividades) {
                $response['alumno']['actividades_recreativas'] = $actividades;
            }
            
            $trabajo = TrabajoAlumno::where('id_alumno', $id)->first();
            if ($trabajo) {
                $response['alumno']['trabajo'] = $trabajo;
            }

            // Dentro de obtenerAlumnoCompleto(), después de cargar $datosFamiliares, $datosAcademicos, etc.

            // Convertir servicios_casa de binario a array
            if ($datosFamiliares && $datosFamiliares->servicios_casa) {
                $response['alumno']['datos_familiares']->servicios_array = DatosFamiliares::binarioToServicios($datosFamiliares->servicios_casa);
            }

            // Convertir dispositivos de binario a array
            if ($datosAcademicos && $datosAcademicos->dispositivos) {
                $response['alumno']['datos_academicos']->dispositivos_array = DatosAcademicos::binarioToDispositivos($datosAcademicos->dispositivos);
            }

            // Convertir caracteristicas_especificas de binario a array
            if ($problemaAprendizaje && $problemaAprendizaje->caracteristicas_especificas) {
                $response['alumno']['problema_aprendizaje']->caracteristicas_array = ProblemaAprendizaje::binarioToProblemas($problemaAprendizaje->caracteristicas_especificas);
            }

            // Convertir síntomas de binario a array
            if ($datosSalud && $datosSalud->sintomas_cuales) {
                $response['alumno']['datos_salud']->sintomas_array = DatosSalud::binarioToSintomas($datosSalud->sintomas_cuales);
                $response['alumno']['datos_salud']->usa_anteojos = !empty($datosSalud->graduacion_anteojos);
            }

            // Determinar usa_anteojos basado en graduacion_anteojos
            if ($datosSalud) {
                $response['alumno']['datos_salud']->usa_anteojos = !empty($datosSalud->graduacion_anteojos);
            }

            // Determinar chatea basado en temas_quien_chat
            if ($actividades) {
                $response['alumno']['actividades_recreativas']->chatea = !empty($actividades->temas_quien_chat);
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Error en obtenerAlumnoCompleto: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()], 500);
        }
    }

    // Actualiza todos los datos de un alumno (consulta y edición individual)
    public function actualizarAlumnoCompleto(Request $request)
    {
        // 1. VERIFICAR PERMISOS Y CONTRASEÑA
        $user = Auth::user();
        
        // Verificar si tiene permisos de edición
        if (!$this->puedeRegistrar()) {
            return response()->json([
                'success' => false, 
                'message' => 'No tienes permisos para editar alumnos.'
            ], 403);
        }
        
        // Validar contraseña actual
        $request->validate([
            'current_password' => 'required|current_password',
            'alumno_id' => 'required|exists:alumnos,id'
        ]);
        
        $alumnoId = $request->alumno_id;
        
        // Iniciar transacción para asegurar que todo se guarde o nada
        DB::beginTransaction();
        
        try {
            // SECCIÓN 1.1 - DATOS BÁSICOS DEL ALUMNO
            $alumno = Alumno::findOrFail($alumnoId);

            // Verificar si se está cambiando a inactivo
            $cambiandoAInactivo = ($request->has('estatus') && $request->estatus == 0 && $alumno->estatus == 1);

            $alumnoData = $request->validate([
                'nombre' => 'required|string|max:30',
                'apellido1' => 'required|string|max:20',
                'apellido2' => 'nullable|string|max:20',
                'num_control' => 'nullable|string|max:14|unique:alumnos,num_control,' . $alumnoId,
                'telefono_celular' => 'nullable|string|max:10',
                'email_personal' => 'nullable|email|max:50',
                'email_institucional' => 'nullable|email|max:50',
                'curp' => 'nullable|string|max:18|unique:alumnos,curp,' . $alumnoId,
                'nss' => 'nullable|string|max:11',
                'estatus' => 'required|boolean',
            ]);

            $alumno->update($alumnoData);

            // Si se cambió a inactivo, desasociar del grupo y eliminar becas
            if ($cambiandoAInactivo) {
                // Desasociar del grupo
                $alumno->id_grupo = null;
                $alumno->save();
                
                // Eliminar todas las relaciones con becas
                \DB::table('alumno_beca')->where('id_alumno', $alumnoId)->delete();
                
                // También eliminar cualquier beca temporal en la sesión (limpiamos)
                if (session()->has('becas_temporales')) {
                    session()->forget('becas_temporales');
                }
            }
            
            // SECCIÓN 1.2 - LUGAR DE NACIMIENTO
            if ($request->has('lugar_nacimiento')) {
                $lugarData = $request->validate([
                    'lugar_nacimiento.localidad' => 'required|string|max:30',
                    'lugar_nacimiento.municipio' => 'required|string|max:30',
                    'lugar_nacimiento.estado' => 'required|string|max:20',
                    'lugar_nacimiento.pais' => 'required|string|max:15',
                ]);
                
                $lugarData = $lugarData['lugar_nacimiento'];
                
                // Buscar si ya existe
                $lugar = LugarNacimiento::where('localidad', $lugarData['localidad'])
                    ->where('municipio', $lugarData['municipio'])
                    ->where('estado', $lugarData['estado'])
                    ->where('pais', $lugarData['pais'])
                    ->first();
                
                if (!$lugar) {
                    $lugar = LugarNacimiento::create($lugarData);
                }
                
                $alumno->id_lugar_nacimiento = $lugar->id;
                $alumno->save();
            }
            
            // SECCIÓN 1.3 - DOMICILIO ACTUAL
            if ($request->has('domicilio')) {
                $domicilioData = $request->validate([
                    'domicilio.calle' => 'required|string|max:40',
                    'domicilio.num_ext' => 'required|string|max:10',
                    'domicilio.num_int' => 'nullable|string|max:10',
                    'domicilio.colonia' => 'required|string|max:40',
                    'domicilio.localidad' => 'required|string|max:30',
                    'domicilio.municipio' => 'required|string|max:30',
                    'domicilio.cp' => 'required|string|max:5',
                    'domicilio.estado' => 'required|string|max:20',
                    'domicilio.telefono_domicilio' => 'nullable|string|max:10',
                ]);
                
                $domicilioData = $domicilioData['domicilio'];
                
                // Si ya tiene domicilio asignado, actualizarlo; si no, crear uno nuevo
                if ($alumno->id_domicilio) {
                    $domicilio = Domicilio::find($alumno->id_domicilio);
                    if ($domicilio) {
                        $domicilio->update($domicilioData);
                    } else {
                        $domicilio = Domicilio::create($domicilioData);
                        $alumno->id_domicilio = $domicilio->id;
                        $alumno->save();
                    }
                } else {
                    $domicilio = Domicilio::create($domicilioData);
                    $alumno->id_domicilio = $domicilio->id;
                    $alumno->save();
                }
            }
            
            // SECCIÓN 1.4 - SECUNDARIA DE PROCEDENCIA
            if ($request->has('secundaria')) {
                $secundariaData = $request->validate([
                    'secundaria.nombre' => 'required|string|max:100',
                    'secundaria.tipo' => 'required|in:General,Técnica,Telesecundaria,Abierta,Privada,Otro',
                    'secundaria.localidad' => 'required|string|max:30',
                    'secundaria.municipio' => 'required|string|max:30',
                    'secundaria.estado' => 'required|string|max:20',
                    'secundaria.pais' => 'required|string|max:15',
                ]);
                
                $secundariaData = $secundariaData['secundaria'];
                
                // Buscar si ya existe
                $secundaria = SecundariaProcedencia::where('nombre', $secundariaData['nombre'])
                    ->where('localidad', $secundariaData['localidad'])
                    ->where('municipio', $secundariaData['municipio'])
                    ->where('estado', $secundariaData['estado'])
                    ->first();
                
                if (!$secundaria) {
                    $secundaria = SecundariaProcedencia::create($secundariaData);
                }
                
                $alumno->id_secundaria_procedencia = $secundaria->id;
                $alumno->save();
            }
            
            // SECCIÓN 1.6 - GRUPO
            if ($request->has('id_grupo')) {
                $alumno->id_grupo = $request->id_grupo ?: null;
                $alumno->save();
            }
            
            // SECCIÓN 1.7 - BECAS
            if ($request->has('becas')) {
                $becasData = $request->input('becas', []);
                
                // Validación más flexible para becas
                if (!is_array($becasData)) {
                    $becasData = [];
                }
                
                // Obtener IDs de becas actuales del alumno
                $becasActuales = \DB::table('alumno_beca')
                    ->where('id_alumno', $alumnoId)
                    ->pluck('id_beca')
                    ->toArray();
                
                $nuevosIds = [];
                
                foreach ($becasData as $beca) {
                    // Asegurarse de que cada beca tenga los campos necesarios
                    if (!isset($beca['id']) || !isset($beca['nombre']) || !isset($beca['activa'])) {
                        continue;
                    }
                    
                    $idBeca = $beca['id'];
                    $nombreBeca = $beca['nombre'];
                    $activaBeca = filter_var($beca['activa'], FILTER_VALIDATE_BOOLEAN);
                    
                    // Si es una beca nueva (comienza con 'new_' o no es numérico), crearla en la tabla becas
                    if (!is_numeric($idBeca) || str_starts_with($idBeca, 'new_')) {
                        $nuevaBeca = Beca::create([
                            'tipo_beca' => $nombreBeca,
                            'descripcion' => 'Creada desde edición de alumno'
                        ]);
                        $idBeca = $nuevaBeca->id;
                    } else {
                        $idBeca = (int) $idBeca;
                    }
                    
                    $nuevosIds[] = $idBeca;
                    
                    // Guardar o actualizar la relación
                    \DB::table('alumno_beca')->updateOrInsert(
                        [
                            'id_alumno' => $alumnoId,
                            'id_beca' => $idBeca
                        ],
                        ['activa' => $activaBeca]
                    );
                }
                
                // Eliminar becas que ya no están en la lista
                $idsAEliminar = array_diff($becasActuales, $nuevosIds);
                if (!empty($idsAEliminar)) {
                    \DB::table('alumno_beca')
                        ->where('id_alumno', $alumnoId)
                        ->whereIn('id_beca', $idsAEliminar)
                        ->delete();
                }
            }

            // SECCIÓN 1.8 - TRABAJO DEL ALUMNO
            if ($request->has('trabajo')) {
                $trabajoData = $request->validate([
                    'trabajo.lugar_trabajo' => 'nullable|string|max:30',
                    'trabajo.horario_laboral' => 'nullable|string|max:50',
                    'trabajo.domicilio_trabajo' => 'nullable|string|max:100',
                    'trabajo.telefono_trabajo' => 'nullable|string|max:10',
                ]);
                
                $trabajoData = $trabajoData['trabajo'];
                
                // Verificar si tiene algún dato para guardar
                $tieneDatos = !empty($trabajoData['lugar_trabajo']) || 
                              !empty($trabajoData['horario_laboral']) ||
                              !empty($trabajoData['domicilio_trabajo']) ||
                              !empty($trabajoData['telefono_trabajo']);
                
                if ($tieneDatos) {
                    TrabajoAlumno::updateOrCreate(
                        ['id_alumno' => $alumnoId],
                        $trabajoData
                    );
                } else {
                    // Si no hay datos, eliminar el registro si existe
                    TrabajoAlumno::where('id_alumno', $alumnoId)->delete();
                }
            }

            // SECCIÓN 2.1 - FAMILIARES Y RELACIONADOS
            if ($request->has('familiares')) {
                $familiaresData = $request->input('familiares', []);
                
                if (!is_array($familiaresData)) {
                    $familiaresData = [];
                }
                
                // Obtener IDs de familiares actuales del alumno
                $familiaresActuales = AlumnoFamiliar::where('id_alumno', $alumnoId)
                    ->pluck('id_familiar')
                    ->toArray();
                
                $nuevosIds = [];
                $familiaresParaActualizar = [];
                
                foreach ($familiaresData as $familiarDataItem) {
                    if (!isset($familiarDataItem['familiar']) || !isset($familiarDataItem['relacion'])) {
                        continue;
                    }
                    
                    $familiar = $familiarDataItem['familiar'];
                    $relacion = $familiarDataItem['relacion'];
                    
                    // Preparar datos del familiar
                    $familiarDatos = [
                        'vive' => $familiar['vive'] ?? true,
                        'nombre' => $familiar['nombre'] ?? '',
                        'apellido1' => $familiar['apellido1'] ?? '',
                        'apellido2' => $familiar['apellido2'] ?? null,
                        'fecha_nacimiento' => $familiar['fecha_nacimiento'] ?? null,
                        'telefono_celular' => $familiar['telefono_celular'] ?? null,
                        'escolaridad' => $familiar['escolaridad'] ?? null,
                        'ocupacion' => $familiar['ocupacion'] ?? null,
                        'lugar_trabajo' => $familiar['lugar_trabajo'] ?? null,
                        'horario_laboral' => $familiar['horario_laboral'] ?? null,
                        'domicilio_trabajo' => $familiar['domicilio_trabajo'] ?? null,
                        'telefono_trabajo' => $familiar['telefono_trabajo'] ?? null,
                    ];
                    
                    // Manejar domicilio del familiar
                    $id_domicilio = null;
                    if (isset($familiar['id_domicilio']) && $familiar['id_domicilio']) {
                        $id_domicilio = $familiar['id_domicilio'];
                    } elseif (isset($familiar['domicilio']) && $familiar['domicilio']) {
                        $domicilio = Domicilio::create($familiar['domicilio']);
                        $id_domicilio = $domicilio->id;
                    }
                    $familiarDatos['id_domicilio'] = $id_domicilio;
                    
                    // Verificar si es un familiar existente o nuevo
                    if (isset($familiarDataItem['id']) && is_numeric($familiarDataItem['id'])) {
                        // Actualizar familiar existente
                        $familiarModel = Familiar::find($familiarDataItem['id']);
                        if ($familiarModel) {
                            $familiarModel->update($familiarDatos);
                            $idFamiliar = $familiarModel->id;
                            
                            // Actualizar relación
                            AlumnoFamiliar::updateOrCreate(
                                [
                                    'id_alumno' => $alumnoId,
                                    'id_familiar' => $idFamiliar
                                ],
                                [
                                    'parentesco' => $relacion['parentesco'],
                                    'tutor' => $relacion['tutor'],
                                    'contacto_emergencia' => $relacion['contacto_emergencia']
                                ]
                            );
                        } else {
                            continue;
                        }
                    } else {
                        // Crear nuevo familiar
                        $familiarModel = Familiar::create($familiarDatos);
                        $idFamiliar = $familiarModel->id;
                        
                        // Crear relación
                        AlumnoFamiliar::create([
                            'id_alumno' => $alumnoId,
                            'id_familiar' => $idFamiliar,
                            'parentesco' => $relacion['parentesco'],
                            'tutor' => $relacion['tutor'],
                            'contacto_emergencia' => $relacion['contacto_emergencia']
                        ]);
                    }
                    
                    $nuevosIds[] = $idFamiliar;
                    $familiaresParaActualizar[] = $idFamiliar;
                }
                
                // Eliminar familiares que ya no están en la lista
                $idsAEliminar = array_diff($familiaresActuales, $nuevosIds);
                if (!empty($idsAEliminar)) {
                    // Eliminar las relaciones
                    AlumnoFamiliar::where('id_alumno', $alumnoId)
                        ->whereIn('id_familiar', $idsAEliminar)
                        ->delete();
                    
                    // Opcional: eliminar los registros de familiares si no están asociados a otros alumnos
                    foreach ($idsAEliminar as $idFamiliar) {
                        $existeOtraRelacion = AlumnoFamiliar::where('id_familiar', $idFamiliar)->exists();
                        if (!$existeOtraRelacion) {
                            Familiar::where('id', $idFamiliar)->delete();
                        }
                    }
                }
            }
            
            // SECCIÓN 2.2 - INFORMACIÓN FAMILIAR
            if ($request->has('datos_familiares')) {
                $familiaresData = $request->validate([
                    'datos_familiares.estado_civil_padres' => 'nullable|string|max:15',
                    'datos_familiares.ingreso_familiar_aprox' => 'nullable|integer|min:0',
                    'datos_familiares.gasto_familiar_aprox' => 'nullable|integer|min:0',
                    'datos_familiares.casa_propia' => 'required|boolean',
                    'datos_familiares.auto_propio_familia' => 'required|boolean',
                    'datos_familiares.servicios' => 'nullable|array',
                    'datos_familiares.servicios.*' => 'in:1,2,3,4',
                ]);
                
                $familiaresData = $familiaresData['datos_familiares'];
                
                // Convertir servicios a binario
                $serviciosBinario = null;
                if (!empty($familiaresData['servicios'])) {
                    $serviciosBinario = DatosFamiliares::serviciosToBinario($familiaresData['servicios']);
                }
                
                DatosFamiliares::updateOrCreate(
                    ['id_alumno' => $alumnoId],
                    [
                        'estado_civil_padres' => $familiaresData['estado_civil_padres'],
                        'ingreso_familiar_aprox' => $familiaresData['ingreso_familiar_aprox'],
                        'gasto_familiar_aprox' => $familiaresData['gasto_familiar_aprox'],
                        'casa_propia' => $familiaresData['casa_propia'],
                        'servicios_casa' => $serviciosBinario,
                        'auto_propio_familia' => $familiaresData['auto_propio_familia'],
                    ]
                );
            }
            
            // SECCIÓN 3 - INFORMACIÓN SOCIOECONÓMICA PERSONAL
            if ($request->has('info_socioeco')) {
                $socioecoData = $request->validate([
                    'info_socioeco.auto_propio_alumno' => 'required|boolean',
                    'info_socioeco.transportes' => 'nullable|array',
                    'info_socioeco.transportes.*' => 'string|max:30',
                    'info_socioeco.traslado_horas' => 'nullable|integer|min:0|max:23',
                    'info_socioeco.traslado_minutos' => 'nullable|integer|min:0|max:59',
                    'info_socioeco.estado_civil_alumno' => 'nullable|string|max:15',
                    'info_socioeco.num_hijos' => 'nullable|integer|min:0',
                    'info_socioeco.edades_hijos' => 'nullable|string|max:20',
                    'info_socioeco.monto_apoyo' => 'nullable|integer|min:0',
                    'info_socioeco.gasto_comida_transporte' => 'nullable|integer|min:0',
                    'info_socioeco.comidas_diarias' => 'nullable|integer|min:1|max:10',
                ]);
                
                $socioecoData = $socioecoData['info_socioeco'];
                
                InfoSocioeco::updateOrCreate(
                    ['id_alumno' => $alumnoId],
                    [
                        'auto_propio_alumno' => $socioecoData['auto_propio_alumno'],
                        'transporte' => $socioecoData['transportes'] ?? [],
                        'traslado_horas' => $socioecoData['traslado_horas'],
                        'traslado_minutos' => $socioecoData['traslado_minutos'],
                        'estado_civil_alumno' => $socioecoData['estado_civil_alumno'],
                        'num_hijos' => $socioecoData['num_hijos'] ?? 0,
                        'edades_hijos' => $socioecoData['edades_hijos'],
                        'monto_apoyo' => $socioecoData['monto_apoyo'],
                        'gasto_comida_transporte' => $socioecoData['gasto_comida_transporte'],
                        'comidas_diarias' => $socioecoData['comidas_diarias'] ?? 3,
                    ]
                );
            }
            
            // SECCIÓN 4.1 - DATOS ACADÉMICOS (HERRAMIENTAS)
            if ($request->has('datos_academicos')) {
                $academicosData = $request->validate([
                    'datos_academicos.dispositivos' => 'nullable|array',
                    'datos_academicos.dispositivos.*' => 'in:1,2,3,4',
                    'datos_academicos.otro_dispositivo' => 'nullable|string|max:30',
                ]);
                
                $academicosData = $academicosData['datos_academicos'];
                
                $dispositivosBinario = null;
                if (!empty($academicosData['dispositivos'])) {
                    $dispositivosBinario = DatosAcademicos::dispositivosToBinario($academicosData['dispositivos']);
                }
                
                DatosAcademicos::updateOrCreate(
                    ['id_alumno' => $alumnoId],
                    [
                        'dispositivos' => $dispositivosBinario,
                        'otro_dispositivo' => $academicosData['otro_dispositivo'],
                    ]
                );
            }
            
            // SECCIÓN 4.2 - PROBLEMAS DE APRENDIZAJE
            if ($request->has('problema_aprendizaje')) {
                $problemaData = $request->validate([
                    'problema_aprendizaje.tiene_problema' => 'required|boolean',
                    'problema_aprendizaje.caracteristicas' => 'nullable|array',
                    'problema_aprendizaje.caracteristicas.*' => 'in:1,2,3,4,5,6,7,8,9,10,11,12',
                    'problema_aprendizaje.otro_problema' => 'nullable|string|max:255',
                ]);
                
                $problemaData = $problemaData['problema_aprendizaje'];
                
                if ($problemaData['tiene_problema']) {
                    $caracteristicasBinario = null;
                    if (!empty($problemaData['caracteristicas'])) {
                        $caracteristicasBinario = ProblemaAprendizaje::problemasToBinario($problemaData['caracteristicas']);
                    }
                    
                    ProblemaAprendizaje::updateOrCreate(
                        ['id_alumno' => $alumnoId],
                        [
                            'caracteristicas_especificas' => $caracteristicasBinario,
                            'otro_problema' => $problemaData['otro_problema'],
                        ]
                    );
                } else {
                    // Si no tiene problema, eliminar el registro
                    ProblemaAprendizaje::where('id_alumno', $alumnoId)->delete();
                }
            }
            
            // SECCIÓN 5 - DATOS GENERALES DE SALUD
            if ($request->has('datos_salud')) {
                $saludData = $request->validate([
                    'datos_salud.estatura' => 'required|integer|min:50|max:250',
                    'datos_salud.peso' => 'required|numeric|min:10|max:300',
                    'datos_salud.tipo_sangre' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
                    'datos_salud.cuadro_basico_vacunas' => 'required|boolean',
                    'datos_salud.usa_anteojos' => 'required|boolean',
                    'datos_salud.graduacion_anteojos' => 'nullable|numeric|min:0|max:10',
                    'datos_salud.cirugia' => 'nullable|string|max:255',
                    'datos_salud.alergia' => 'nullable|string|max:255',
                    'datos_salud.limitante_fisico' => 'nullable|string|max:255',
                    'datos_salud.problema_auditivo' => 'nullable|string|max:255',
                    'datos_salud.adiccion' => 'nullable|string|max:255',
                    'datos_salud.padecimiento_emocional' => 'nullable|string|max:255',
                    'datos_salud.enfermedad_actual' => 'nullable|string|max:255',
                    'datos_salud.tiene_sintomas' => 'required|boolean',
                    'datos_salud.sintomas' => 'nullable|array',
                    'datos_salud.sintomas.*' => 'in:1,2,3,4,5',
                    'datos_salud.otro_sintoma' => 'nullable|string|max:255',
                    'datos_salud.medicamento_controlado' => 'nullable|string|max:255',
                    'datos_salud.alergia_medicamento' => 'nullable|string|max:255',
                    'datos_salud.motivo_hospitalizacion' => 'nullable|string|max:255',
                    'datos_salud.diabetes' => 'required|boolean',
                    'datos_salud.hipertension' => 'required|boolean',
                    'datos_salud.dolores_cabeza' => 'required|boolean',
                    'datos_salud.dolores_estomago' => 'required|boolean',
                    'datos_salud.frecuencia_medico' => 'required|integer|min:-1',
                    'datos_salud.frecuencia_dentista' => 'required|integer|min:-1',
                ]);
                
                $saludData = $saludData['datos_salud'];
                
                // Convertir síntomas a binario
                $sintomasBinario = null;
                if ($saludData['tiene_sintomas'] && !empty($saludData['sintomas'])) {
                    $sintomasBinario = DatosSalud::sintomasToBinario($saludData['sintomas']);
                }
                
                DatosSalud::updateOrCreate(
                    ['id_alumno' => $alumnoId],
                    [
                        'estatura' => $saludData['estatura'],
                        'peso' => $saludData['peso'],
                        'tipo_sangre' => $saludData['tipo_sangre'],
                        'cuadro_basico_vacunas' => $saludData['cuadro_basico_vacunas'],
                        'graduacion_anteojos' => $saludData['usa_anteojos'] ? $saludData['graduacion_anteojos'] : null,
                        'cirugia' => $saludData['cirugia'],
                        'alergia' => $saludData['alergia'],
                        'limitante_fisico' => $saludData['limitante_fisico'],
                        'problema_auditivo' => $saludData['problema_auditivo'],
                        'adiccion' => $saludData['adiccion'],
                        'padecimiento_emocional' => $saludData['padecimiento_emocional'],
                        'enfermedad_actual' => $saludData['enfermedad_actual'],
                        'sintomas_cuales' => $sintomasBinario,
                        'otro_sintoma' => $saludData['tiene_sintomas'] ? $saludData['otro_sintoma'] : null,
                        'medicamento_controlado' => $saludData['medicamento_controlado'],
                        'alergia_medicamento' => $saludData['alergia_medicamento'],
                        'diabetes' => $saludData['diabetes'],
                        'hipertension' => $saludData['hipertension'],
                        'motivo_hospitalizacion' => $saludData['motivo_hospitalizacion'],
                        'dolores_cabeza' => $saludData['dolores_cabeza'],
                        'dolores_estomago' => $saludData['dolores_estomago'],
                        'frecuencia_medico' => $saludData['frecuencia_medico'],
                        'frecuencia_dentista' => $saludData['frecuencia_dentista'],
                    ]
                );
            }
            
            // SECCIÓN 6 - ACTIVIDADES RECREATIVAS
            if ($request->has('actividades_recreativas')) {
                $actividadesData = $request->validate([
                    'actividades_recreativas.pasatiempo_favorito' => 'nullable|string|max:100',
                    'actividades_recreativas.horas_pasatiempo' => 'nullable|integer|min:0|max:168',
                    'actividades_recreativas.deportes' => 'nullable|array',
                    'actividades_recreativas.deportes.*' => 'string|max:50',
                    'actividades_recreativas.horas_deporte' => 'nullable|integer|min:0|max:168',
                    'actividades_recreativas.horas_tv' => 'nullable|integer|min:0|max:24',
                    'actividades_recreativas.horas_compu' => 'nullable|integer|min:0|max:24',
                    'actividades_recreativas.uso_compu' => 'nullable|string|max:50',
                    'actividades_recreativas.chatea' => 'required|boolean',
                    'actividades_recreativas.temas_chat' => 'nullable|string|max:255',
                ]);
                
                $actividadesData = $actividadesData['actividades_recreativas'];
                
                ActividadesRecreativas::updateOrCreate(
                    ['id_alumno' => $alumnoId],
                    [
                        'pasatiempo_favorito' => $actividadesData['pasatiempo_favorito'],
                        'horas_pasatiempo_dedicadas' => $actividadesData['horas_pasatiempo'],
                        'deporte_practicado' => $actividadesData['deportes'] ?? [],
                        'horas_deporte_dedicadas' => $actividadesData['horas_deporte'],
                        'horas_dia_tv' => $actividadesData['horas_tv'],
                        'horas_dia_compu' => $actividadesData['horas_compu'],
                        'uso_frecuente_compu' => $actividadesData['uso_compu'],
                        'temas_quien_chat' => $actividadesData['chatea'] ? $actividadesData['temas_chat'] : null,
                    ]
                );
            }
            
            // Confirmar la transacción
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Todos los datos del alumno han sido actualizados correctamente.'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en actualizarAlumnoCompleto: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al guardar los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    // Elimina una beca asociada a un alumno
    public function eliminarBecaAlumno(Request $request)
    {
        $user = Auth::user();
        
        // Verificar permisos de edición
        if (!$this->puedeRegistrar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'], 403);
        }
        
        $request->validate([
            'current_password' => 'required|current_password',
            'id_beca' => 'required'
        ]);
        
        // Buscar el ID del alumno actual (debes obtenerlo de alguna manera)
        // Asumiendo que tienes el ID del alumno en sesión o en el request
        $alumnoId = $request->input('id_alumno');
        if (!$alumnoId) {
            return response()->json(['success' => false, 'message' => 'No se pudo identificar al alumno.']);
        }
        
        try {
            $eliminado = AlumnoBeca::where('id_alumno', $alumnoId)
                ->where('id_beca', $request->id_beca)
                ->delete();
            
            if ($eliminado) {
                return response()->json(['success' => true, 'message' => 'Beca eliminada correctamente.']);
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontró la beca asociada.']);
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar beca-alumno: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar la beca.'], 500);
        }
    }
}