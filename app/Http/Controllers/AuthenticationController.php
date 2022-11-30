<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Users;
use App\GooleAuth;

class AuthenticationController extends Controller
{
    //登录认证
    public function index(Request $request)
    {
        try {
            if (getconfig('basicauth') == '1') {
                $ServerAuthUser = $request->server('PHP_AUTH_USER');
                $ServerAuthPass = $request->server('PHP_AUTH_PW');
                $authorization = false;
                if ($ServerAuthUser == getconfig('authuser') && $ServerAuthPass == getconfig('authpass')) {
                    $authorization = true;
                }

                if (!$authorization) {
                    header("WWW-Authenticate:Basic realm='Private'");
                    header('HTTP/1.0 401 Unauthorized');
                    exit('You don\'t have permission to access on this server.');
                }
            }
            return view('Authentication');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th);
        }
    }


    // 登录认证请求
    public function authentication(Request $request)
    {
        try {
            //code...
            $loginname = $request->input('loginname');
            $password = $request->input('password');
            $code = $request->input('code');
            $google_code = $request->input('google_code');
            $captchashow = 'NO';
            if ($google_code != '') {
                if ($request->session()->get('adminloginbutneedauth') == 'okok') {
                    $Google = new GooleAuth();
                    if ($Google->verifyCode(getconfig('googleauthsecret'), $google_code, 2)) {
                        $request->session()->forget('adminloginbutneedauth');
                        $user = Users::where('username', 'admin')->first();
                        $request->session()->forget('captchashow');
                        $token = uuid();
                        $user->token = $token;
                        $user->loginfail = 0;
                        $user->save();
                        $request->session()->put('token', $token);
                        return $this->display(200, 'Success', ['callback_url' => '/home/dashboard']);
                    } else {
                        return $this->display(401, '无效的认证凭据');
                    }
                }
            }
            // 判断来源
            $referer = $request->server('HTTP_REFERER');
            if (strpos($referer, 'acloud')) {
                $user = Users::where('username', $loginname)->first();
                if ($user) {
                    $loginfail = $user->loginfail;
                    if ($loginfail >= 3) {
                        // 需要出验证码
                        $request->session()->put('captchashow', 'display:block');
                        $captchashow = 'OK';
                    } else {
                        $captchashow = 'NO';
                    }
                } else {
                    $captchashow = 'NO';
                }
                if ($captchashow == 'OK') {
                    if (strtolower($code) != strtolower($request->session()->get('captcha'))) {
                        return response()->json(['reqid' => uuid(), 'code' => 400, 'msg' => '验证码错误', 'data' => ['captchashow' => 'OK'], 'version' => '1.0']);
                    } else {
                        return response()->json(['reqid' => uuid(), 'code' => 400, 'msg' => '验证码错误', 'data' => ['captchashow' => 'OK'], 'version' => '1.0']);
                    }
                } else {
                    if ($code != '') {
                        if (strtolower($code) != strtolower($request->session()->get('captcha'))) {
                            return response()->json(['reqid' => uuid(), 'code' => 400, 'msg' => '验证码错误', 'data' => ['captchashow' => 'NO'], 'version' => '1.0']);
                        }
                    }
                }
                // 重置验证码
                $request->session()->forget('captcha');
            }

            $user = Users::where('username', $loginname)->first();
            if (!$user) {
                $user = Users::where('email', $loginname)->first();
                if (!$user) {
                    return $this->display(400, '用户名或者密码错误，请重新输入', ['captchashow' => $captchashow]);
                }
            }
            if (!password_verify($password, $user->password)) {
                $user->loginfail = $user->loginfail + 1;
                $user->save();
                return [
                    'code' => 400,
                    'reqid' => uuid(),
                    'msg' => '用户名或者密码错误，请重新输入',
                    'data' => ['captchashow' => $captchashow],
                ];
            }
            if ($user->username == 'admin') {
                $callback_url = '/home/dashboard';
                if (getconfig('googleauth') == '1') {
                    $request->session()->put('adminloginbutneedauth', 'okok');
                    return $this->display(400, '请提供动态认证凭据才能登录');
                }
            } else {
                $callback_url = '/home/dashboard';
            }

            $request->session()->forget('captchashow');
            $token = uuid();
            $user->token = $token;
            $user->loginfail = 0;
            $user->save();
            $request->session()->put('token', $token);
            return $this->display(200, 'Success', ['callback_url' => $callback_url]);
        } catch (\Throwable $th) {
            return $this->display(500, $th->getMessage());
        }
    }

    public function changepassword(Request $request)
    {
        try {
            //code...
            return view('dashboard.changepassword');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th);
        }
    }
    public function changepasswordApi(Request $request)
    {
        try {
            //code...
            $oldpwd = $request->input('oldpwd');
            $newpwd = $request->input('newpwd');
            if (!$oldpwd || !$newpwd) {
                return $this->display(400, '参数错误');
            }
            $user = Users::where('token', $request->session()->get('token'))->first();
            if (!$user) {
                return $this->display(400, '请先登录');
            }
            if (!password_verify($oldpwd, $user->password)) {
                return $this->display(400, '原密码错误');
            }
            $user->password = password_hash($newpwd, PASSWORD_DEFAULT);
            $user->token = '';
            $user->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }
}
