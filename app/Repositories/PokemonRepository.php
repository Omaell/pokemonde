<?php
namespace App\Repositories;

use App\PokemonTrad;
use App\Services\PokeAPI\PokeApi;

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
        $this->pokeApi->setLangue('fr');
        $this->pokeApi->setListeTypes();
        $liste_types_fr = $this->pokeApi->getListeTypes();

        $this->pokeApi->setLangue('en');
        $this->pokeApi->setListeTypes();
        $liste_types_en =  $this->pokeApi->getListeTypes();

        foreach($liste_types_fr as $id => $traduction) {
            PokemonTrad::firstOrCreate(array(
                'pokeapi_categ' =>'type',
                'pokeapi_categ_id_str' => $id,
                'trad_fr' => $traduction,
                'trad_en' => $liste_types_en[$id],
            ));
        }
    }

    public function recupereStats()
    {
        $this->pokeApi->setLangue('fr');
        $this->pokeApi->setListeStats();
        $liste_stats_fr = $this->pokeApi->getListeStats();

        $this->pokeApi->setLangue('en');
        $this->pokeApi->setListeStats();
        $liste_stats_en =  $this->pokeApi->getListeStats();

        foreach($liste_stats_fr as $id => $traduction) {
            PokemonTrad::firstOrCreate(array(
                'pokeapi_categ' =>'stat',
                'pokeapi_categ_id_str' => $id,
                'trad_fr' => $traduction,
                'trad_en' => $liste_stats_en[$id],
            ));
        }
    }


    
}