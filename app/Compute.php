<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compute extends Model
{
    protected $table = 'compute';
    public $incrementing = false;
    public $guarded = [];

    public static function getRow($id, $filed = 'ALL')
    {
        if ($filed == 'ALL') {
            return self::where('id', $id)->first();
        } else {
            // return self::where('id',$id)->value($filed);
            $row = self::where('id', $id)->first();
            return $row->$filed;
        }
    }


    public static function myCompute()
    {
        $network = Compute::get();
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
