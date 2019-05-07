<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PokemonTrad extends Model
{
    protected $table = 'pokemonde_pokeapi_trad';

    /**
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pokemons() : \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\Pokemon');
    }
}
