<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PokemonTrad extends Model
{
    protected $table = 'pokemonde_pokeapi_trad';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pokeapi_categ', 'pokeapi_categ_id_str', 'langue', 'trad'];
}
