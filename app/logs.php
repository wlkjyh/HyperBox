<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class logs extends Model
{
    //
    protected $table = 'logs';
    public $incrementing = false;
    public $guarded = [];
}
