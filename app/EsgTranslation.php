<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EsgTranslation extends Model
{
    protected $fillable = ['esg_id', 'locale', 'title', 'information'];
}
