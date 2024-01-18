<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->hasRole('company')) {
            // Verificar si el usuario tiene permisos sobre el recurso específico (company_id)
            $companyId = $request->input('company_id');

            if (!$this->userOwnsCompany($companyId)) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            return $next($request);
        }

        return response()->json(['error' => 'Only users with role \'company\' can access this resource.'], 403);
    }

    protected function userOwnsCompany($companyId)
    {
        return auth()->user()->company->id == $companyId;
    }
}
