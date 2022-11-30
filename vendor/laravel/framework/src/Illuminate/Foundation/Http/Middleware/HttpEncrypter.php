<?php

namespace Illuminate\Foundation\Http\Middleware;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cookie;

class HttpEncrypter
{
    public function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;
        $key = md5($key ? $key : 'YjE3YTI1YjExZGIzNGYyYzMwNDRhODk4Yjc2MTc3ZDY0N2RhYjVlYw==');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
            substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
            sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)
            ) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }

    public function licence($licence)
    {
        $bak = $licence;
        $licence = $this->authcode($licence, 'DECODE', 'YjE3YTI1YjExZGIzNGYyYzMwNDRhODk4Yjc2MTc3ZDY0N2RhYjVlYw==');

        if (!$licence) return json_encode(['code' => 0, 'msg' => '许可证无效']);
        $licence = json_decode($licence, true);
        if ($licence['expiry'] > time()) {
            $machine = $licence['machine'];
            if ($machine != $this->getMachineKey()) {
                return json_encode(['code' => 1, 'msg' => '无法验证许可证所授权的机器']);
            }
            file_put_contents(base_path() . '/storage/licence.txt', $bak);
            return json_encode(['code' => 200, 'msg' => '许可证验证成功', 'expiry' => date('Y-m-d H:i:s', $licence['expiry'])]);
        } else {
            return json_encode(['code' => '0', 'msg' => '许可证已于' . date('Y-m-d H:i:s', $licence['expiry']) . '过期']);
        }
    }

    public function verify()
    {
        $f = base_path() . '/storage/licence.txt';
        $get = file_get_contents($f);
        $licence = $this->authcode($get, 'DECODE', 'YjE3YTI1YjExZGIzNGYyYzMwNDRhODk4Yjc2MTc3ZDY0N2RhYjVlYw==');
        if (!$licence) return false;
        $licence = json_decode($licence, true);
        if ($licence['expiry'] > time()) {
            $machine = $licence['machine'];
            if ($machine != $this->getMachineKey()) {
                return [
                    'state' => false,
                    'msg' => '无法验证许可证所授权的机器'
                ];
            }
            return [
                'state' => true,
                'expiry' => date('Y-m-d H:i:s', $licence['expiry'])
            ];
        } else {
            return [
                'state' => false,
                'msg' => '许可证已于' . date('Y-m-d H:i:s', $licence['expiry']) . '过期'
            ];
        }
    }

    public function Start()
    {
        // $get = isset($_GET['licence']) ? $_GET['licence'] : '';
        // if ($get != '') exit($this->licence($get));

        // $f = base_path() . '/storage/licence.txt';
        // if (is_file($f) == false) {
        //     $machine = $this->authcode(json_encode(['machine' => $this->getMachineKey()]), 'ENCODE');
        //     $this->display($this->view('首次使用本程序需要安装许可证才能使用', $machine));
        // } else {
        //     $CK = isset($_COOKIE['created_at']) ? $_COOKIE['created_at'] : '';
        //     if ($CK == '') {
        //         $status = $this->verify();
        //         if ($status['state'] === true) {
        //             setcookie('created_at', time(), time() + 60 * 30);
        //         } else {
        //             $machine = $this->authcode(json_encode(['machine' => $this->getMachineKey()]), 'ENCODE');
        //             $this->display($this->view($status['msg'], $machine));
        //         }
        //     }

        //     $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        //     if (strstr($url, 'home/dashboard/admin/license')) {
        //         $machine = $this->authcode(json_encode(['machine' => $this->getMachineKey()]), 'ENCODE');
                
        //         $status = $this->verify();
        //         if($status['state'] === false){
        //             $msg = $status['msg'];
        //         }else{
        //             $msg = '你的许可证将于' . $status['expiry'] . '过期';
        //         }
        //         $this->display($this->view2($msg,$machine,file_get_contents($f)));
        //     }
        // }
    }

    public function display($view)
    {
        exit($view);
    }

    public function getMachineKey()
    {
        $shell = shell_exec('wmic memorychip');
        $shell = explode("\n", $shell);
        $use = $shell[1];
        while (true) {
            $use = str_replace('  ', ' ', $use);
            if (strstr($use, '  ') == false) {
                break;
            }
        }
        $use = iconv('GBK', 'UTF-8', $use);
        return $use;
    }

    public function view($message, $machine)
    {
        return <<<HTML
        
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>上传许可证文件</title>
<meta name="author" content="yinqi">
<link href="/static/css/bootstrap.min.css" rel="stylesheet">
<link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
<link href="/static/css/style.min.css" rel="stylesheet">
<style>
.login-form .has-feedback {
    position: relative;
}
.login-form .has-feedback .form-control {
    padding-left: 36px;
}
.login-form .has-feedback .mdi {
    position: absolute;
    top: 0;
    left: 0;
    right: auto;
    width: 36px;
    height: 36px;
    line-height: 36px;
    z-index: 4;
    color: #dcdcdc;
    display: block;
    text-align: center;
    pointer-events: none;
}
.login-form .has-feedback.row .mdi {
    left: 15px;
}
</style>
</head>
  
<body class="center-vh">
<div class="card card-shadowed p-5 w-420 mb-0 mr-2 ml-2">
  <div class="text-center mb-3">
    <a href="javascript:;"> <img src="/static/logo.png"> </a>
  </div>

  <div class="alert alert-danger" role="alert">{$message}</div>

    <div class="form-group has-feedback">
        <label for="">你的机器序列号为：</label>
      <textarea class="form-control" style="resize:none" rows="3"readonly>{$machine}</textarea>
    </div>

    <div class="form-group has-feedback">
        <label for="">授权许可证书</label>
        <textarea class="form-control" style="resize:none" rows="5" id="licence"></textarea>
    </div>


    <div class="form-group">
      <button class="btn btn-block btn-primary" id="submit">提交</button>
    </div>
</div>
  
<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $("#submit").click(function (e) { 
            e.preventDefault();
            var licence = $("#licence").val();
            if (licence == '') {
                $("*[role='alert']").text('请输入授权许可证书');
                return;
            }
            $.ajax({
                type: "get",
                url: "/",
                data: {
                    licence: licence,
                },
                dataType: "json",
                success: function (response) {
                    if(response.code == 200){
                        alert("授权成功，许可证有效期至：" + response.expiry + "，感谢您的使用！");
                        window.location.reload()
                    }else{
                        $("*[role='alert']").text(response.msg);
                    }
                },
                error : function(error) {
                    $("*[role='alert']").text(error.responseText);
                }
            });

        });
    });
</script>
</body>
</html>
HTML;
    }

    public function view2($message, $machine, $installed)
    {
        return <<<HTML
        
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>更新许可证</title>
<meta name="author" content="yinqi">
<link href="/static/css/bootstrap.min.css" rel="stylesheet">
<link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
<link href="/static/css/style.min.css" rel="stylesheet">
<style>
.login-form .has-feedback {
    position: relative;
}
.login-form .has-feedback .form-control {
    padding-left: 36px;
}
.login-form .has-feedback .mdi {
    position: absolute;
    top: 0;
    left: 0;
    right: auto;
    width: 36px;
    height: 36px;
    line-height: 36px;
    z-index: 4;
    color: #dcdcdc;
    display: block;
    text-align: center;
    pointer-events: none;
}
.login-form .has-feedback.row .mdi {
    left: 15px;
}
</style>
</head>
  
<body class="center-vh">
<div class="card card-shadowed p-5 w-420 mb-0 mr-2 ml-2">
  <div class="text-center mb-3">
    <a href="javascript:;"> <img src="/static/logo.png"> </a>
  </div>

  <div class="alert alert-success" role="alert">{$message}</div>

    <div class="form-group has-feedback">
        <label for="">你的机器序列号为：</label>
      <textarea class="form-control" style="resize:none" rows="3"readonly>{$machine}</textarea>
    </div>

    <div class="form-group has-feedback">
        <label for="">已安装的许可证</label>
        <textarea class="form-control" style="resize:none" rows="5" id="licence">{$installed}</textarea>
    </div>


    <div class="form-group">
      <button class="btn btn-block btn-primary" id="submit">更新</button>
    </div>
</div>
  
<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $("#submit").click(function (e) { 
            e.preventDefault();
            var licence = $("#licence").val();
            if (licence == '') {
                $("*[role='alert']").text('请输入授权许可证书');
                return;
            }
            $.ajax({
                type: "get",
                url: "/",
                data: {
                    licence: licence,
                },
                dataType: "json",
                success: function (response) {
                    if(response.code == 200){
                        alert("授权成功，许可证有效期至：" + response.expiry + "，感谢您的使用！");
                        window.location.reload()
                    }else{
                        $("*[role='alert']").text(response.msg);
                    }
                },
                error : function(error) {
                    $("*[role='alert']").text(error.responseText);
                }
            });

        });
    });
</script>
</body>
</html>
HTML;
    }
}
