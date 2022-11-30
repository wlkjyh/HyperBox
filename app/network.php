<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class network extends Model
{
    //
    protected $table = 'network';
    public $incrementing = false;
    public $guarded = [];

    public static function myNetwork()
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

    public static function getipanduse($id)
    {
        $row = self::where('id', $id)->first();
        $ip = json_decode($row->ippool, true);
        $ipa = count($ip);
        if ($ipa == 0) return false;
        $ipnew = '';
        foreach ($ip as $k => $val) {
            if($val == 'true'){
                $ipnew = $k;
                unset($ip[$k]);
                break;
            }
        }
        $ip[$ipnew] = 'false';
        $row->ippool = json_encode($ip);
        $row->save();
        return $ipnew;

    }

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
    
}
