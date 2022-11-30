<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Volume extends Model
{
    //
    protected $table = 'volume';
    public $incrementing = false;
    public $guarded = [];
}
