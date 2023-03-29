<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            // $table->uuid();
            $table->string('name');
            $table->string('slug')->nullable()->index();
            $table->foreignId('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->char('country_code', 2)->nullable();
            $table->string('fips_code', 255)->nullable();
            $table->string('iso2', 255)->nullable();
            // $table->string('type', 191);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->tinyInteger('flag')->default(true);
            $table->tinyInteger('status')->default(true);
            $table->string('wikiDataId')->nullable()->comment('Rapid API GeoDB Cities');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
}
