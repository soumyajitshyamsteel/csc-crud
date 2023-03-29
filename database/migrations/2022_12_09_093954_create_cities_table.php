<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            // $table->uuid();
            $table->string('name');
            $table->string('slug')->nullable()->index();
            $table->foreignId('state_id')->references('id')->on('states')->onDelete('cascade');
            // $table->char('state_code', 2);
            $table->text('state_code')->nullable();
            $table->foreignId('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->char('country_code', 2)->nullable();
            $table->string('fips_code', 255)->nullable();
            $table->string('iso2', 255)->nullable();
            $table->string('type', 191)->nullable();
            $table->decimal('latitude', 15, 8)->nullable();
            $table->decimal('longitude', 15, 8)->nullable();
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
        Schema::dropIfExists('cities');
    }
}
