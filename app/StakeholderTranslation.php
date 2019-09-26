<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StakeholderTranslation extends Model
{
    protected $fillable = ['stakeholder_id', 'locale', 'title'];
}
