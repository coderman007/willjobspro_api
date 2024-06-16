<?php

use App\Http\Controllers\Api\ApiApplicationController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiBenefitController;
use App\Http\Controllers\Api\ApiCandidateController;
use App\Http\Controllers\Api\ApiCityController;
use App\Http\Controllers\Api\ApiCompanyController;
use App\Http\Controllers\Api\ApiCountryController;
use App\Http\Controllers\Api\ApiEducationLevelController;
use App\Http\Controllers\Api\ApiJobCategoryController;
use App\Http\Controllers\Api\ApiJobController;
use App\Http\Controllers\Api\ApiJobTypeController;
use App\Http\Controllers\Api\ApiLanguageController;
use App\Http\Controllers\Api\ApiSkillCategoryController;
use App\Http\Controllers\Api\ApiSkillController;
use App\Http\Controllers\Api\ApiStateController;
use App\Http\Controllers\Api\ApiSubscriptionController;
use App\Http\Controllers\Api\ApiSubscriptionPlanController;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\ApiWorkExperienceController;
use App\Http\Controllers\Api\ApiZipCodeController;
use App\Http\Controllers\CommandController;
use Illuminate\Support\Facades\Route;


// Rutas abiertas
Route::post("register", [ApiAuthController::class, 'register']);
Route::post("login", [ApiAuthController::class, 'login']);
Route::get('countries', [ApiCountryController::class, 'index']);
Route::get('users', [ApiUserController::class, 'index']);
Route::get('countries/{country}', [ApiCountryController::class, 'show']);
Route::get('states', [ApiStateController::class, 'index']);
Route::get('states/{state}', [ApiStateController::class, 'show']);
Route::get('cities', [ApiCityController::class, 'index']);
Route::get('cities/{city}', [ApiCityController::class, 'show']);
Route::get('zip-codes', [ApiZipCodeController::class, 'index']);
Route::get('zip-codes/{zip_code}', [ApiZipCodeController::class, 'show']);
Route::get('companies', [ApiCompanyController::class, 'index']);
Route::get('companies/{company}', [ApiCompanyController::class, 'show']);
Route::get('job-categories', [ApiJobCategoryController::class, 'index']);
Route::get('job-types', [ApiJobTypeController::class, 'index']);
Route::get('skill-categories', [ApiSkillCategoryController::class, 'index']);
Route::get('skill-categories/{skill_category}', [ApiSkillCategoryController::class, 'show']);
Route::get('skills', [ApiSkillController::class, 'index']);
Route::get('skills/{skill}', [ApiSkillController::class, 'show']);
Route::get('jobs/{job}', [ApiJobController::class, 'show']);
Route::get('jobs/{user?}', [ApiJobController::class, 'index']);
Route::get('/job-type-counts', [ApiJobController::class, 'getJobTypeCounts']);
Route::get('/education-level-counts', [ApiJobController::class, 'getEducationLevelCounts']);
Route::get('education-levels', [ApiEducationLevelController::class, 'index']);
Route::get('education-levels/{education_level}', [ApiEducationLevelController::class, 'show']);
Route::get('languages', [ApiLanguageController::class, 'index']);
Route::get('languages/{language}', [ApiLanguageController::class, 'show']);
Route::get('benefits', [ApiBenefitController::class, 'index']);
Route::get('benefits/{benefit}', [ApiBenefitController::class, 'show']);
Route::get('link-storage', [CommandController::class, 'linkStorage']);

// Rutas comunes a todos los usuarios, protegidas solo con autenticación.
Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:sanctum'],
], function () {
    Route::get('profile', [ApiAuthController::class, 'profile']);
    Route::delete('logout', [ApiAuthController::class, 'logOut']);
    Route::put('update-password', [ApiAuthController::class, 'updatePassword']);

    Route::get('subscriptions', [ApiSubscriptionController::class, 'getSubscriptions']);
    Route::get('subscriptions/{subscription}', [ApiSubscriptionController::class, 'getSubscription']);

    // Rutas para gestionar categorías de habilidades
    Route::post('skill-categories', [ApiSkillCategoryController::class, 'store']);
    Route::put('skill-categories/{skill_category}', [ApiSkillCategoryController::class, 'update']);
    Route::delete('skill-categories/{skill_category}', [ApiSkillCategoryController::class, 'destroy']);

    // Rutas para gestionar habilidades
    Route::post('skills', [ApiSkillController::class, 'store']);
    Route::put('skills/{skill}', [ApiSkillController::class, 'update']);
    Route::delete('skills/{skill}', [ApiSkillController::class, 'destroy']);

    // Rutas para gestionar candidatos
    Route::get('candidates', [ApiCandidateController::class, 'index']);
    Route::post('candidates', [ApiCandidateController::class, 'store']);

    Route::put('candidates/{candidate}', [ApiCandidateController::class, 'update']);
    Route::delete('candidates/{candidate}', [ApiCandidateController::class, 'destroy']);
    Route::get('candidates/applications', [ApiCandidateController::class, 'getAllApplications']);
    Route::get('jobs/unapplied', [ApiCandidateController::class, 'getUnappliedJobs']);
    Route::get('candidates/{candidate}', [ApiCandidateController::class, 'show']);

    // Rutas para gestionar compañías

    // Obtener los candidatos que han aplicado a una oferta laboral publicada por la compañía.
    Route::get('/companies/applicants', [ApiCompanyController::class, 'getCompanyJobApplications']);
    Route::post('companies', [ApiCompanyController::class, 'store']);
    Route::put('companies/{company}', [ApiCompanyController::class, 'update']);
    Route::delete('companies/{company}', [ApiCompanyController::class, 'destroy']);

    // Rutas para crear, actualizar y eliminar categorías de ofertas de trabajo
    Route::get('job-categories/{job_category}', [ApiJobCategoryController::class, 'show']);
    Route::post('job-categories', [ApiJobCategoryController::class, 'store']);
    Route::put('job-categories/{job_category}', [ApiJobCategoryController::class, 'update']);
    Route::delete('job-categories/{job_category}', [ApiJobCategoryController::class, 'destroy']);

    // Rutas para crear, actualizar y eliminar tipos de ofertas de trabajo
    Route::get('job-types/{job_type}', [ApiJobTypeController::class, 'show']);
    Route::post('job-types', [ApiJobTypeController::class, 'store']);
    Route::put('job-types/{job_type}', [ApiJobTypeController::class, 'update']);
    Route::delete('job-types/{job_type}', [ApiJobTypeController::class, 'destroy']);

    // Rutas para crear, actualizar y eliminar tipos de ofertas de trabajo
    Route::post('subscription-plans', [ApiSubscriptionPlanController::class, 'store']);
    Route::put('subscription-plans/{subscription_plan}', [ApiSubscriptionPlanController::class, 'update']);
    Route::delete('subscription-plans/{subscription_plan}', [ApiSubscriptionPlanController::class, 'destroy']);

    Route::apiResource('users', ApiUserController::class);

    // Rutas para crear, actualizar y eliminar ofertas de trabajo

    /*Route::get('jobs/{user?}', [ApiJobController::class, 'index']);*/
    Route::post('jobs', [ApiJobController::class, 'store']);
    Route::put('jobs/{job}', [ApiJobController::class, 'update']);
    Route::delete('jobs/{job}', [ApiJobController::class, 'destroy']);


    // Rutas para crear, actualizar y eliminar aplicaciones de trabajo
    Route::get('applications', [ApiApplicationController::class, 'index']);
    Route::get('applications/{application}', [ApiApplicationController::class, 'show']);
    Route::post('applications', [ApiApplicationController::class, 'store']);
    Route::put('applications/{application}', [ApiApplicationController::class, 'update']);
    Route::put('/applications/{application}/status', [ApiApplicationController::class, 'updateStatus']);
    Route::delete('applications/{application}', [ApiApplicationController::class, 'destroy']);

    // Rutas para suscripciones
    Route::post('subscriptions', [ApiSubscriptionController::class, 'subscribe']);
    Route::put('subscriptions/{subscription}', [ApiSubscriptionController::class, 'updateSubscription']);
    Route::delete('subscriptions/{subscription}', [ApiSubscriptionController::class, 'cancelSubscription']);

    // Rutas para niveles de educación
    Route::post('education-levels', [ApiEducationLevelController::class, 'store']);
    Route::put('education-levels/{education_level}', [ApiEducationLevelController::class, 'update']);
    Route::delete('education-levels/{education_level}', [ApiEducationLevelController::class, 'destroy']);

    // Rutas para Idiomas
    Route::post('languages', [ApiLanguageController::class, 'store']);
    Route::put('languages/{language}', [ApiLanguageController::class, 'update']);
    Route::delete('languages/{language}', [ApiLanguageController::class, 'destroy']);

    // Rutas para Beneficios
    Route::post('benefits', [ApiBenefitController::class, 'store']);
    Route::put('benefits/{benefit}', [ApiBenefitController::class, 'update']);
    Route::delete('benefits/{benefit}', [ApiBenefitController::class, 'destroy']);

    // Rutas para gestionar experiencias laborales
    Route::get('work-experiences', [ApiWorkExperienceController::class, 'index']);
    Route::post('work-experiences', [ApiWorkExperienceController::class, 'store']);
    Route::get('work-experiences/{work_experience}', [ApiWorkExperienceController::class, 'show']);
    Route::put('work-experiences/{work_experience}', [ApiWorkExperienceController::class, 'update']);
    Route::delete('work-experiences/{work_experience}', [ApiWorkExperienceController::class, 'destroy']);

    // Rutas para países

    Route::post('countries', [ApiCountryController::class, 'store']);
    Route::put('countries/{country}', [ApiCountryController::class, 'update']);
    Route::delete('countries/{country}', [ApiCountryController::class, 'destroy']);

    // Rutas para estados
    Route::post('states', [ApiStateController::class, 'store']);
    Route::put('states/{state}', [ApiStateController::class, 'update']);
    Route::delete('states/{state}', [ApiStateController::class, 'destroy']);
    // Rutas para gestionar estados según el país
    Route::get('countries/{country}/states', [ApiStateController::class, 'getStatesByCountry']);

    // Rutas para ciudades
    Route::post('cities', [ApiCityController::class, 'store']);
    Route::put('cities/{city}', [ApiCityController::class, 'update']);
    Route::delete('cities/{city}', [ApiCityController::class, 'destroy']);
    // Rutas para gestionar ciudades según el estado
    Route::get('states/{state}/cities', [ApiCityController::class, 'getCitiesByState']);

    // Rutas para códigos postales
    Route::post('zip-codes', [ApiZipCodeController::class, 'store']);
    Route::put('zip-codes/{zip_code}', [ApiZipCodeController::class, 'update']);
    Route::delete('zip-codes/{zip_code}', [ApiZipCodeController::class, 'destroy']);
}
);
