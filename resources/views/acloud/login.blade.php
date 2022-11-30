<!DOCTYPE html>
<html>

<head>

    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="format-detection" content="email=no" />
    <meta name="wap-font-scale" content="no" />
    <meta name="viewport" content="user-scalable=no, width=device-width" />
    <meta content="telephone=no" name="format-detection" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>身份认证</title>
    <link href="/static/acloud/css/ax.css" rel="stylesheet" type="text/css">
    <link href="/static/acloud/css/ax-response.css" rel="stylesheet" type="text/css">
    <link href="/static/acloud/css/main.css" rel="stylesheet" type="text/css">
</head>

<body class="ax-align-origin">


    <div class="login ax-shadow-cloud ax-radius-md">
        <div class="ax-row ax-radius-md ax-split">
            <div class="ax-col ax-col-14 ax-radius-left ax-radius-md cover"></div>
            <div class="ax-col ax-col-10">
                <div class="core">

                    <div class="ax-break"></div>

                    <div class="ax-tab" axTab>

                        <ul class="ax-row ax-tab-nav ax-menu-tab">
                            <a href="javascript:;" class="ax-item" style="display:none">登录账号</a>
                            <a href="javascript:;" class="ax-item" style="display:none"></a>
                            <li class="ax-col"></li>
                        </ul>

                        <ul class="ax-tab-content">
                            <li>
                                <!-- <form> -->

                                <div class="ax-break"></div>
                                <div class="ax-break ax-hide-tel"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input"><span class="ax-pos-left" style="width: 2.4rem;"><i class="ax-iconfont ax-icon-me-f"></i></span><input id="username" placeholder="请输入用户名/邮箱" type="text"><span class="ax-pos-right"><a href="javascript:;" class="ax-iconfont ax-icon-close ax-val-none"></a></span></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break-md"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input"><span class="ax-pos-left" style="width:2.4rem;"><i class="ax-iconfont ax-icon-lock-f"></i></span>
                                                <input id="password" placeholder="输入登录密码" type="password"><span class="ax-pos-right"><a href="javascript:;" class="ax-iconfont ax-icon-close ax-val-none"></a></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="captchashow" style="{{Session::get('captchashow','display:none')}}">
                                    <div class="ax-break-md"></div>

                                    <div class="ax-form-group">
                                        <div class="ax-flex-row">
                                            <div class="ax-form-con">
                                                <div class="ax-form-input">
                                                    <div class="ax-row">
                                                        <div class="ax-flex-block">
                                                            <span class="ax-pos-left" style="width:2.4rem;"><i class="ax-iconfont ax-icon-shield-f"></i></span>
                                                            <input type="text" id="code" placeholder="请输入验证码"><span class="ax-pos-right"><a href="javascript:;" class="ax-iconfont ax-icon-close ax-val-none"></a></span>
                                                        </div>
                                                        <a href="javascript:;" class="ax-form-img"><img src="/code.png" id="a" onclick="this.src='/code.png?r=' + Math.random()"></a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="ax-break-md"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input">
                                                <div class="ax-row">
                                                    <div class="ax-flex-block">
                                                        <label class="ax-checkbox"><input name="free-agree" value="0" checked="" type="checkbox"><span>记住密码</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="ax-break-md"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-flex-block">
                                            <div class="ax-form-input"><button id="logincall" type="button" class="ax-btn ax-primary ax-full">连接</button></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break"></div>
                                <div class="ax-break ax-hide-tel"></div>
                                <div class="ax-break ax-hide-tel"></div>

                                <!-- </form> -->
                            </li>
                        </ul>
                    </div>





                </div>
            </div>
        </div>
    </div>


    <script src="/static/acloud/js/ax.min.js" type="text/javascript"></script>
    <script src="/static/js/jquery.min.js"></script>
    <script src="/static/layer/layer.js"></script>
</body>

<script>
    $(document).ready(function() {
        $("#logincall").click(function(e) {
            e.preventDefault();
            var username = $("#username").val();
            var password = $("#password").val();
            var code = $("#code").val();
            if (username == '') {
                layer.msg('请输入用户名/邮箱', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            if (password == '') {
                layer.msg('请输入登录密码', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            // if (code == '') {
            //     layer.msg('请输入验证码', {
            //         icon: 5,
            //         time: 1000
            //     });
            //     return false;
            // }
            // 正在进行
            var load = layer.msg('正在进行...', {
                icon: 16,
                shade: 0.3,
                time: 0
            });

            $.ajax({
                type: "post",
                url: "/home/api/authentication",
                data: {
                    loginname: username,
                    password,
                    code,
                    _token: '{{csrf_token()}}'
                },
                dataType: "json",
                success: function(response) {
                    layer.close(load);
                    if (response.code == 200) {
                        window.location.href = response.data['callback_url'];
                    } else {
                        layer.msg(response.msg, {
                            icon: 5
                        });
                        if (response.data['captchashow'] == 'OK') {
                            $("#captchashow").show();
                        }
                        $("#a").click();
                    }
                },
                error: function() {
                    layer.close(load);
                    layer.msg('网络错误，请稍后再试！', {
                        icon: 5
                    });
                    $("#a").click();
                }
            });

        });


        <?php
            if(getconfig('freeoauth') == 'enable'){
                echo <<<JS
                // 免密认证，版本：1.0
                $.ajax({
                    type : 'get',
                    url: "/acloud/freeoauth/1.0",
                    data: {
                        r : Math.random()
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.code == 200){
                            layer.msg(response.msg, {
                                icon: 6
                            });
                            setTimeout(() => {
                                window.location.href = response.data['callback_url'];
                            }, 500);
                        }
                    }
                });
JS;
            }
        ?>
    });
</script>

</html>