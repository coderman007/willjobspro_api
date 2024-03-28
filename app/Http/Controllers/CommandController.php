<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class CommandController extends Controller
{
    public function linkStorage()
    {
        // Ejecuta el comando storage:link
        $exitCode = Artisan::call('storage:link');

        // Puedes verificar si el comando fue exitoso
        if ($exitCode === 0) {
            return response()->json(['success' => true, 'message' => 'El enlace simbólico se creó correctamente.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Hubo un problema al crear el enlace simbólico.']);
        }
    }

    public function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return response()->json(['success' => true, 'message' => 'Toda la caché ha sido borrada.']);
    }

}
