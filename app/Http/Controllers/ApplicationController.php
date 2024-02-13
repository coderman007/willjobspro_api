<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Job;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

            // Inicializar la consulta de Eloquent
            $query = Application::with(['candidate', 'job']);

            // Aplicar búsqueda por el título del trabajo
            if ($request->filled('search')) {
                $searchTerm = $request->query('search');
                $query->whereHas('job', function ($jobQuery) use ($searchTerm) {
                    $jobQuery->where('title', 'like', '%' . $searchTerm . '%');
                });
            }

            // Aplicar filtros dinámicos
            foreach ($this->getFilters() as $filter) {
                if ($request->filled($filter) && Schema::hasColumn('applications', $filter)) {
                    $query->where($filter, $request->query($filter));
                }
            }

            // Ordenar por fecha de aplicación de forma predeterminada
            $query->orderBy('application_date', 'desc');

            // Paginar los resultados
            $applications = $query->paginate($perPage);

            // Construir datos de paginación
            $paginationData = $applications->toArray();
            unset($paginationData['data']); // Eliminar los datos para evitar redundancia

            return response()->json(['data' => $applications, 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
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
            $validatedData = $request->validated();

            // Verificar si el 'candidate_id' en la solicitud coincide con el del usuario autenticado
            $candidateId = $request->input('candidate_id');

            // Asociar la aplicación al 'candidate_id' actual
            if (!$this->userOwnsCandidate($candidateId)) {
                return response()->json(['error' => 'You do not have permissions to perform this action on this resource.'], 403);
            }

            // Verificar si el 'job_id' proporcionado existe
            $jobId = $validatedData['job_id'];
            $job = Job::find($jobId);
            if (!$job) {
                return response()->json(['error' => 'Job not found.'], 404);
            }

            // Crear la aplicación
            $application = Application::create($validatedData);

            return response()->json(['data' => $application, 'message' => 'Application created successfully!'], 201);
        } catch (QueryException $e) {

            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'An error ocurred in database while creating the application.',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
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
            // Obtener la compañía asociada al usuario
            $userCandidate = $user->candidate;

            // Verificar si la compañía existe y su ID coincide con $candidateId
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
    public function show(Application $application): JsonResponse
    {
        try {
            return response()->json(['data' => $application], 200);
        } catch (\Exception $e) {
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

            if (!$this->userOwnsCandidate($candidateId)) {
                return response()->json(['error' => 'You do not have permissions to perform this action on this resource.'], 403);
            }

            $application->update($validatedData);

            return response()->json(['data' => $application, 'message' => 'Application updated successfully!'], 200);
        } catch (\Exception $e) {
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
                return response()->json(['error' => 'Application offer not found.'], 404);
            }
            // Verificar si el 'candidate_id' del trabajo coincide con el del usuario autenticado
            $candidateId = $application->candidate_id;

            if (!$this->userOwnsCandidate($candidateId)) {
                return response()->json(['error' => 'You do not have permissions to perform this action on this resource.'], 403);
            }
            $application->delete();
            return response()->json(['message' => 'Application deleted!'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while deleting the application!',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
