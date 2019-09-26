<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveyEntryStakeholdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_entry_stakeholders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('survey_entry_id');
            $table->integer('stakeholder_id');
            $table->text('stakeholder_comment');
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
        Schema::dropIfExists('survey_entry_stakeholders');
    }
}
