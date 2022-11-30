<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    //
    protected $table = 'config';
    // 主键是k
    protected $primaryKey = 'k';
    public $incrementing = false;
    public $guarded = [];
    // 关闭时间戳
    public $timestamps = false;
}
