<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserFormationController;
use App\Http\Controllers\UserExperienceController;
use App\Http\Controllers\UserMetaController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\Office365MailController;
use App\Http\Controllers\ResumeController;


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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgotPassword', [AuthController::class, 'forgotPassword']);
Route::post('updatePassword', [AuthController::class, 'updatePassword']);

Route::middleware('throttle:10000,1')->group(function () {
    // Vos routes ici
    // check profil
   Route::post('check-profil', [ProfilController::class, 'checkProfil']);
   //  sync profil
   Route::post('sync-profils', [ProfilController::class, 'syncProfils']);
   
   // add profil
   Route::post('add-profil', [ProfilController::class, 'addProfil']);
   
   // total profils
   Route::get('total-profils', [ProfilController::class, 'getProfilsCount']);
});


// check entreprise
Route::post('check-entreprise', [EntrepriseController::class, 'checkEntreprise']);
// sync entreprise
Route::post('sync-entreprise', [EntrepriseController::class, 'syncEntreprise']);

Route::group(["middleware" => 'auth:sanctum'],function() {

    // get all users
    Route::get('all-users', [UserController::class, 'getAllUsers']);

    // get all permissions
    Route::get('all-permissions', [UserController::class, 'getAllPermissions']);

    // get all etapes
    Route::get('all-etapes', [CandidatController::class, 'getAllEtapes']);

    
    Route::post('confirm-code', [AuthController::class, 'confirmCode']);
    Route::post('resend-code', [AuthController::class, 'resendCode']);
    // user auth
    Route::post('updateProfile', [AuthController::class, 'updateProfile']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('changePassword', [AuthController::class, 'changePassword']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refreshToken', [AuthController::class, 'refreshToken']);

    // user formations  
    Route::get('my-formations', [UserFormationController::class, 'getMyFormations']);
    Route::post('create-formation', [UserFormationController::class, 'createFormation']);
    Route::post('update-formation/{id}', [UserFormationController::class, 'updateFormation']);
    Route::delete('delete-formation/{id}', [UserFormationController::class, 'deleteFormation']);

    Route::get('my-certifications', [UserMetaController::class, 'getMyCertifications']);
    Route::get('my-expertises', [UserMetaController::class, 'getMyExpertises']);
    Route::get('my-interests', [UserMetaController::class, 'getMyInterests']);
    Route::get('my-skills', [UserMetaController::class, 'getMySkills']);
    Route::get('my-tskills', [UserMetaController::class, 'getMyTSkills']);
    Route::get('my-mskills', [UserMetaController::class, 'getMyMSkills']);

    // user metas
    Route::post('create-meta', [UserMetaController::class, 'createMeta']);
    Route::post('update-meta/{id}', [UserMetaController::class, 'updateMeta']);
    Route::delete('delete-meta/{id}', [UserMetaController::class, 'deleteMeta']);

     // user experiences  
     Route::get('my-experiences', [UserExperienceController::class, 'getMyExperiences']);
     Route::post('create-experience', [UserExperienceController::class, 'createExperience']);
     Route::post('update-experience/{id}', [UserExperienceController::class, 'updateExperience']);
     Route::delete('delete-experience/{id}', [UserExperienceController::class, 'deleteExperience']);


    // crud entreprises
    Route::get('entreprises', [EntrepriseController::class, 'index']);
    Route::post('entreprises', [EntrepriseController::class, 'store']);
    Route::post('update-entreprises/{id}', [EntrepriseController::class, 'update']);
    Route::delete('entreprises/{id}', [EntrepriseController::class, 'destroy']);
    Route::get('export-entreprises', [EntrepriseController::class, 'export']);
    Route::post('search-entreprises', [EntrepriseController::class, 'search']);

    // crud campaign
    Route::get('campaigns', [CampaignController::class, 'index']);
    Route::post('campaigns', [CampaignController::class, 'store']);
    Route::post('update-campaigns/{id}', [CampaignController::class, 'update']);
    Route::delete('campaigns/{id}', [CampaignController::class, 'destroy']);
    Route::post('search-campaigns', [CampaignController::class, 'search']);
    // lauch campaign
    Route::post('campaigns/{id}/launch', [CampaignController::class, 'launch']);

    // crud profils
    Route::get('profils', [ProfilController::class, 'index']);
    Route::post('profils', [ProfilController::class, 'store']);
    Route::post('update-profils/{id}', [ProfilController::class, 'update']);
    Route::delete('profils/{id}', [ProfilController::class, 'destroy']);
    Route::get('export-profils', [ProfilController::class, 'export']);
    Route::post('search-profils', [ProfilController::class, 'search']);

    // get profils by tags
    Route::get('profils-by-tag/{tag}', [ProfilController::class, 'getProfilsByTags']);

    // crud profil tags
    Route::get('profil-tags', [ProfilController::class, 'getTags']);
    Route::post('profil-tags', [ProfilController::class, 'createTag']);
    Route::post('update-profil-tags/{id}', [ProfilController::class, 'updateTag']);
    Route::delete('profil-tags/{id}', [ProfilController::class, 'deleteTag']);

    // crud clients
    Route::get('clients', [ClientController::class, 'index']);
    Route::post('clients', [ClientController::class, 'store']);
    Route::post('update-clients/{id}', [ClientController::class, 'update']);
    Route::delete('clients/{id}', [ClientController::class, 'destroy']);
    // Route::get('export-clients', [ClientController::class, 'export']);
    Route::post('search-clients', [ClientController::class, 'search']);

    // crud candidats
    Route::get('candidats', [CandidatController::class, 'index']);
    Route::get('candidats/{id}', [CandidatController::class, 'show']);
    Route::post('candidats', [CandidatController::class, 'store']);
    Route::post('update-candidats/{id}', [CandidatController::class, 'update']);
    Route::delete('candidats/{id}', [CandidatController::class, 'destroy']);
    Route::post('search-candidats', [CandidatController::class, 'search']);
    // get_candidats_by_offer
    Route::get('get-candidats-by-offer/{id}', [CandidatController::class, 'get_candidats_by_offer']);


    // crud projects
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('pending-projects', [ProjectController::class, 'pending']);
    Route::post('projects', [ProjectController::class, 'store']);
    Route::post('update-projects/{id}', [ProjectController::class, 'update']);
    Route::delete('projects/{id}', [ProjectController::class, 'destroy']);
    Route::post('search-projects', [ProjectController::class, 'search']);

    // crud timesheets
    Route::get('timesheets', [TimesheetController::class, 'index']);
    Route::get('all-timesheets', [TimesheetController::class, 'all']);
    Route::post('timesheets', [TimesheetController::class, 'store']);
    Route::post('update-timesheets/{id}', [TimesheetController::class, 'update']);
    Route::delete('timesheets/{id}', [TimesheetController::class, 'destroy']);
    // update timesheet entry
    Route::post('update-entry/{id}', [TimesheetController::class, 'updateEntry']);
    // update timesheet entry project
    Route::post('update-entry-project/{id}', [TimesheetController::class, 'updateEntryProject']);
    // reinit timesheet entry
    Route::post('reinit-timesheet/{id}', [TimesheetController::class, 'reinit']);
    // get timesheet by id
    Route::get('timesheet/{id}', [TimesheetController::class, 'getTimesheet']);
    // month and year
    Route::get('month-year', [TimesheetController::class, 'getMonthYear']);
    // timesheet-by-month
    Route::get('timesheet-by-month', [TimesheetController::class, 'getTimesheetByMonth']);
    Route::get('export-timesheet', [TimesheetController::class, 'export']);
    

    // crud offers
    Route::get('offers', [OfferController::class, 'index']);
    Route::post('offers', [OfferController::class, 'store']);
    Route::post('update-offers/{id}', [OfferController::class, 'update']);
    Route::delete('offers/{id}', [OfferController::class, 'destroy']);
    // Route::get('export-offers', [OfferController::class, 'export']);
    Route::post('search-offers', [OfferController::class, 'search']);
   

    // dashboard
    Route::get('stats/{period}', [DashboardController::class, 'stats']);
    // last 5 users
    Route::get('last-data', [DashboardController::class, 'last_data']);

    // crud users
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    // delete user
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::post('update-users/{id}', [UserController::class, 'update']);
    Route::post('search-users', [UserController::class, 'search']);


    // enrich - profil
    // Route::get('enrich-profils', [ProfilController::class, 'enrichProfils']);
    Route::get('enrich-profil/{id}', [ProfilController::class, 'enrichProfil']);
    Route::get('enrich-profil-cout/{id}', [ProfilController::class, 'enrichProfilWithContactOut']);

    Route::get('/microsoft-auth/redirect', [Office365MailController::class, 'redirectToProvider']);
    Route::post('/microsoft-auth/callback', [Office365MailController::class, 'handleProviderCallback']);
    Route::get('/mails', [Office365MailController::class, 'getMails']);
    Route::post('/send-mail', [Office365MailController::class, 'sendMail']);

    // resumes
    Route::get('resumes', [ResumeController::class, 'index']);
    Route::post('resumes', [ResumeController::class, 'store']);
    Route::get('resumes/{id}', [ResumeController::class, 'show']);
    Route::post('resumes/{id}', [ResumeController::class, 'update']);
    Route::delete('resumes/{id}', [ResumeController::class, 'destroy']);  
    Route::post('resumes/search', [ResumeController::class, 'search']);
    Route::get('resumes/candidat/{id}', [ResumeController::class, 'getResumeByCandidatId']);


});