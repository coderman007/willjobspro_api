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

// Rutas protegidas con autenticación
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
        Route::apiResource('job-categories', JobCategoryController::class);
        Route::apiResource('job-types', JobTypeController::class);
        Route::apiResource('subscription-plans', SubscriptionPlanController::class);

        // Rutas para ver y listar ofertas de trabajo
        Route::get('jobs', [JobController::class, 'index']);
        Route::get('jobs/{job}', [JobController::class, 'show']);

        // Rutas para crear, actualizar y eliminar ofertas de trabajo
        Route::middleware(['checkCompanyRole'])->group(function () {
            Route::post('jobs', [JobController::class, 'store']);
            Route::put('jobs/{job}', [JobController::class, 'update']);
            Route::delete('jobs/{job}', [JobController::class, 'destroy']);
        });
        Route::apiResource('applications', ApplicationController::class);

        // Rutas para suscripciones
        Route::post('subscriptions', [SubscriptionController::class, 'subscribe']);
        Route::get('subscriptions', [SubscriptionController::class, 'getSubscriptions']);
        Route::get('subscriptions/{id}', [SubscriptionController::class, 'getSubscription']);
        Route::put('subscriptions/{id}', [SubscriptionController::class, 'updateSubscription']);
        Route::delete('subscriptions/{id}', [SubscriptionController::class, 'cancelSubscription']);

        // Rutas para pagos
        Route::post('payments', [PaymentController::class, 'makePayment']);
        Route::get('payments', [PaymentController::class, 'getPayments']);
        Route::get('payments/{id}', [PaymentController::class, 'getPayment']);
    }
);
