<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    protected $table = 'instance';
    public $incrementing = false;
    public $guarded = [];
}
