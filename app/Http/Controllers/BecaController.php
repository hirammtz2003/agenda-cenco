<?php

namespace App\Http\Controllers;

use App\Models\Beca;
use App\Helpers\PrivilegiosHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BecaController extends Controller
{
    /**
     * Verificar si el usuario tiene acceso al módulo
     */
    private function verificarAcceso()
    {
        $user = Auth::user();
                
        return true;
    }

    /**
     * Verificar si el usuario puede editar (G en posición 1 o 2)
     */
    private function puedeEditar()
    {
        $user = Auth::user();
    }

    public function index()
    {
        $this->verificarAcceso();
        
        $user = Auth::user();
        
        return view('usuarios.becas.becas');
    }

    /**
     * Listar becas (con búsqueda opcional)
     */
    public function listar(Request $request)
    {
        $this->verificarAcceso();
        
        $query = Beca::query();

        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('tipo_beca', 'LIKE', "%{$busqueda}%")
                  ->orWhere('descripcion', 'LIKE', "%{$busqueda}%");
            });
        }

        $becas = $query->orderBy('tipo_beca')->get();
        $totalEncontrados = $becas->count();

        return response()->json([
            'success' => true,
            'becas' => $becas,
            'total' => $totalEncontrados
        ]);
    }

    /**
     * Guardar nueva beca o actualizar existente
     */
    public function guardar(Request $request)
    {
        $this->verificarAcceso();
        
        if (!$this->puedeEditar()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta operación.'
            ], 403);
        }

        $request->validate([
            'current_password' => 'required|current_password',
            'id' => 'nullable|exists:becas,id',
            'tipo_beca' => 'required|string|max:20',
            'descripcion' => 'nullable|string|max:255',
        ]);

        // Verificar unicidad del tipo de beca (solo para nuevos o si cambió)
        if ($request->filled('id')) {
            $beca = Beca::findOrFail($request->id);
            $reglaUnica = Rule::unique('becas', 'tipo_beca')->ignore($beca->id);
        } else {
            $reglaUnica = Rule::unique('becas', 'tipo_beca');
        }

        $request->validate([
            'tipo_beca' => ['required', 'string', 'max:20', $reglaUnica],
        ]);

        $data = [
            'tipo_beca' => $request->tipo_beca,
            'descripcion' => $request->descripcion,
        ];

        if ($request->filled('id')) {
            // Actualizar
            $beca = Beca::findOrFail($request->id);
            $beca->update($data);
            $mensaje = 'Beca actualizada correctamente.';
        } else {
            // Crear nueva
            $beca = Beca::create($data);
            $mensaje = 'Beca creada correctamente.';
        }

        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'beca' => $beca
        ]);
    }

    /**
     * Obtener una beca específica para editar
     */
    public function obtener($id)
    {
        $this->verificarAcceso();
        
        if (!$this->puedeEditar()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar.'
            ], 403);
        }
        
        $beca = Beca::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'beca' => $beca
        ]);
    }

    /**
     * Eliminar una beca
     */
    public function eliminar(Request $request, $id)
    {
        $this->verificarAcceso();
        
        if (!$this->puedeEditar()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar.'
            ], 403);
        }

        $request->validate([
            'current_password' => 'required|current_password',
        ]);

        $beca = Beca::findOrFail($id);
        $beca->delete();

        return response()->json([
            'success' => true,
            'message' => 'Beca eliminada correctamente.'
        ]);
    }
}