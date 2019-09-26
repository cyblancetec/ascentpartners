<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = ['company_id', 'title', 'fiscal_entry', 'expiry_date', 'unique_link'];
}
