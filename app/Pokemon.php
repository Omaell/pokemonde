<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $table = 'pokemonde_pokeapi_pokemons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pokeapi_pokemon_id_int', 'pokeapi_pokemon_id_str', 'pokeapi_height', 'pokeapi_weight', 'pokeapi_pokemon_sprite'];

    /**
     * Les stats du pokemon
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function stats() : \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany('App\PokemonStat');
    }

    /**
     * Les types du pokemon
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function types() : \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany('App\PokemonType');
    }

    /**
     * Le nom traduit du pokemon
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function traduction() : \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\PokemonTrad');
    }
}
