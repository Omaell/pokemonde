<?php
namespace App\Repositories;

use App\PokemonTrad;
use App\Pokemon;
use App\Services\PokeAPI\PokeApi;
use App\PokemonStat;
use App\PokemonType;

class PokemonRepository
{

    protected $pokemon_trad;

    private $pokeApi;

    public function __construct(PokeApi $pokeApi)
    {
        $this->pokeApi = $pokeApi;
    }

    public function recupereTypes()
    {
        $this->pokeApi->setListeTypes();
        $liste_types = $this->pokeApi->getListeTypes();

        foreach($liste_types as $id => $traduction) {
            foreach($traduction as $langue => $chaine) {
                PokemonTrad::firstOrCreate(array(
                    'pokeapi_categ' =>'type',
                    'pokeapi_categ_id_str' => $id,
                    'langue' => $langue,
                    'trad' => $chaine,
                ));
            }
        }
    }

    public function recupereStats()
    {
        $this->pokeApi->setListeStats();
        $liste_stats = $this->pokeApi->getListeStats();

        foreach($liste_stats as $id => $traduction) {
            foreach($traduction as $langue => $chaine) {
                PokemonTrad::firstOrCreate(array(
                    'pokeapi_categ' =>'stat',
                    'pokeapi_categ_id_str' => $id,
                    'langue' => $langue,
                    'trad' => $chaine,
                ));
            }
        }
    }

    public function recuperePokemons()
    {
        $this->pokeApi->setListePokemons();
        $liste_pokemons = $this->pokeApi->getListePokemons();
        foreach($liste_pokemons as $url) {
            $this->pokeApi->setPokemons($url);
        }
        $pokemons = $this->pokeApi->getPokemons();

        foreach($pokemons as $id => $details) {
            Pokemon::firstOrCreate(array(
                'pokeapi_pokemon_id_int' => $details['id'],
                'pokeapi_pokemon_id_str' => $id,
                'pokeapi_height' => $details['taille'],
                'pokeapi_weight' => $details['poids'],
                'pokeapi_pokemon_sprite' => $details['image'],
            ));

            foreach($details['noms'] as $langue=>$nom){
                PokemonTrad::firstOrCreate(array(
                    'pokeapi_categ' =>'pokemon',
                    'pokeapi_categ_id_str' => $id,
                    'langue' => $langue,
                    'trad' => $nom,
                ));
            }

            foreach ($details['stats'] as $stat) {
                PokemonStat::firstOrCreate(array(
                    'pokeapi_pokemon_id_int' => $details['id'], 
                    'pokeapi_pokemon_id_str' => $id, 
                    'pokeapi_categ' => 'stat', 
                    'pokeapi_categ_id_str' => $stat['stat']['name'], 
                    'stat_valeur' => $stat['base_stat'],
                ));
            }

            foreach ($details['types'] as $type) {
                PokemonType::firstOrCreate(array(
                    'pokeapi_pokemon_id_int' => $details['id'], 
                    'pokeapi_pokemon_id_str' => $id, 
                    'pokeapi_categ' => 'type', 
                    'pokeapi_categ_id_str' => $type['type']['name'],
                ));
            }

        }

    }


    
}