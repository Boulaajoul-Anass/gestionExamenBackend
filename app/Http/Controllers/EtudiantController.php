<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;
use App\Http\Controllers\MatiereController;
use App\Models\Matiere;

class EtudiantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $etudiants = Etudiant::all();
        return response()->json($etudiants);
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
        $etudiants = new Etudiant([
            'nom' => $request->input('nom'),

        ]);
        $etudiants->save();
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
        $etudiants = Etudiant::find($id);
        return response()->json($etudiants);
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
        $etudiants = Etudiant::find($id);
        $etudiants->update($request->all());
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
        $etudiants = Etudiant::find($id);
        $etudiants->delete();
        return response()->json('');
    }

    /*
     * Les exmens des matières de l'etudiant connécté
     */
    public function getExamsByFiliere($id)
    {
        $etudiant = Etudiant::findOrFail($id);
        $filiere_id = $etudiant->filiere_id;

        $matieres = Matiere::where('filiere_id', $filiere_id)->get();
        $examens = array();

        foreach ($matieres as $matiere) {
            $examens[$matiere->nom] = $matiere->examens;
        }

        return response()->json(['examens' => $examens], 200);
    }
    public function getExamsPassedByEtudiant($id)
    {
        $etudiant = Etudiant::findOrFail($id);
        $notes = $etudiant->notes()->where('valeur', '>', 0)->get();
        $examens = array();

        foreach ($notes as $note) {
            $examen = $note->examen;
            $matiere = $examen->matiere;
            $examens[$matiere->nom][] = $examen;
        }

        return response()->json(['examens' => $examens], 200);
    }

}
