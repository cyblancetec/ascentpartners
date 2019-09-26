<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyEntryStakeholder extends Model
{
    protected $fillable = ['survey_entry_id', 'stakeholder_id', 'stakeholder_comment'];
}
