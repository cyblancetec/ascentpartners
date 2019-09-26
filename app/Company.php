<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'stock_code', 'industry_type', 'fiscal_year'];
}
