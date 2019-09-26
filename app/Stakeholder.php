<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stakeholder extends Model
{
    protected $fillable = ['alias_name', 'textbox_support_required', 'survey_choice'];
}
