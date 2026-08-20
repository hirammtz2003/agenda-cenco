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
        'pw_temporal',
        'tipo',
        'estatus'
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

    // Método para verificar si es admin
    public function isAdmin()
    {
        return $this->tipo === 'Administrador';
    }

    public function isDocente()
    {
        return $this->tipo === 'Docente';
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

    // Obtiene la clase CSS para el badge de tipo
    public function getTipoBadgeClass(): string
    {
        return match($this->tipo) {
            'Administrador' => 'bg-danger',
            'Docente' => 'bg-primary',
            default => 'bg-secondary'
        };
    }

    // Obtiene los badges de privilegios para mostrar
    public function getPrivilegiosBadges(): array
    {
        $badges = [];
        
        // Privilegios
        if ($tipo === 'Administrador') {
            $badges[] = '<span class="badge bg-primary mb-1">Administración general del Sistema</span>';
        } else {
            $badges[] = '<span class="badge bg-info mb-1">Reservación de horas</span>';
        }
        
        return $badges;
    }
}
