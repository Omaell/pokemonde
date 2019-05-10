<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePokeapipokemonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table des pokemons
        Schema::create('pokemonde_pokeapi_pokemons', function (Blueprint $table) {
            $table->unsignedSmallInteger('pokeapi_pokemon_id_int');
            $table->string('pokeapi_pokemon_id_str', 80);
            $table->unsignedSmallInteger('pokeapi_height');
            $table->unsignedSmallInteger('pokeapi_weight');
            $table->string('pokeapi_pokemon_sprite');
            $table->timestamps();
            $table->primary(['pokeapi_pokemon_id_int', 'pokeapi_pokemon_id_str']);	
        });

        // Table des traductions
        Schema::create('pokemonde_pokeapi_trad', function (Blueprint $table) {
            $table->string('pokeapi_categ', 50);
            $table->string('pokeapi_categ_id_str', 80);
            $table->string('langue');
            $table->string('trad');
            $table->timestamps();
            $table->primary(['pokeapi_categ', 'pokeapi_categ_id_str','langue']);	
        });

        // Table de liaison entre les pokemons et leurs stats
        Schema::create('pokemonde_pokeapi_pokemons_stats', function (Blueprint $table) {
            $table->unsignedSmallInteger('pokeapi_pokemon_id_int');
            $table->string('pokeapi_pokemon_id_str', 80);
            $table->string('pokeapi_categ', 80)->default('stats');
            $table->string('pokeapi_categ_id_str', 80);
            $table->unsignedSmallInteger('stat_valeur');
            $table->timestamps();
            $table->primary(['pokeapi_pokemon_id_int', 'pokeapi_pokemon_id_str', 'pokeapi_categ', 'pokeapi_categ_id_str']);	
            $table->foreign(['pokeapi_pokemon_id_int', 'pokeapi_pokemon_id_str'])
                  ->references(['pokeapi_pokemon_id_int', 'pokeapi_pokemon_id_str'])
                  ->on('pokemonde_pokeapi_pokemons');
        });

        // Table de liaison entre les pokemons et leurs types
        Schema::create('pokemonde_pokeapi_pokemons_types', function (Blueprint $table) {
            $table->unsignedSmallInteger('pokeapi_pokemon_id_int');
            $table->string('pokeapi_pokemon_id_str', 80);
            $table->string('pokeapi_categ', 80)->default('types');
            $table->string('pokeapi_categ_id_str', 80);
            $table->timestamps();
            $table->primary(['pokeapi_pokemon_id_int', 'pokeapi_pokemon_id_str', 'pokeapi_categ', 'pokeapi_categ_id_str']);
            $table->foreign(['pokeapi_pokemon_id_int', 'pokeapi_pokemon_id_str'])
                  ->references(['pokeapi_pokemon_id_int', 'pokeapi_pokemon_id_str'])
                  ->on('pokemonde_pokeapi_pokemons');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pokemonde_pokeapi_pokemons_stats');
        Schema::dropIfExists('pokemonde_pokeapi_pokemons_types');
        Schema::dropIfExists('pokemonde_pokeapi_pokemons');
        Schema::dropIfExists('pokemonde_pokeapi_trad');
    }
}
