<?php

namespace App\Policies;

use App\Models\TransactionHistory;
use App\Models\User;

class TransactionHistoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Por ejemplo, cualquier usuario autenticado puede ver todo el historial de transacciones
        return $user->is_authenticated;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TransactionHistory $transactionHistory): bool
    {
        // Por ejemplo, cualquier usuario autenticado puede ver una transacción específica
        return $user->is_authenticated;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Por ejemplo, un usuario puede crear transacciones si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TransactionHistory $transactionHistory): bool
    {
        // Por ejemplo, un usuario puede actualizar transacciones si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TransactionHistory $transactionHistory): bool
    {
        // Por ejemplo, un usuario puede eliminar transacciones si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TransactionHistory $transactionHistory): bool
    {
        // Puedes definir la lógica según tus necesidades
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TransactionHistory $transactionHistory): bool
    {
        // Puedes definir la lógica según tus necesidades
        return false;
    }
}
