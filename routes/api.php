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
use App\Http\Controllers\ResultatController ;
use App\Http\Controllers\AuthController;
use App\Models\Mark;

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

//Les exmens des matières de la filière de l'etudiant connécté
Route::get('/etudiants/{id}/examens', 'App\Http\Controllers\EtudiantController@getExamsByFiliere');

Route::get('/etudiant/{etudiant_id}/examensOfEtudiant', function ($etudiant_id){
    $examens = DB::table('examens')
            ->join('matieres', 'examens.matiere_id', '=', 'matieres.id')
            ->leftJoin('questions', 'examens.id', '=', 'questions.examen_id')
            ->leftJoin('propositions', 'questions.id', '=', 'propositions.question_id')
            ->leftJoin('reponses', function ($join) {
                $join->on('reponses.proposition_id', '=', 'propositions.id')
                     ->where('reponses.etudiant_id', '=', 4);
            })
            ->select('examens.id', 'examens.date', 'examens.heure', 'examens.duree', 'matieres.nom as matiere', DB::raw('SUM(IF(propositions.est_correcte = 1, 1, 0)) as nb_reponses_correctes'))
            ->groupBy('examens.id', 'examens.date', 'examens.heure', 'examens.duree', 'matieres.nom')
            ->get();


    return response()->json($examens);
});




//Les exmens déjà passé des matières de la filière de l'etudiant connécté
Route::get('/etudiants/{etudiant_id}/examens/{examen_id}/note', function ($etudiant_id, $examen_id) {
    $marks = DB::table('marks')
        ->join('examens', 'marks.examen_id', '=', 'examens.id')
        ->join('matieres', 'examens.matiere_id', '=', 'matieres.id')
        ->select('marks.valeur', 'matieres.nom as matiere')
        ->where('marks.etudiant_id', $etudiant_id)
        ->where('marks.examen_id', $examen_id)
        ->first();

    return response()->json([
        'marks' => $marks->valeur,
        'matiere' => $marks->matiere
    ]);
});




// Departement

Route::get('/departements',[App\Http\Controllers\DepartementController::class, 'index']);
Route::post('/departements/save',[App\Http\Controllers\DepartementController::class, 'store']);
Route::put('/departements/update/{id}',[App\Http\Controllers\DepartementController::class, 'update']);
Route::delete('/departements/delete/{id}',[App\Http\Controllers\DepartementController::class, 'destroy']);

// Filieres

Route::get('/filieres',[App\Http\Controllers\FiliereController::class, 'index']);
Route::post('/filieres/save',[App\Http\Controllers\FiliereController::class, 'store']);
Route::put('/filieres/update/{id}',[App\Http\Controllers\FiliereController::class, 'update']);
Route::delete('/filieres/delete/{id}',[App\Http\Controllers\FiliereController::class, 'destroy']);

// Etudiant

Route::get('/etudiants',[App\Http\Controllers\EtudiantController::class, 'index']);
Route::post('/etudiants/save',[App\Http\Controllers\EtudiantController::class, 'store']);
Route::put('/etudiants/update/{id}',[App\Http\Controllers\EtudiantController::class, 'update']);
Route::delete('/etudiants/delete/{id}',[App\Http\Controllers\EtudiantController::class, 'destroy']);

// Examen
Route::post('/marks/save', function (Request $request) {
    $mark = new Mark;
    $mark->etudiant_id = $request->input('etudiant_id');
    $mark->examen_id = $request->input('examen_id');
    $mark->valeur = $request->input('valeur');
    $mark->save();

    return response()->json([
        'message' => 'Note créée avec succès',
        'mark' => $mark
    ]);
});



//Requete de Correction
Route::get('etudiant/{etudiant_id}/examen/{examen_id}/resultats', [\App\Http\Controllers\ResultatController::class, 'show']);

//Les exmens des matières de la filière de l'etudiant connécté
Route::get('/etudiants/{id}/examens', 'App\Http\Controllers\EtudiantController@getExamsByFiliere');

Route::get('/examens/{filiere_id}',[App\Http\Controllers\ExamenController::class, 'show']);

Route::get('/etudiants/{id}/examens-passes', 'App\Http\Controllers\EtudiantController@getExamsPassedByEtudiant');
Route::get('/etudiants/{id}/deux-examens-passes', 'App\Http\Controllers\EtudiantController@getTwoExamsPassedByEtudiant');


// Les examens d'une filliere donnée
Route::get('/exams/{filiere_id}', function ($filiere_id) {
    $exams = DB::table('examens')
        ->join('matieres', 'examens.matiere_id', '=', 'matieres.id')
        ->join('professeurs', 'matieres.professeur_id', '=', 'professeurs.id')
        ->join('filieres', 'matieres.filiere_id', '=', 'filieres.id')
        ->where('filieres.id', '=', $filiere_id)
        ->select('examens.*', 'matieres.nom as matiere_nom', 'professeurs.nom as professeur_nom', 'filieres.nom as filiere_nom', 'matieres.filiere_id')
        ->distinct()
        ->get();

    return response()->json($exams);
});

use Illuminate\Support\Facades\DB;


Route::get('/deux-upcoming-exams/{filiere_id}', function ($filiere_id) {
    $today = date('Y-m-d');
    $upcomingExams = DB::table('examens')
        ->join('matieres', 'examens.matiere_id', '=', 'matieres.id')
        ->join('professeurs', 'matieres.professeur_id', '=', 'professeurs.id')
        ->join('filieres', 'matieres.filiere_id', '=', 'filieres.id')
        ->where('filieres.id', '=', $filiere_id)
        ->where('date', '>', $today)
        ->orderBy('heure','asc')
        ->orderBy('date','asc')
        ->limit(2)
        ->select('examens.*', 'matieres.nom as matiere_nom', 'professeurs.nom as professeur_nom', 'filieres.nom as filiere_nom')
        ->distinct()
        ->get();

    return response()->json($upcomingExams);
});

Route::get('/upcoming-exams/{filiere_id}', function ($filiere_id) {
    $today = date('Y-m-d');
    $upcomingExams = DB::table('examens')
        ->join('matieres', 'examens.matiere_id', '=', 'matieres.id')
        ->join('professeurs', 'matieres.professeur_id', '=', 'professeurs.id')
        ->join('filieres', 'matieres.filiere_id', '=', 'filieres.id')
        ->where('filieres.id', '=', $filiere_id)
        ->where('date', '>', $today)
        ->orderBy('date','asc')
        ->orderBy('heure','asc')
        ->select('examens.*', 'matieres.nom as matiere_nom', 'professeurs.nom as professeur_nom','professeurs.prenom as professeur_prenom', 'filieres.nom as filiere_nom')
        ->distinct()
        ->get();

    return response()->json($upcomingExams);
});


Route::get('/today-exams-filiere/{filiere_id}', function ($filiere_id) {
    $today = date('Y-m-d');
    $currentTime = date('H:i:s', strtotime('-5 minute'));
    $todayexams = DB::table('examens')
        ->join('matieres', 'examens.matiere_id', '=', 'matieres.id')
        ->join('professeurs', 'matieres.professeur_id', '=', 'professeurs.id')
        ->join('filieres', 'matieres.filiere_id', '=', 'filieres.id')
        ->join('questions', 'examens.id', '=', 'questions.examen_id')
        ->where('filieres.id', '=', $filiere_id)
        ->where('date', '=', $today)
        ->where('heure', '>', DB::raw("TIME('$currentTime')"))
        ->select('examens.*', 'matieres.nom as matiere_nom', 'professeurs.nom as professeur_nom', 'filieres.nom as filiere_nom')
        ->distinct()
        ->get();

    return response()->json($todayexams);
});


use App\Models\Etudiant;

// Route::get('/etudiants/{id_etudiant}/examens/{id_examen}', function (Request $request, $id_etudiant, $id_examen) {

//     $etudiant = Etudiant::findOrFail($id_etudiant);
//     $examen = $etudiant->examens()->findOrFail($id_examen);

//     $data = [
//         'etudiant' => [
//             'id' => $etudiant->id,
//             'nom' => $etudiant->nom,
//             'prenom' => $etudiant->prenom,
//         ],
//         'examen' => [
//             'id' => $examen->id,
//             'date' => $examen->date,
//             'heure' => $examen->heure,
//             'duree' => $examen->duree,
//         ],
//         'questions' => [],
//     ];

//     foreach ($examen->questions as $question) {
//         $reponse = $etudiant->reponses()->where('question_id', $question->id)->first();
//         $proposition_correcte = $question->propositions()->where('est_correcte', true)->first();

//         $data['questions'][] = [
//             'id' => $question->id,
//             'libelle' => $question->libelle,
//             'proposition_etudiant' => $reponse ? $reponse->proposition->libelle : null,
//             'proposition_correcte' => $proposition_correcte ? $proposition_correcte->libelle : null,
//         ];
//     }

//     return response()->json($data);
// });

use App\Models\Examen;
use App\Models\Question;
use App\Models\Reponse;

Route::get('/etudiants/{etudiant_id}/examens/{examen_id}', [ExamenController::class, 'examenEtudiant']);



Route::get('/etudiant/{etudiant_id}/exam/{exam_id}/correctes-reponses', function ($etudiant_id, $exam_id) {
    $correctReponsesCount = DB::table('reponses')
        ->join('propositions', 'reponses.proposition_id', '=', 'propositions.id')
        ->join('questions', 'propositions.question_id', '=', 'questions.id')
        ->join('examens', 'questions.examen_id', '=', 'examens.id')
        ->where('reponses.etudiant_id', '=', $etudiant_id)
        ->where('examens.id', '=', $exam_id)
        ->where('propositions.est_correcte', '=', 1)
        ->count();
    return response()->json(['correct_reponses_count' => $correctReponsesCount], 200);
});

Route::get('/etudiant/{etudiant_id}/exam/{exam_id}/fausses-reponses', function ($etudiant_id, $exam_id) {
    $correctReponsesCount = DB::table('reponses')
        ->join('propositions', 'reponses.proposition_id', '=', 'propositions.id')
        ->join('questions', 'propositions.question_id', '=', 'questions.id')
        ->join('examens', 'questions.examen_id', '=', 'examens.id')
        ->where('reponses.etudiant_id', '=', $etudiant_id)
        ->where('examens.id', '=', $exam_id)
        ->where('propositions.est_correcte', '=', 0)
        ->count();
    return response()->json(['correct_reponses_count' => $correctReponsesCount], 200);
});

Route::get('/examens/{exam_id}/questions-count', function ($exam_id) {
    $questionsCount = DB::table('questions')
        ->where('examen_id', $exam_id)
        ->count();

    return response()->json(['questions_count' => $questionsCount], 200);
});







Route::get('/exams/past', [ExamController::class, 'pastExams']);






Route::get('/examens',[App\Http\Controllers\ExamenController::class, 'index']);
Route::post('/examens/save',[App\Http\Controllers\ExamenController::class, 'store']);
Route::put('/examens/update/{id}',[App\Http\Controllers\ExamenController::class, 'update']);
Route::delete('/examens/delete/{id}',[App\Http\Controllers\ExamenController::class, 'destroy']);

// Matiere
Route::get('/matieres',[App\Http\Controllers\MatiereController::class, 'index']);
Route::post('/matieres/save',[App\Http\Controllers\MatiereController::class, 'store']);
Route::put('/matieres/update/{id}',[App\Http\Controllers\MatiereController::class, 'update']);
Route::delete('/matieres/delete/{id}',[App\Http\Controllers\MatiereController::class, 'destroy']);


Route::get('/examens/{examen_id}/questions/{etudiant_id}', 'App\Http\Controllers\ExamenController@getQuestionsWithAnswersAndPropositions');



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
Route::delete('/reponses/delete/{id}',[App\Http\Controllers\ReponseController::class, 'destroy']);
