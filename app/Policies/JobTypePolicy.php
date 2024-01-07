<?php

namespace App\Policies;

use App\Models\JobType;
use App\Models\User;

class JobTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Por ejemplo, cualquier usuario autenticado puede ver todos los tipos de trabajo
        return $user->is_authenticated;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobType $jobType): bool
    {
        // Por ejemplo, cualquier usuario autenticado puede ver un tipo de trabajo específico
        return $user->is_authenticated;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Por ejemplo, un usuario puede crear tipos de trabajo si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobType $jobType): bool
    {
        // Por ejemplo, un usuario puede actualizar tipos de trabajo si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobType $jobType): bool
    {
        // Por ejemplo, un usuario puede eliminar tipos de trabajo si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JobType $jobType): bool
    {
        // Puedes definir la lógica según tus necesidades
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JobType $jobType): bool
    {
        // Puedes definir la lógica según tus necesidades
        return false;
    }
}
