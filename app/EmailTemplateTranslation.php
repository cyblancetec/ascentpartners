<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTemplateTranslation extends Model
{
    protected $fillable = ['email_template_id', 'locale', 'subject', 'content'];
}
