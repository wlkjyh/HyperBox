<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function throwable($ERROR, $err2 = 1)
    {
        // if($err2 != 1){
        return $this->display(500,$ERROR);
        // }
        return view('Throwable', compact('ERROR'));
    }

    public function display($code, $message, $data = [])
    {
        return response()->json([
            'reqid' => uuid(),
            'code' => $code,
            'msg' => $message,
            'data' => $data,
            'version' => '1.0'
        ]);
    }
}
