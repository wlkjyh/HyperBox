<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    protected $table = 'group';
    public $incrementing = false;
    public $guarded = [];
}
