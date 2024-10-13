<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [ 'date', 'usd_s','usd_a','eur_s','eur_a'];
}

