<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendSurvey extends Model
{
    protected $fillable = ['company_id', 'user_id', 'survey_id', 'email'];
}
