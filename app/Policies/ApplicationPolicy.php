<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Por ejemplo, cualquier usuario autenticado puede ver todas las solicitudes de aplicación
        return $user->is_authenticated;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Application $application): bool
    {
        // Por ejemplo, cualquier usuario autenticado puede ver una solicitud de aplicación específica
        return $user->is_authenticated;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Por ejemplo, cualquier usuario autenticado puede crear nuevas solicitudes de aplicación
        return $user->is_authenticated;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Application $application): bool
    {
        // Por ejemplo, el propietario de la solicitud de aplicación puede actualizarla
        return $user->id === $application->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Application $application): bool
    {
        // Por ejemplo, el propietario de la solicitud de aplicación puede eliminarla
        return $user->id === $application->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Application $application): bool
    {
        // Puedes definir la lógica según tus necesidades
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Application $application): bool
    {
        // Puedes definir la lógica según tus necesidades
        return false;
    }
}
