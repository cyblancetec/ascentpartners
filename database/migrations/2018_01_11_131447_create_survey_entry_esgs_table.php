<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveyEntryEsgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_entry_esgs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('survey_entry_id');
            $table->integer('esg_id');
            $table->integer('esg_value');
            $table->enum('survey_type',array('stakeholder', 'company'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_entry_esgs');
    }
}
