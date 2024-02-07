<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobTypeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rutas abiertas
Route::post("register", [AuthController::class, 'register']);
Route::post("login", [AuthController::class, 'login']);
Route::get('jobs', [JobController::class, 'index']);
Route::get('jobs/{id}', [JobController::class, 'show']);
Route::get('companies', [CompanyController::class, 'index']);
Route::get('companies/{id}', [CompanyController::class, 'show']);

Route::group(
    [
        'prefix' => 'v1',
        'middleware' => ['auth:sanctum'],
    ],
    function () {
        // Rutas comunes a todos los usuarios, protegidas sólo con autenticación.
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('update-password', [AuthController::class, 'updatePassword']);
        Route::delete('logout', [AuthController::class, 'logOut']);
        Route::get('subscriptions', [SubscriptionController::class, 'getSubscriptions']);
        Route::get('subscriptions/{id}', [SubscriptionController::class, 'getSubscription']);
        Route::get('applications', [ApplicationController::class, 'index']);
        Route::get('applications/{id}', [ApplicationController::class, 'show']);
        Route::get('skills', [SkillController::class, 'index']);
        Route::get('skills/{id}', [SkillController::class, 'show']);

        //Rutas protegidas con autenticación y con el middleware 'checkAdminRole'
        Route::middleware(['checkAdminRole'])->group(function () {

            // Rutas para gestionar usuarios
            Route::apiResource('users', UserController::class);

            // Rutas para gestionar compañías
            Route::post('companies', [CompanyController::class, 'store']);
            Route::put('companies/{id}', [CompanyController::class, 'update']);
            Route::delete('companies/{id}', [CompanyController::class, 'destroy']);

            // Rutas para gestionar candidatos
            Route::get('candidates', [CandidateController::class, 'index']);
            Route::post('candidates', [CandidateController::class, 'store']);
            Route::post('candidates/{candidate}/skills/{skill}', [CandidateController::class, 'addSkills']);
            Route::delete('candidates/{candidate}/skills/{skill}', [CandidateController::class, 'removeSkills']);
            Route::get('candidates/{id}', [CandidateController::class, 'show']);
            Route::put('candidates/{id}', [CandidateController::class, 'update']);
            Route::delete('candidates/{id}', [CandidateController::class, 'destroy']);

            // Rutas para crear, actualizar y eliminar categorías de ofertas de trabajo
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


        //Rutas protegidas con autenticación y con el middleware 'checkCompanyRole'

        // Rutas para crear, actualizar y eliminar ofertas de trabajo
        Route::middleware(['checkCompanyRole'])->group(function () {

            Route::get('candidates', [CandidateController::class, 'index']);
            Route::post('candidates', [CandidateController::class, 'store']);
            Route::post('jobs', [JobController::class, 'store']);
            Route::put('jobs/{job}', [JobController::class, 'update']);
            Route::delete('jobs/{job}', [JobController::class, 'destroy']);
        });

        //Rutas protegidas con autenticación y con el middleware 'checkCandidateRole'

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
