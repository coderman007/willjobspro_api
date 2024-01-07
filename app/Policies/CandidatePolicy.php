<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\User;

class CandidatePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Candidate $candidate): bool
    {
        // Por ejemplo, un usuario puede ver un candidato si es el propietario del candidato o si es un administrador
        return $user->id === $candidate->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Candidate $candidate): bool
    {
        // Por ejemplo, un usuario puede actualizar su propio candidato
        return $user->id === $candidate->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Candidate $candidate): bool
    {
        // Por ejemplo, un usuario puede eliminar su propio candidato
        return $user->id === $candidate->user_id;
    }
}
