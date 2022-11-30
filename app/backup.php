<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class backup extends Model
{
    //
    protected $table = 'backup';
    public $incrementing = false;
    public $guarded = [];
}
