<?php

namespace App\Policies;

use App\Models\JobCategory;
use App\Models\User;

class JobCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Por ejemplo, un usuario puede ver todas las categorías de trabajo si está autenticado
        return $user->is_authenticated;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobCategory $jobCategory): bool
    {
        // Por ejemplo, un usuario puede ver una categoría de trabajo si está autenticado
        return $user->is_authenticated;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Por ejemplo, un usuario puede crear categorías de trabajo si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobCategory $jobCategory): bool
    {
        // Por ejemplo, un usuario puede actualizar categorías de trabajo si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobCategory $jobCategory): bool
    {
        // Por ejemplo, un usuario puede eliminar categorías de trabajo si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JobCategory $jobCategory): bool
    {
        // Puedes definir la lógica según tus necesidades
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JobCategory $jobCategory): bool
    {
        // Puedes definir la lógica según tus necesidades
        return false;
    }
}
