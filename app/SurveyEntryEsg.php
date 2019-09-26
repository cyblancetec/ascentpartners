<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyEntryEsg extends Model
{
    protected $fillable = ['survey_entry_id', 'esg_id', 'esg_value', 'survey_type'];
}
