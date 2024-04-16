<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Job;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        try {
            // Definir el número de elementos por página
            $perPage = $request->query('per_page', 10);

            // Obtener el usuario autenticado
            $user = Auth::user();

            // Inicializar la consulta de Eloquent
            $query = Application::with(['candidate', 'job']);

            // Filtrar las aplicaciones según el tipo de usuario
            switch ($user->getUserType()) {
                case 'admin':
                    // Los administradores tienen acceso total, no se aplican filtros adicionales
                    break;
                case 'company':
                    // Filtrar las aplicaciones de trabajo por las ofertas publicadas por la compañía
                    $query->whereHas('job', function ($jobQuery) use ($user) {
                        $jobQuery->where('company_id', $user->company->id);
                    });
                    break;
                case 'candidate':
                    // Filtrar las aplicaciones de trabajo por el candidato autenticado
                    $query->where('candidate_id', $user->candidate->id);
                    break;
                default:
                    // Manejar otros roles según sea necesario
                    break;
            }

            // Aplicar otros filtros dinámicos si es necesario
            foreach ($this->getFilters() as $filter) {
                if ($request->filled($filter) && Schema::hasColumn('applications', $filter)) {
                    $query->where($filter, $request->query($filter));
                }
            }

            // Ordenar por fecha de aplicación de forma predeterminada
            $query->orderBy('application_date', 'desc');

            // Obtener las aplicaciones
            $applications = $query->paginate($perPage)->items();

            // Transformar los resultados utilizando el recurso API
            $formattedApplications = ApplicationResource::collection($applications);

            return response()->json(['data' => $formattedApplications], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while getting the application list!', 'details' => $e->getMessage()], 500);
        }
    }


    // Función auxiliar para obtener los filtros dinámicos
    private function getFilters(): array
    {
        return Schema::hasTable('applications') ? DB::getSchemaBuilder()->getColumnListing('applications') : [];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreApplicationRequest $request
     * @return JsonResponse
     */
    public function store(StoreApplicationRequest $request): JsonResponse
    {
        try {
            // Obtener el usuario autenticado
            $user = Auth::user();

            // Verificar si el usuario autenticado tiene el rol de candidato
            if (!$user->hasRole('candidate')) {
                return response()->json(['error' => 'Only candidates can create applications.'], 403);
            }

            // Obtener el ID del candidato asociado al usuario autenticado
            $candidateId = $user->candidate->id;

            // Obtener el ID del trabajo desde la solicitud
            $jobId = $request->input('job_id');

            // Verificar si el trabajo proporcionado existe
            $job = Job::find($jobId);
            if (!$job) {
                return response()->json(['error' => 'Job not found.'], 404);
            }

            // Verificar si ya existe una aplicación para este usuario y trabajo
            $existingApplication = Application::where('candidate_id', $candidateId)
                ->where('job_id', $jobId)
                ->exists();

            if ($existingApplication) {
                return response()->json(['error' => 'An application for this job already exists.'], 422);
            }

            // Crear la aplicación asociada al candidato y al trabajo
            $validatedData = $request->validated();
            $validatedData['candidate_id'] = $candidateId;
            $application = Application::create($validatedData);

            return response()->json(['message' => 'Application created successfully!', 'data' => $application], 201);
        } catch (QueryException $e) {
            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'An error occurred in the database while creating the application.',
                'details' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the application!',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    protected function userOwnsCandidate($candidateId)
    {
        $user = auth()->user();

        // Verificar si el usuario está autenticado y tiene el rol 'candidate'
        if ($user && $user->hasRole('candidate')) {
            // Obtener el candidato asociado al usuario
            $userCandidate = $user->candidate;

            // Verificar si el candidato existe y su ID coincide con $candidateId
            if ($userCandidate && $userCandidate->id == $candidateId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Display the specified resource.
     *
     * @param Application $application
     * @return JsonResponse
     */

    public function show(Application $application)
    {
        try {
            // Obtener el usuario autenticado
            $user = Auth::user();

            // Declarar una variable para almacenar el recurso API
            $formattedApplication = null;

            // Verificar el tipo de usuario y sus permisos para acceder al detalle de la aplicación
            switch ($user->getUserType()) {
                case 'admin':
                    // Los administradores tienen acceso total
                    $formattedApplication = new ApplicationResource($application);
                    break;
                case 'company':
                    // Verificar si la aplicación está relacionada con una oferta de trabajo publicada por la compañía
                    if ($application->job->company_id !== $user->company->id) {
                        return response()->json(['error' => 'You do not have permissions to access this resource.'], 403);
                    }
                    $formattedApplication = new ApplicationResource($application);
                    break;
                case 'candidate':
                    // Verificar si la aplicación fue creada por el candidato autenticado
                    if ($application->candidate_id !== $user->candidate->id) {
                        return response()->json(['error' => 'You do not have permissions to access this resource.'], 403);
                    }
                    $formattedApplication = new ApplicationResource($application);
                    break;
                default:
                    // Manejar otros roles según sea necesario
                    break;
            }

            // Si el usuario tiene permisos, devolver el detalle de la aplicación utilizando el recurso API
            return response()->json(['data' => $formattedApplication], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the application!',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateApplicationRequest $request
     * @param Application $application
     * @return JsonResponse
     */
    public function update(UpdateApplicationRequest $request, Application $application): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // Verificar si el 'candidate_id' en la solicitud coincide con el del usuario autenticado
            $candidateId = $request->input('candidate_id');

            // Verificar si el usuario autenticado es el propietario de la aplicación
            if (!$this->userOwnsCandidate($candidateId) || $candidateId != $application->candidate_id) {
                return response()->json(['error' => 'You do not have permissions to perform this action on this resource.'], 403);
            }

            $application->update($validatedData);

            return response()->json(['data' => $application, 'message' => 'Application updated successfully!'], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the application!',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Application $application
     * @return JsonResponse
     */
    public function destroy(Application $application): JsonResponse
    {
        try {
            if (!$application) {
                return response()->json(['error' => 'Application not found.'], 404);
            }

            // Verificar si el 'candidate_id' de la aplicación coincide con el del usuario autenticado
            $candidateId = $application->candidate_id;

            // Verificar si el usuario autenticado es el propietario de la aplicación
            if (!$this->userOwnsCandidate($candidateId)) {
                return response()->json(['error' => 'You do not have permissions to perform this action on this resource.'], 403);
            }

            $application->delete();

            return response()->json(['message' => 'Application deleted successfully!'], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while deleting the application!',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status of the specified application.
     *
     * @param Request $request
     * @param Application $application
     * @return JsonResponse
     */
    public function updateStatus(Request $request, Application $application): JsonResponse
    {
        try {
            // Verificar si el usuario autenticado es una empresa
            if (!Auth::user()->hasRole('company')) {
                return response()->json(['error' => 'Only companies can update the status of an application.'], 403);
            }

            // Verificar si la aplicación está relacionada con una oferta de trabajo publicada por la empresa
            if ($application->job->company_id !== Auth::user()->company->id) {
                return response()->json(['error' => 'You do not have permissions to access this resource.'], 403);
            }

            // Validar la solicitud y actualizar el estado de la aplicación
            $request->validate([
                'status' => 'required|in:Pending,Reviewed,Accepted,Rejected',
            ]);

            $application->status = $request->input('status');

            // Si el estado es "Rejected", establecer la fecha de rechazo
            if ($request->input('status') === 'Rejected') {
                $application->rejection_date = now();
            }

            $application->save();

            return response()->json(['data' => $application, 'message' => 'Application status updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the application status!',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
