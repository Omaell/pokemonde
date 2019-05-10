<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\PokemonRepository;
use PHPUnit\Runner\Exception;

class RechercheInfoPokemons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pokemonde:seeddb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Appelle la PokéAPI pour récupérer des informations sur les pokemons et remplit la base de données Pokemonde.';

    protected $pokemonRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PokemonRepository $pokemonRepository)
    {
        parent::__construct();
        $this->pokemonRepository = $pokemonRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Recherche des données en cours...');
        try {
            $this->pokemonRepository->recupereTypes();
            $this->pokemonRepository->recupereStats();
            $this->pokemonRepository->recuperePokemons();
            $this->info('Les données sont enregistrées en base de données.');

        } catch (Exception $e) {
            $this->error("Une erreur s'est produite pendant le script.");
            exit;
        }
       
    }
}
