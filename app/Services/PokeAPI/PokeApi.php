<?php

namespace App\Services\PokeAPI;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class PokeApi 
{

    const URL_POKEAPI = "https://pokeapi.co/api/v2/";

    /**
     * il y a plus de 900 pokemons disponibles, ça fait beaucoup à charger sachant qu'on est limité à 100 appels au WS par minute
     */
    const NB_MAX_POKEMONS = 50;



    /**
     * Undocumented variable
     *
     * @var int
     */
    private $compteur_dappel;

    /**
     * Langues gérées par l'application
     *
     * @var array
     */
    private $langues;

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
    private $pokemons = array();

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
                        if(count($this->liste_pokemons) == self::NB_MAX_POKEMONS) {
                            return;
                        }
                    }
                }
                if (empty($retour['next'])) {
                    return;
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
    public function setListeStats() : void
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
     * Va chercher les langues dans le fichier .env
     *
     * @return void
     */
    public function setLangues()
    {
        $this->langues = explode(',',env('APP_LANGUAGES'));
    }

    /**
     * retourne les langues
     *
     * @return array
     */
    public function getLangues() : array 
    {
        return $this->langues;
    }

    /**
     * remplit $pokemons avec toutes les informations concernant chaque pokemon
     *
     * @param string $url
     * @return void
     */
    public function setPokemons( string $url) : void 
    {
        $retour_poke = $this->appelPokeAPI($url);
        $retour_spec = $this->appelPokeAPI($retour_poke['species']['url']);
        $names = $this->extraireTraduction($retour_spec['names']);

        $this->pokemons[$retour_poke['name']] = array(
            'id' => $retour_poke['id'],
            'image' => $retour_poke['sprites']['front_default'],
            'stats' => $retour_poke['stats'],
            'types' => $retour_poke['types'],
            'nom' => $names,
            'poids' => $retour_poke['weight'],
            'taille' => $retour_poke['height'],
            'versions' => $retour_poke['game_indices'],
                );
    }

    public function getPokemons() : array
    {
        return $this->pokemons;
    }



    /**
     * Constructeur
     * 
     * initialise les langues configurées dans .env
     * initialise le client HttpGuzzle
     */
    function __construct()
    {
        $this->setLangues();
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
                if (empty($retour['next'])) {
                    break;
                }
            } while($retour = $this->appelPokeAPI($retour['next'])) ;
        }
        return $liste;
    }

    /**
     * Récupère la traduction d'un terme à partir du tableau de retour du WS
     *
     * @param array $retour
     * @return array
     */
    private function extraireTraduction(array $retour=array()) : array 
    {
        $traduction = [];
        foreach ($retour as $value) {
            if (in_array($value['language']['name'], $this->langues)) {
                $traduction[$value['language']['name']] = $value['name'];
            }
        }
        return $traduction;
    }
    
    /**
     * Appel du WS PokeAPI
     *
     * @param string $url
     * @return array|null
     */
    private function appelPokeAPI(string $url) : ?array
    {
        if (!empty($url))
        {
            $retour = json_decode($this->client->get($url)->getBody()->getContents(), true);
            $this->compteur_dappel ++;
            if($this->compteur_dappel == 100) {
                sleep(10);
                $this->compteur_dappel = 0;
            }
            if (is_array($retour)) {
                return $retour;
            } else {
                return null;
            }       
        }
    }
    
}