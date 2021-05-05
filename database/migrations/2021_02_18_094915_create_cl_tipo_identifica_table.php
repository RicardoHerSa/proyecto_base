<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClTipoIdentificaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_tipo_identifica', function (Blueprint $table) {
            $table->id('id_tipo');
            $table->string('nombre_tipo', 50);
            $table->char('code_tipo', 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_tipo_identifica');
    }
}
