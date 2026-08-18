<?php

namespace App\Helpers;

class PrivilegiosHelper
{
    /**
     * Verifica si el usuario puede editar (privilegio G en posición 0)
     */
    public static function puedeEditar($user)
    {
        return $user && isset($user->privilegios[0]) && $user->privilegios[0] === 'G';
    }

    /**
     * Verifica si el usuario puede consultar (privilegio G o C en posición 0)
     */
    public static function puedeConsultar($user)
    {
        return $user && isset($user->privilegios[0]) && in_array($user->privilegios[0], ['G', 'C']);
    }

        /**
     * Verifica si el usuario puede editar a nivel General (privilegio G en posición 1)
     */
    public static function gestionGeneral($user)
    {
        return $user && isset($user->privilegios[1]) && $user->privilegios[1] === 'G';
    }

    /**
     * Verifica si el usuario puede consultar a nivel General (privilegio G o C en posición 1)
     */
    public static function consultaGeneral($user)
    {
        return $user && isset($user->privilegios[1]) && in_array($user->privilegios[1], ['G', 'C']);
    }

    /**
     * Verifica si el usuario puede editar a nivel Grado (privilegio G en posición 2)
     */
    public static function gestionGrado($user)
    {
        return $user && isset($user->privilegios[2]) && $user->privilegios[2] === 'G';
    }

    /**
     * Verifica si el usuario puede consultar a nivel Grado (privilegio G o C en posición 2)
     */
    public static function consultaGrado($user)
    {
        return $user && isset($user->privilegios[2]) && in_array($user->privilegios[2], ['G', 'C']);
    }

    /**
     * Verifica si el usuario puede editar a nivel Grupo (privilegio G en posición 3)
     */
    public static function gestionGrupo($user)
    {
        return $user && isset($user->privilegios[3]) && $user->privilegios[3] === 'G';
    }

    /**
     * Verifica si el usuario puede consultar a nivel Grupo (privilegio G o C en posición 3)
     */
    public static function consultaGrupo($user)
    {
        return $user && isset($user->privilegios[3]) && in_array($user->privilegios[3], ['G', 'C']);
    }

    /**
     * Verifica si el usuario puede editar a nivel Individual (privilegio G en posición 4)
     */
    public static function gestionIndividual($user)
    {
        return $user && isset($user->privilegios[4]) && $user->privilegios[4] === 'G';
    }

    /**
     * Verifica si el usuario puede consultar a nivel Individual (privilegio G o C en posición 3)
     */
    public static function consultaIndividual($user)
    {
        return $user && isset($user->privilegios[4]) && in_array($user->privilegios[4], ['G', 'C']);
    }
}