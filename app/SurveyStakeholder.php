<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyStakeholder extends Model
{
    protected $fillable = ['survey_id', 'stakeholder_id', 'sample_size'];
}
