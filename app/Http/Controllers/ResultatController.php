<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Examen;

class ResultatController extends Controller
{
    public function show($etudiant_id, $examen_id)
    {
        $examen = Examen::with(['questions.propositions', 'questions.reponses' => function ($query) use ($etudiant_id) {
            $query->where('etudiant_id', $etudiant_id);
        }])->findOrFail($examen_id);

        $resultats = [];

        foreach ($examen->questions as $question) {
            $reponse_etudiant = $question->reponses->first();
            $proposition_etudiant = $reponse_etudiant ? $reponse_etudiant->proposition : null; // Modifiez cette ligne
            $proposition_correcte = $question->propositions->where('est_correcte', 1)->first();
            $est_correct = $proposition_etudiant && $proposition_correcte && $proposition_etudiant->id === $proposition_correcte->id; // Modifiez cette ligne

            $resultats[] = [
                'question' => $question->libelle,
                'reponse_etudiant' => $proposition_etudiant ? $proposition_etudiant->libelle : null, // Modifiez cette ligne
                'proposition_correcte' => $proposition_correcte->libelle,
                'est_correct' => $est_correct,
            ];
        }

        return response()->json(['resultats' => $resultats]);
    }


}