<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'apellido1',
        'apellido2',
        'email_personal',
        'email_institucional',
        'telefono',
        'num_empleado',
        'password',
        'pw_recuperacion',
        'tipo',
        'privilegios',
        'estatus',
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'pw_recuperacion' => 'boolean',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'pw_recuperacion' => 'boolean',
            'estatus' => 'boolean',
        ];
    }

        // Accessor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return trim($this->nombre . ' ' . $this->apellido1 . ' ' . ($this->apellido2 ?? ''));
    }

        public function getApellidosAttribute()
    {
        return trim($this->apellido1 . ' ' . ($this->apellido2 ?? ''));
    }

    // Accessor para email (si algún paquete espera 'email')
    public function getEmailAttribute()
    {
        return $this->email_institucional;
    }

    // Mutator para compatibilidad
    public function setEmailAttribute($value)
    {
        $this->attributes['email_institucional'] = $value;
    }
    
    // Para usar num_empleado como username
    public function username()
    {
        return 'num_empleado';
    }

    // En app/Models/User.php, dentro de la clase User

/**
 * Verifica si el usuario tiene privilegio de administración general (G en posición 0)
 */
public function tienePrivilegioG0(): bool
{
    return !empty($this->privilegios) && $this->privilegios[0] === 'G';
}

/**
 * Verifica si el usuario tiene privilegio de consulta (C en posición 0)
 */
public function tienePrivilegioC0(): bool
{
    return !empty($this->privilegios) && $this->privilegios[0] === 'C';
}

/**
 * Verifica si puede editar usuarios (G en posición 0)
 */
public function puedeEditarUsuarios(): bool
{
    return $this->tienePrivilegioG0();
}

/**
 * Verifica si puede solo consultar usuarios (C en posición 0)
 */
public function puedeConsultarUsuarios(): bool
{
    return $this->tienePrivilegioC0() || $this->tienePrivilegioG0();
}

/**
 * Obtiene la clase CSS para el badge de tipo
 */
public function getTipoBadgeClass(): string
{
    return match($this->tipo) {
        'Administrador' => 'bg-danger',
        'Directivo' => 'bg-warning text-dark',
        'Docente' => 'bg-primary',
        'Trabajo Social' => 'bg-info',
        default => 'bg-secondary'
    };
}

/**
 * Obtiene los badges de privilegios para mostrar
 */
public function getPrivilegiosBadges(): array
{
    $badges = [];
    
    if (empty($this->privilegios) || strlen($this->privilegios) < 5) {
        return $badges;
    }
    
    $privilegios = $this->privilegios;
    
    // Posición 0 - Nivel general
    if ($privilegios[0] === 'G') {
        $badges[] = '<span class="badge bg-primary mb-1">Administración general del Sistema</span>';
    } elseif ($privilegios[0] === 'C') {
        $badges[] = '<span class="badge bg-info mb-1">Sólo consulta de Usuarios</span>';
    }
    
    // Posiciones 1-4 para C (Consulta)
    if ($privilegios[1] === 'C') {
        $badges[] = '<span class="badge bg-secondary mb-1">Sólo consulta general de Alumnos</span>';
    }
    if ($privilegios[2] === 'C') {
        $badges[] = '<span class="badge bg-danger mb-1">Consulta por Grado</span>';
    }
    if ($privilegios[3] === 'C') {
        $badges[] = '<span class="badge bg-warning mb-1">Consulta por Grupo</span>';
    }
    if ($privilegios[4] === 'C') {
        $badges[] = '<span class="badge bg-success mb-1">Consulta individual de Alumnos</span>';
    }
    
    // Posiciones 1-4 para G (Gestión)
    if ($privilegios[1] === 'G') {
        $badges[] = '<span class="badge bg-secondary mb-1">Gestión general de Alumnos</span>';
    }
    if ($privilegios[2] === 'G') {
        $badges[] = '<span class="badge bg-danger mb-1">Gestión por Grado</span>';
    }
    if ($privilegios[3] === 'G') {
        $badges[] = '<span class="badge bg-warning mb-1">Gestión por Grupo</span>';
    }
    if ($privilegios[4] === 'G') {
        $badges[] = '<span class="badge bg-success mb-1">Gestión individual de Alumnos</span>';
    }
    
    return $badges;
}
}
