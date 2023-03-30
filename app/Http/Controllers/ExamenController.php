<?php

namespace App\Http\Controllers;

use App\Models\Examen;
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

    /*
     *  Recuperer les examens associé à les matières de la filière de l'étudiant connecté
     */
    public function getExamsByFiliere()
    {
        $etudiant = auth()->user();
        $examens = Examen::whereHas('matiere', function($query) use($etudiant) {
            $query->where('filiere_id', $etudiant->filiere_id);
        })->get();
        return response()->json(['examens' => $examens], 200);
    }

}
