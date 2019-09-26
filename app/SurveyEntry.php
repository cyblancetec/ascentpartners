<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyEntry extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'survey_id', 'email',
    ];
}
