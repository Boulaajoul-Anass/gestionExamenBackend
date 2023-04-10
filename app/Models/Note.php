<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
     protected $fillable = ['etudiant_id', 'examen_id', 'valeur'];

     public function examens()
     {
         return $this->belongsToMany(Examen::class)->withPivot('valeur');
     }
     public function etudiants()
    {
        return $this->belongsToMany(Etudiant::class)->withPivot('valeur');
    }
}
