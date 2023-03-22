<?php

namespace App\Http\Controllers;

use App\Models\Etudiant ;
use Illuminate\Http\Request;
use Illuminate\Http\Response ;
use Illuminate\Support\Facades\Hash ;

class AuthController extends Controller
{
    public function  register(Request $request) {
        $fields = $request->validate([
            'nom' => 'required|string' ,
            'prenom' => 'required|string',
            'email' => 'required|string|unique:etudiants,email',
            'password' => 'required|string|confirmed',
            'filiere_id'=> 'required|string'
        ]);

        $etudiant = Etudiant::create([
            'nom' => $fields['nom'] ,
            'prenom' => $fields['prenom'] ,
            'email' => $fields['email'] ,
            'password' => bcrypt($fields['password']) ,
            'filiere_id' => $fields['filiere_id'] ,
        ]);

        $token = $etudiant->createToken('etudiantToken')->plainTextToken ;

        $response = [
            'etudiant' => $etudiant,
            'token' => $token
        ] ;

        return response($response, 201) ;
    }

    public function  login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check Email

        $etudiant = Etudiant::where('email', $fields['email'])-> first();

        //Check password

        if(!$etudiant || !Hash::check($fields['password'], $etudiant->password)){
            return response([
                'message' => 'incorrect informations'
            ], 401);
        }

        $token = $etudiant->createToken('etudiantToken')->plainTextToken ;

        $response = [
            'etudiant' => $etudiant,
            'token' => $token
        ] ;

        return response($response, 201) ;
    }

    public function logout(Request $request) {
        $user = auth()->user();
        if ($user) {
            $user->tokens()->delete();
        }
        return[
            'message' => 'Disconnected'
        ];
    }
}
