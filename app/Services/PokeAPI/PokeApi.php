<?php

namespace App\Services\PokeAPI;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class PokeApi 
{

    const URL_POKEAPI = "https://pokeapi.co/api/v2/";

    /**
     * Langue renseignée par l'utilisateur
     * 'fr' par défaut
     *
     * @var string
     */
    private $langue = 'fr';

    /**
     * Le client guzzle qui sera utilisé pour appeler la PokeAPI
     *
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * Listes des types existants
     *
     * @var array
     */
    private $liste_types = array();

    /**
     * Liste des pokemons
     *
     * @var array
     */
    private $liste_pokemons = array();

    /**
     * Liste des pokemons + les détails et leurs caractéristiques
     *
     * @var array
     */
    private $pokemon = array();

    /**
     * Liste des noms des stats en fonction de la langue demandée
     *
     * @var array
     */
    private $liste_stats = array();

    /**
     * Liste des versions dans lesquelles apparaissent le pokemon
     *
     * @var array
     */
    private $liste_versions = array();

    /**
     * remplit la propriété liste_versions
     *
     * @return void
     */
    public function setListeVersions() : void
    {
        $this->liste_versions = $this->getListe( 'version' );

    }

    /**
     * retourne la propriété liste_versions
     *
     * @return array
     */
    public function getListeVersions() : array 
    {
        return  $this->liste_versions;
    }

    /**
     * Récupère la liste des pokemons sur la PokeAPI et la met dans $this->liste_pokemons[id_name]=url
     *
     * @return void
     */
    public function setListePokemons() : void
    {
        $retour = $this->appelPokeAPI(self::URL_POKEAPI . 'pokemon');
        if (count($retour) > 0) {
            do {
                $resultats = $retour['results'];
                foreach ($resultats as $element) {
                    if (isset($element['url'])) {
                        $this->liste_pokemons[$element['name']] = $element['url'];
                    }
                }
            } while($retour = $this->appelPokeAPI($retour['next'])) ;
        }
    }

    /**
     * retourne la propriété liste_pokemons
     *
     * @return array
     */
    public function getListePokemons() : array
    {
        return $this->liste_pokemons;
    }

    /**
     * Liste des types de pokemons et leur traduction
     *
     * @return void
     */
    public function setListeTypes() : void
    {
        $this->liste_types = $this->getListe( 'type' );
    }

    /**
     * Retourne la propriété liste_types
     *
     * @return array
     */
    public function getListeTypes() : array
    {
        return $this->liste_types;
    }

    /**
     * Remplit un tableau avec tous les noms des stats traduits dans la langue sélectionnée
     *
     * @return void
     */
    private function setListeStats() : void
    {
        $this->liste_stats = $this->getListe( 'stat' );
    }

    /**
     * retourne la propriété liste_stats
     *
     * @return array
     */
    public function getListeStats() : array
    {
        return $this->liste_stats;
    }

    /**
     * retourne la langue choisie par l'utilisateur
     *
     * @return string
     */
    public function getLangue() : string 
    {
        return $this->langue;
    }

    public function setPokemon( string $url) : void 
    {
        $retour_poke = $this->appelPokeAPI($url);
        $retour_spec = $this->appelPokeAPI($retour_poke['species']['url']);

        $this->pokemon[$retour_poke['name']] = array(
            'image' => $retour_poke['sprites']['front_default'],
            'stats' => $retour_poke['stats'],
            'types' => $retour_poke['types'],
            'nom' => $this->extraireTraduction($retour_spec['names']),
            'poids' => $retour_poke['weight'],
            'taille' => $retour_poke['height'],
            'xp' => $retour_poke['base_experience'],
            'versions' => $retour_poke['game_indices'],
                );
    }



    /**
     * Constructeur
     * 
     * initialise la langue
     * initialise le client HttpGuzzle
     */
    function __construct(string $langue)
    {
        $this->langue = $langue;
        $this->client = new Client(); //GuzzleHttp\Client
    }

    /**
     * récupère de manière générique les traductions et les retourne sous forme de liste
     *
     * @param string $arg
     * @return array
     */
    private function getListe( string $arg ) : array
    {
        $retour = $this->appelPokeAPI(self::URL_POKEAPI . $arg);
        $liste = [];
        if (count($retour) > 0) {
            do {
                $resultats = $retour['results'];
                foreach ($resultats as $element) {
                    if (!array_key_exists($element['name'], $liste)) {
                        if (isset($element['url'])) {
                            $element_details = $this->appelPokeAPI($element['url']);
                            if (count($element_details) > 0) {
                                $liste[$element['name']] = $this->extraireTraduction($element_details['names']);
                            }
                        }
                    }
                }
            } while($retour = $this->appelPokeAPI($retour['next'])) ;
        }
        return $liste;
    }

    /**
     * Récupère la traduction d'un terme à partir du tableau de retour du WS
     *
     * @param array $retour
     * @return string
     */
    private function extraireTraduction(array $retour=array()) : string 
    {
        $traduction = '';
        foreach ($retour as $value) {
            if ($value['language']['name'] == $this->langue) {
                $traduction = $value['name'];
            }
        }
        return $traduction;
    }
    
    /**
     * Appel du WS PokeAPI
     *
     * @param string $url
     * @return array
     */
    private function appelPokeAPI(string $url) : array
    {
        $retour = json_decode($this->client->get($url), true);
        
        if (is_array($retour)) {
            return $retour;
        } else {
            return [];
        }        
    }
    
}