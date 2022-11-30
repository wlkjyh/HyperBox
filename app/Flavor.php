<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flavor extends Model
{
    //
    protected $table = 'flavor';
    public $incrementing = false;
    public $guarded = [];
}
