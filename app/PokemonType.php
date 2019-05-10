<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PokemonType extends Model
{
    protected $table = 'pokemonde_pokeapi_pokemons_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pokeapi_pokemon_id_int', 'pokeapi_pokemon_id_str', 'pokeapi_categ', 'pokeapi_categ_id_str'];

     /**
     * Les pokemons qui ont ce type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pokemons() : \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany('App\Pokemon');
    }

}
