<?php

namespace App\Policies;

use App\Models\SubscriptionPlan;
use App\Models\User;

class SubscriptionPlanPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        // Por ejemplo, un usuario puede ver un plan de suscripción si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Por ejemplo, un usuario puede crear planes de suscripción si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        // Por ejemplo, un usuario puede actualizar planes de suscripción si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        // Por ejemplo, un usuario puede eliminar planes de suscripción si es un administrador
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        // Puedes definir la lógica según tus necesidades
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        // Puedes definir la lógica según tus necesidades
        return false;
    }
}
