<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Etudiant extends Model
{
    use HasFactory, Notifiable, HasApiTokens;

   protected $fillable = ['nom', 'prenom', 'email', 'password', 'filiere_id'];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function examens()
    {
        return $this->belongsToMany(Examen::class)->withPivot('valeur');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    

}
