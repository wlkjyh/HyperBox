<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Users, Client};
use App\QRcode;

class IndexController extends Controller
{
    public function getQrcode(Request $request)
    {
        try {
            //code...
            $value = $request->input('value','null');
            QRcode::png($value,false,QR_ECLEVEL_L,5,2);
            $resource = ob_get_clean();
            return response($resource)->header('Content-Type', 'image/png');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th);
        }
    }
}
