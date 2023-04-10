<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Question;
use App\Models\Proposition;
use App\Models\Reponse;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;

class ExamenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $examens = Examen::all();
        return response()->json($examens);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $examens = new Examen([
            'date' => $request->input('date'),
            'duree' => $request->input('duree'),
            'idprofesseur' => $request->input('idprofesseur'),
            'idmatiere' => $request->input('idmatiere'),
        ]);
        $examens->save();
        return response()->json('');
    }

    public function getQuestionsWithAnswersAndPropositions($examen_id, $etudiant_id)
{
    $questions = Question::where('examen_id', $examen_id)->get();
    $answers = Reponse::where('etudiant_id', $etudiant_id)->whereIn('question_id', $questions->pluck('id'))->get();
    $propositions = Proposition::whereIn('question_id', $questions->pluck('id'))->where('est_correcte', true)->get();

    $result = [];

    foreach ($questions as $question) {
        $question_answers = $answers->where('question_id', $question->id);
        $question_propositions = $propositions->where('question_id', $question->id);

        $result[] = [
            'question' => $question,
            'answers' => $question_answers,
            'propositions' => $question_propositions,
        ];
    }

    return response()->json($result);
}

public function examenEtudiant($etudiant_id, $examen_id) {
    $examen = DB::table('examens')->where('id', $examen_id)->first();
    $etudiant = DB::table('etudiants')->where('id', $etudiant_id)->first();
    $questions = DB::table('questions')
                    ->select('questions.id', 'questions.libelle as question', 'propositions.libelle as proposition', 'propositions.est_correcte')
                    ->join('propositions', 'questions.id', '=', 'propositions.question_id')
                    ->where('questions.examen_id', $examen_id)
                    ->get();
    $reponses = DB::table('reponses')
                    ->select('reponses.question_id', 'propositions.libelle')
                    ->join('propositions', 'reponses.proposition_id', '=', 'propositions.id')
                    ->where('reponses.etudiant_id', $etudiant_id)
                    ->get();
    return response()->json([
        'etudiant' => $etudiant,
        'examen' => $examen,
        'questions' => $questions,
        'reponses' => $reponses
    ]);
}




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $exam = Examen::with(['matiere', 'questions.propositions'])->findOrFail($id);
    
        return response()->json($exam);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $examens = Examen::find($id);
        $examens->update($request->all());
        return response()->json('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $examens = Examen::find($id);
        $examens->delete();
        return response()->json('');
    }
}