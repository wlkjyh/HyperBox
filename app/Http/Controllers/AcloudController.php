<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Users, Compute, Flavor, network, Instance, Client, Image};

class AcloudController extends Controller
{
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     * 
     * 云桌面首页，返回当前用户所有的云桌面
     */
    public function index(Request $request)
    {
        try {
            $myInstance = Instance::where('userid', userrow('id'))->get();
            return view('acloud.index', ['myInstance' => $myInstance]);
        } catch (\Throwable $th) {
            return $this->throwable($th->getMessage());
        }
    }


    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     * 注销登录
     */
    public function logout(Request $request)
    {
        try {
            $request->session()->forget('token');
            return redirect('/acloud.middleware');
        } catch (\Throwable $th) {
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     * 获取验证码
     */
    public function code(Request $request)
    {
        $length = 4;
        $im_x = 160;
        $im_y = 40;

        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $text = "";
        for ($i = 0; $i < $length; $i++) {
            $num[$i] = rand(0, 25);
            $text .= $str[$num[$i]];
        }
        $text = strtolower($text);

        $request->session()->put('captcha', $text);
        // $_SESSION['code_google_text'] = $text;
        //生成验证码图片
        $im = imagecreatetruecolor($im_x, $im_y);
        $text_c = ImageColorAllocate($im, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
        $tmpC0 = mt_rand(100, 255);
        $tmpC1 = mt_rand(100, 255);
        $tmpC2 = mt_rand(100, 255);
        $buttum_c = ImageColorAllocate($im, $tmpC0, $tmpC1, $tmpC2);
        imagefill($im, 16, 13, $buttum_c);

        $font = public_path() . '/static/t1.ttf';

        for ($i = 0; $i < strlen($text); $i++) {
            $tmp = substr($text, $i, 1);
            $array = array(-1, 1);
            $p = array_rand($array);
            $an = $array[$p] * mt_rand(1, 10); //角度
            $size = 28;
            imagettftext($im, $size, $an, 15 + $i * $size, 35, $text_c, $font, $tmp);
        }


        $distortion_im = imagecreatetruecolor($im_x, $im_y);

        imagefill($distortion_im, 16, 13, $buttum_c);
        for ($i = 0; $i < $im_x; $i++) {
            for ($j = 0; $j < $im_y; $j++) {
                $rgb = imagecolorat($im, $i, $j);
                if ((int)($i + 20 + sin($j / $im_y * 2 * M_PI) * 10) <= imagesx($distortion_im) && (int)($i + 20 + sin($j / $im_y * 2 * M_PI) * 10) >= 0) {
                    imagesetpixel($distortion_im, (int)($i + 10 + sin($j / $im_y * 2 * M_PI - M_PI * 0.1) * 4), $j, $rgb);
                }
            }
        }
        //加入干扰象素;
        $count = 160; //干扰像素的数量
        for ($i = 0; $i < $count; $i++) {
            $randcolor = ImageColorallocate($distortion_im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagesetpixel($distortion_im, mt_rand() % $im_x, mt_rand() % $im_y, $randcolor);
        }

        $rand = mt_rand(5, 30);
        $rand1 = mt_rand(15, 25);
        $rand2 = mt_rand(5, 10);
        for ($yy = $rand; $yy <= +$rand + 2; $yy++) {
            for ($px = -80; $px <= 80; $px = $px + 0.1) {
                $x = $px / $rand1;
                if ($x != 0) {
                    $y = sin($x);
                }
                $py = $y * $rand2;

                imagesetpixel($distortion_im, $px + 80, $py + $yy, $text_c);
            }
        }


        //以PNG格式将图像输出到浏览器或文件;
        imagepng($distortion_im);
        $resource = ob_get_clean();
        return response($resource)->header('Content-Type', 'image/png');
    }

    public function freeoauth(Request $request)
    {
        try {
            if (getconfig('freeoauth') != 'enable') return $this->throwable('freeoauth is disable');
            //code...
            $ClietntIp = $request->getClientIp();
            // 有端口就去掉端口
            $ClietntIp = explode(':', $ClietntIp)[0];
            $macaddr = getmacbyip($ClietntIp);
            if($macaddr == 'localhost' && getconfig('localauto') == 'enable'){
                $user = Users::where('username','admin')->first();
                if(!$user) return $this->display(401, '不受信任');
                $callback_url = '/home/dashboard';
                $request->session()->forget('captchashow');
                $token = uuid();
                $user->token = $token;
                $user->loginfail = 0;
                $user->save();
                $request->session()->put('token', $token);
                return $this->display(200, '认证成功', ['callback_url' => $callback_url]);
            }


            if ($macaddr == false) return $this->display(401, '不受信任');
            $macaddr = strtolower($macaddr);
            $freeoauth = \App\Config::where('k', 'freeoauth')->first();
            $json = json_decode($freeoauth->v, true);
            foreach ($json as $key => $value) {
                if ($key == $macaddr) {
                    $uid = $value;
                    $user = Users::where('id', $uid)->first();
                    if(!$user) return $this->display(401, '不受信任');
                    if($user->username == 'admin') $callback_url = '/home/dashboard';
                    else $callback_url = '/acloud';
                    $request->session()->forget('captchashow');
                    $token = uuid();
                    $user->token = $token;
                    $user->loginfail = 0;
                    $user->save();
                    $request->session()->put('token', $token);
                    return $this->display(200, 'Success', ['callback_url' => $callback_url]);
                }
            }
            return $this->display(401, '认证成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }
}
