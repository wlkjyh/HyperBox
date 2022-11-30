<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>dreamstack - 身份认证</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <link href="/static/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css" rel="stylesheet">
</head>

<body>
    <div class="row no-gutters bg-white vh-100">
        <div class="col-md-6 col-lg-7 col-xl-8 d-none d-md-block" style="background:rgb(146,109,222); background-size: cover;">

            <div class="d-flex vh-100">
                <div class="p-5 align-self-end">
                    <!-- <img src="/static/logo.png" alt="..."> -->
                    <br><br><br>
                    <font color="white">dreamStack是新一代基于Hyper-v虚拟化技术实现的云计算一体化管理平台，可实现虚拟硬件防火墙、动态内存、显卡云计算
                        等功能，提供了一个简单易用的云计算管理平台。</font>

                </div>
            </div>

        </div>

        <div class="col-md-6 col-lg-5 col-xl-4 align-self-center">
            <div class="p-5">
                <div class="text-center">
                    <a href="index.html"> <img alt="light year admin" src="/static/logo.png"> </a>
                </div>
                <p class="text-center text-muted"><small>身份认证</small></p>



                <div class="form-group">
                    <label for="username">登录名</label>
                    <input type="text" class="form-control" id="loginname" placeholder="请输入您的用户名/邮箱">
                </div>

                <div class="form-group">
                    <label for="password">密码</label>
                    <input type="password" class="form-control" id="password" placeholder="请输入您的密码">
                </div>

                <div class="form-group">
                    <button class="btn btn-block btn-primary" type="submit" id="submit">登录</button>
                </div>
                <!-- </form> -->
                <p class="text-center text-muted mt-3">Copyright © {{date('Y')}} <a href="http://dreamstack.baseyun.cn">baseyun</a>. All right reserved</p>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script src="/static/layer/layer.js"></script>
    <script>
        $(document).ready(function() {
            //login submit even
            $("#submit").click(function() {
                var loginname = $("#loginname").val();
                var password = $("#password").val();
                if (loginname == '' || password == '') {
                    layer.msg('请输入登录名和密码！', {
                        icon: 5
                    });
                    return false;
                }
                load = layer.load(2, {
                    shade: [0.1, '#fff']
                });
                $.ajax({
                    type: "post",
                    url: "/home/api/authentication",
                    data: {
                        loginname,
                        password,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: "json",
                    success: function(response) {
                        layer.close(load);
                        if (response.code == 200) {
                            window.location.href = response.data['callback_url'];
                        } else {
                            if (response.code == 400) {
                                layer.open({
                                    title: '动态口令认证',
                                    content: '后端要求你提供动态口令才能登录！<div class="form-group"><label for="google_code"></label><input type="text" class="form-control" id="google_code" placeholder="动态口令"></div>',
                                    btn: ['确定', '取消'],
                                    yes: function(index) {
                                        var google_code = $("#google_code").val();
                                        if (google_code == '') {
                                            layer.msg('请输入动态口令', {
                                                icon: 5
                                            });
                                        }
                                        layer.close(index);
                                        load = layer.load(2, {
                                            shade: [0.1, '#fff']
                                        });
                                        $.ajax({
                                            type: "post",
                                            url: "/home/api/authentication",
                                            data: {
                                                google_code,
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
                                                }
                                            }
                                        });
                                    }
                                });
                            } else {
                                layer.msg(response.msg, {
                                    icon: 5
                                });
                            }
                        }
                    },
                    error: function() {
                        layer.close(load);
                        layer.msg('网络错误，请稍后再试！', {
                            icon: 5
                        });
                    }
                });
            })
        });
    </script>
</body>

</html>