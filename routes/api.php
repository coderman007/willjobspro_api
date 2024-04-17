<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EducationLevelController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobTypeController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ZipCodeController;
use App\Http\Controllers\SkillCategoryController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkExperienceController;
use Illuminate\Support\Facades\Route;


// Rutas abiertas
Route::post("register", [AuthController::class, 'register']);
Route::post("login", [AuthController::class, 'login']);
Route::get('countries', [CountryController::class, 'index']);
Route::get('countries/{country}', [CountryController::class, 'show']);
Route::get('states', [StateController::class, 'index']);
Route::get('states/{state}', [StateController::class, 'show']);
Route::get('cities', [CityController::class, 'index']);
Route::get('cities/{city}', [CityController::class, 'show']);
Route::get('zip-codes', [ZipCodeController::class, 'index']);
Route::get('zip-codes/{zip_code}', [ZipCodeController::class, 'show']);
Route::get('companies', [CompanyController::class, 'index']);
Route::get('companies/{company}', [CompanyController::class, 'show']);
Route::get('job-categories', [JobCategoryController::class, 'index']);
Route::get('job-types', [JobTypeController::class, 'index']);
Route::get('skill-categories', [SkillCategoryController::class, 'index']);
Route::get('skill-categories/{skill_category}', [SkillCategoryController::class, 'show']);
Route::get('skills', [SkillController::class, 'index']);
Route::get('skills/{skill}', [SkillController::class, 'show']);
Route::get('jobs/{job}', [JobController::class, 'show']);
Route::get('jobs/{user?}', [JobController::class, 'index']);
Route::get('education-levels', [EducationLevelController::class, 'index']);
Route::get('education-levels/{education_level}', [EducationLevelController::class, 'show']);
Route::get('languages', [LanguageController::class, 'index']);
Route::get('languages/{language}', [LanguageController::class, 'show']);
Route::get('link-storage', [CommandController::class, 'linkStorage']);

// Rutas comunes a todos los usuarios, protegidas solo con autenticación.
Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:sanctum'],
], function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::delete('logout', [AuthController::class, 'logOut']);
    Route::put('update-password', [AuthController::class, 'updatePassword']);

    Route::get('subscriptions', [SubscriptionController::class, 'getSubscriptions']);
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'getSubscription']);

    // Rutas para gestionar categorías de habilidades
    Route::post('skill-categories', [SkillCategoryController::class, 'store']);
    Route::put('skill-categories/{skill_category}', [SkillCategoryController::class, 'update']);
    Route::delete('skill-categories/{skill_category}', [SkillCategoryController::class, 'destroy']);

    // Rutas para gestionar habilidades
    Route::post('skills', [SkillController::class, 'store']);
    Route::put('skills/{skill}', [SkillController::class, 'update']);
    Route::delete('skills/{skill}', [SkillController::class, 'destroy']);

    // Rutas para gestionar candidatos
    Route::get('candidates', [CandidateController::class, 'index']);
    Route::post('candidates', [CandidateController::class, 'store']);

    Route::put('candidates/{candidate}', [CandidateController::class, 'update']);
    Route::delete('candidates/{candidate}', [CandidateController::class, 'destroy']);
    Route::get('candidates/applications', [CandidateController::class, 'getAllApplications']);
    Route::get('jobs/unapplied', [CandidateController::class, 'getUnappliedJobs']);
    Route::get('candidates/{candidate}', [CandidateController::class, 'show']);

    // Rutas para gestionar compañías

    // Obtener los candidatos que han aplicado a una oferta laboral publicada por la compañía.
    Route::get('/companies/applicants', [CompanyController::class, 'getCompanyJobApplications']);
    Route::post('companies', [CompanyController::class, 'store']);
    Route::put('companies/{company}', [CompanyController::class, 'update']);
    Route::delete('companies/{company}', [CompanyController::class, 'destroy']);

    // Rutas para crear, actualizar y eliminar categorías de ofertas de trabajo
    Route::get('job-categories/{job_category}', [JobCategoryController::class, 'show']);
    Route::post('job-categories', [JobCategoryController::class, 'store']);
    Route::put('job-categories/{job_category}', [JobCategoryController::class, 'update']);
    Route::delete('job-categories/{job_category}', [JobCategoryController::class, 'destroy']);

    // Rutas para crear, actualizar y eliminar tipos de ofertas de trabajo
    Route::get('job-types/{job_type}', [JobTypeController::class, 'show']);
    Route::post('job-types', [JobTypeController::class, 'store']);
    Route::put('job-types/{job_type}', [JobTypeController::class, 'update']);
    Route::delete('job-types/{job_type}', [JobTypeController::class, 'destroy']);

    // Rutas para crear, actualizar y eliminar tipos de ofertas de trabajo
    Route::post('subscription-plans', [SubscriptionPlanController::class, 'store']);
    Route::put('subscription-plans/{subscription_plan}', [SubscriptionPlanController::class, 'update']);
    Route::delete('subscription-plans/{subscription_plan}', [SubscriptionPlanController::class, 'destroy']);

    Route::apiResource('users', UserController::class);

    // Rutas para crear, actualizar y eliminar ofertas de trabajo

    /*Route::get('jobs/{user?}', [JobController::class, 'index']);*/
    Route::post('jobs', [JobController::class, 'store']);
    Route::put('jobs/{job}', [JobController::class, 'update']);
    Route::delete('jobs/{job}', [JobController::class, 'destroy']);


    // Rutas para crear, actualizar y eliminar aplicaciones de trabajo
    Route::get('applications', [ApplicationController::class, 'index']);
    Route::get('applications/{application}', [ApplicationController::class, 'show']);
    Route::post('applications', [ApplicationController::class, 'store']);
    Route::put('applications/{application}', [ApplicationController::class, 'update']);
    Route::put('/applications/{application}/status', [ApplicationController::class, 'updateStatus']);
    Route::delete('applications/{application}', [ApplicationController::class, 'destroy']);

    // Rutas para suscripciones
    Route::post('subscriptions', [SubscriptionController::class, 'subscribe']);
    Route::put('subscriptions/{subscription}', [SubscriptionController::class, 'updateSubscription']);
    Route::delete('subscriptions/{subscription}', [SubscriptionController::class, 'cancelSubscription']);

    // Rutas para niveles de educación
    Route::post('education-levels', [EducationLevelController::class, 'store']);
    Route::put('education-levels/{education_level}', [EducationLevelController::class, 'update']);
    Route::delete('education-levels/{education_level}', [EducationLevelController::class, 'destroy']);

    // Rutas para Idiomas
    Route::post('languages', [LanguageController::class, 'store']);
    Route::put('languages/{language}', [LanguageController::class, 'update']);
    Route::delete('languages/{language}', [LanguageController::class, 'destroy']);

    // Rutas para gestionar experiencias laborales
    Route::get('work-experiences', [WorkExperienceController::class, 'index']);
    Route::post('work-experiences', [WorkExperienceController::class, 'store']);
    Route::get('work-experiences/{work_experience}', [WorkExperienceController::class, 'show']);
    Route::put('work-experiences/{work_experience}', [WorkExperienceController::class, 'update']);
    Route::delete('work-experiences/{work_experience}', [WorkExperienceController::class, 'destroy']);

    // Rutas para países

    Route::post('countries', [CountryController::class, 'store']);
    Route::put('countries/{country}', [CountryController::class, 'update']);
    Route::delete('countries/{country}', [CountryController::class, 'destroy']);

    // Rutas para estados
    Route::post('states', [StateController::class, 'store']);
    Route::put('states/{state}', [StateController::class, 'update']);
    Route::delete('states/{state}', [StateController::class, 'destroy']);
    // Rutas para gestionar estados según el país
    Route::get('countries/{country}/states', [StateController::class, 'getStatesByCountry']);

    // Rutas para ciudades
    Route::post('cities', [CityController::class, 'store']);
    Route::put('cities/{city}', [CityController::class, 'update']);
    Route::delete('cities/{city}', [CityController::class, 'destroy']);
    // Rutas para gestionar ciudades según el estado
    Route::get('states/{state}/cities', [CityController::class, 'getCitiesByState']);

    // Rutas para códigos postales
    Route::post('zip-codes', [ZipCodeController::class, 'store']);
    Route::put('zip-codes/{zip_code}', [ZipCodeController::class, 'update']);
    Route::delete('zip-codes/{zip_code}', [ZipCodeController::class, 'destroy']);
}
);
