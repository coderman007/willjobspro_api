<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class CompanyOwnershipValidator
{
    public static function validateOwnership(int $companyId)
    {
        $user = Auth::user();

        if ($user && $user->hasRole('company') && $user->company->id == $companyId) {
            return true;
        }

        return false;
    }
}
