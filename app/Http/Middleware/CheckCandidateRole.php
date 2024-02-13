<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCandidateRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasRole('candidate')) {
            // Verificar si el usuario tiene permisos sobre el recurso específico (candidate_id)
            $candidateId = $request->input('candidate_id');

            if (!$this->userOwnsCandidate($candidateId)) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            return $next($request);
        }

        return response()->json(['error' => 'Only users with role \'candidate\' can access this resource.'], 403);
    }

    protected function userOwnsCandidate($candidateId)
    {
        return auth()->user()->candidate->id == $candidateId;
    }
}
