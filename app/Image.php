<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //
    protected $table = 'image';
    public $incrementing = false;
    public $guarded = [];

    public static function myImage()
    {
        $network = self::get();
        $mycompute = [];
        $uid = userrow('id');
        foreach ($network as $val) {
            if ($val->rule == 'ALL') {
                $mycompute[] = $val;
            } else {
                $k = json_decode($val->rule, true);
                if (in_array($uid, $k)) {
                    $mycompute[] = $val;
                }
            }
        }
        return $mycompute;
    }
}
