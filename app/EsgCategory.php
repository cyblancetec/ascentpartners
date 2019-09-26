<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EsgCategory extends Model
{
    protected $fillable = ['en_title', 'zh_title', 'zh-Hant_title'];
}
