<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobTypeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Rutas abiertas
Route::post("register", [AuthController::class, 'register']);
Route::post("login", [AuthController::class, 'login']);

// Rutas comunes a todos los usuarios, protegidas con autenticación.
Route::group(
    [
        'prefix' => 'v1',
        'middleware' => ['auth:sanctum'],
    ],
    function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::delete('logout', [AuthController::class, 'logOut']);
        Route::put('update-password', [AuthController::class, 'updatePassword']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('candidates', CandidateController::class);
        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('invoices', InvoiceController::class);
        Route::get('subscriptions', [SubscriptionController::class, 'getSubscriptions']);
        Route::get('subscriptions/{id}', [SubscriptionController::class, 'getSubscription']);
        Route::get('jobs', [JobController::class, 'index']);
        Route::get('jobs/{id}', [JobController::class, 'show']);
        Route::get('applications', [ApplicationController::class, 'index']);
        Route::get('applications/{id}', [ApplicationController::class, 'show']);



        //Rutas para usuarios con el rol 'admin'

        // Rutas para crear, actualizar y eliminar categorías de ofertas de trabajo
        Route::middleware(['checkAdminRole'])->group(function () {
            Route::post('job-categories', [JobCategoryController::class, 'store']);
            Route::put('job-categories/{job_category}', [JobCategoryController::class, 'update']);
            Route::delete('job-categories/{job_category}', [JobCategoryController::class, 'destroy']);

            // Rutas para crear, actualizar y eliminar tipos de ofertas de trabajo
            Route::post('job-types', [JobTypeController::class, 'store']);
            Route::put('job-types/{job_type}', [JobTypeController::class, 'update']);
            Route::delete('job-types/{job_type}', [JobTypeController::class, 'destroy']);


            // Rutas para crear, actualizar y eliminar tipos de ofertas de trabajo
            Route::post('subscription-plans', [SubscriptionPlanController::class, 'store']);
            Route::put('subscription-plans/{subscription_plan}', [SubscriptionPlanController::class, 'update']);
            Route::delete('subscription-plans/{subscription_plan}', [SubscriptionPlanController::class, 'destroy']);
        });


        //Rutas para usuarios con el rol 'company'

        // Rutas para crear, actualizar y eliminar ofertas de trabajo
        Route::middleware(['checkCompanyRole'])->group(function () {
            Route::post('jobs', [JobController::class, 'store']);
            Route::put('jobs/{job}', [JobController::class, 'update']);
            Route::delete('jobs/{job}', [JobController::class, 'destroy']);
        });

        //Rutas para usuarios con el rol 'candidate'

        // Rutas para crear, actualizar y eliminar aplicaciones de trabajo
        Route::middleware(['checkCandidateRole'])->group(function () {
            Route::post('applications', [ApplicationController::class, 'store']);
            Route::put('applications/{application}', [ApplicationController::class, 'update']);
            Route::delete('applications/{application}', [ApplicationController::class, 'destroy']);

            // Rutas para suscripciones
            Route::post('subscriptions', [SubscriptionController::class, 'subscribe']);
            Route::put('subscriptions/{id}', [SubscriptionController::class, 'updateSubscription']);
            Route::delete('subscriptions/{id}', [SubscriptionController::class, 'cancelSubscription']);

            // Rutas para pagos
            Route::post('payments', [PaymentController::class, 'makePayment']);
            Route::get('payments', [PaymentController::class, 'getPayments']);
            Route::get('payments/{id}', [PaymentController::class, 'getPayment']);
        });
    }
);
