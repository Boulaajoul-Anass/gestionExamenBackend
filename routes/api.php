<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReponseController;
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('filieres',FiliereController::class);
Route::apiResource('etudiants',EtudiantController::class);
Route::apiResource('departements',DepartementController::class);
Route::apiResource('examens',ExamenController::class);
Route::apiResource('matieres',MatiereController::class);
Route::apiResource('professeurs',ProfesseurController::class);
Route::apiResource('propositions',PropositionController::class);
Route::apiResource('questions',QuestionController::class);
Route::apiResource('reponses',ReponseController::class);
Route::apiResource('notes',NoteController::class);

//Sanctum
Route::post('/register',[\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout',[\App\Http\Controllers\AuthController::class, 'logout']);
Route::post('/login',[\App\Http\Controllers\AuthController::class, 'login']);


//Requete de Correction
Route::get('etudiant/{etudiant_id}/examen/{examen_id}/resultats', [\App\Http\Controllers\ResultatController::class, 'show']);

//Les exmens des matières de la filière de l'etudiant connécté
//Route::get('/etudiants/{id}/examens', 'App\Http\Controllers\EtudiantController@getExamsByFiliere');

//Les exmens déjà passé des matières de la filière de l'etudiant connécté
Route::get('/etudiants/{id}/examens-passes', 'App\Http\Controllers\EtudiantController@getExamsPassedByEtudiant');


// Departement
/*
Route::get('/departements',[App\Http\Controllers\DepartementController::class, 'index']);
Route::post('/departements/save',[App\Http\Controllers\DepartementController::class, 'store']);
Route::put('/departements/update/{id}',[App\Http\Controllers\DepartementController::class, 'update']);
Route::delete('/departements/delete/{id}',[App\Http\Controllers\DepartementController::class, 'destroy']);*/

// Filieres
/*
Route::get('/filieres',[App\Http\Controllers\FiliereController::class, 'index']);
Route::post('/filieres/save',[App\Http\Controllers\FiliereController::class, 'store']);
Route::put('/filieres/update/{id}',[App\Http\Controllers\FiliereController::class, 'update']);
Route::delete('/filieres/delete/{id}',[App\Http\Controllers\FiliereController::class, 'destroy']);*/

// Etudiant

Route::get('/etudiants',[App\Http\Controllers\EtudiantController::class, 'index']);
Route::post('/etudiants/save',[App\Http\Controllers\EtudiantController::class, 'store']);
Route::put('/etudiants/update/{id}',[App\Http\Controllers\EtudiantController::class, 'update']);
Route::delete('/etudiants/delete/{id}',[App\Http\Controllers\EtudiantController::class, 'destroy']);

// Examen
/*
Route::get('/examens',[App\Http\Controllers\ExamenController::class, 'index']);
Route::post('/examens/save',[App\Http\Controllers\ExamenController::class, 'store']);
Route::put('/examens/update/{id}',[App\Http\Controllers\ExamenController::class, 'update']);
Route::delete('/examens/delete/{id}',[App\Http\Controllers\ExamenController::class, 'destroy']);

// Matiere
Route::get('/matieres',[App\Http\Controllers\MatiereController::class, 'index']);
Route::post('/matieres/save',[App\Http\Controllers\MatiereController::class, 'store']);
Route::put('/matieres/update/{id}',[App\Http\Controllers\MatiereController::class, 'update']);
Route::delete('/matieres/delete/{id}',[App\Http\Controllers\MatiereController::class, 'destroy']);

// Note
Route::get('/notes',[App\Http\Controllers\NoteController::class, 'index']);
Route::post('/notes/save',[App\Http\Controllers\NoteController::class, 'store']);
Route::put('/notes/update/{id}',[App\Http\Controllers\NoteController::class, 'update']);
Route::delete('/notes/delete/{id}',[App\Http\Controllers\NoteController::class, 'destroy']);

// Professeur
Route::get('/professeurs',[App\Http\Controllers\ProfesseurController::class, 'index']);
Route::post('/professeurs/save',[App\Http\Controllers\ProfesseurController::class, 'store']);
Route::put('/professeurs/update/{id}',[App\Http\Controllers\ProfesseurController::class, 'update']);
Route::delete('/professeurs/delete/{id}',[App\Http\Controllers\ProfesseurController::class, 'destroy']);

// Proposition
Route::get('/propositions',[App\Http\Controllers\PropositionController::class, 'index']);
Route::post('/propositions/save',[App\Http\Controllers\PropositionController::class, 'store']);
Route::put('/propositions/update/{id}',[App\Http\Controllers\PropositionController::class, 'update']);
Route::delete('/propositions/delete/{id}',[App\Http\Controllers\PropositionController::class, 'destroy']);

// Question
Route::get('/questions',[App\Http\Controllers\QuestionController::class, 'index']);
Route::post('/questions/save',[App\Http\Controllers\QuestionController::class, 'store']);
Route::put('/questions/update/{id}',[App\Http\Controllers\QuestionController::class, 'update']);
Route::delete('/questions/delete/{id}',[App\Http\Controllers\QuestionController::class, 'destroy']);

// Reponse
Route::get('/reponses',[App\Http\Controllers\ReponseController::class, 'index']);
Route::post('/reponses/save',[App\Http\Controllers\ReponseController::class, 'store']);
Route::put('/reponses/update/{id}',[App\Http\Controllers\ReponseController::class, 'update']);
Route::delete('/reponses/delete/{id}',[App\Http\Controllers\ReponseController::class, 'destroy']);*/
