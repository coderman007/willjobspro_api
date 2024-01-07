<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Company $company): bool
    {
        // Por ejemplo, un usuario puede ver una empresa si es el propietario de la empresa o si es un administrador
        return $user->id === $company->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        // Por ejemplo, un usuario puede actualizar su propia empresa
        return $user->id === $company->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        // Por ejemplo, un usuario puede eliminar su propia empresa
        return $user->id === $company->user_id;
    }
}
