<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Por ejemplo, solo los administradores pueden ver otros perfiles de usuario
        return $user->is_admin && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Por ejemplo, un usuario puede actualizar su propio perfil
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Por ejemplo, solo los administradores pueden eliminar usuarios
        return $user->is_admin && $user->id !== $model->id;
    }
}
