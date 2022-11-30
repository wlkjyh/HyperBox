<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

    <link href="/static/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css" rel="stylesheet">
</head>

<body>
    @if(Request::header('type') != 'ajax-modal')
    <div class="container-fluid p-t-15">

        <div class="row">

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @endif
                        <!-- <form method="" action="#!" class="site-form"> -->
                        <div class="form-group">
                            <label for="old-password">旧密码</label>
                            <input type="password" class="form-control" name="oldpwd" id="old-password" >
                        </div>
                        <div class="form-group">
                            <label for="new-password">新密码</label>
                            <input type="password" class="form-control" name="newpwd" id="new-password" >
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">确认新密码</label>
                            <input type="password" class="form-control" name="confirmpwd" id="confirm-password">
                        </div>
                        <button id="submit" type="submit" class="btn btn-primary">提交</button>
                        <!-- </form> -->

                        
    @if(Request::header('type') != 'ajax-modal')
                    </div>
                </div>
            </div>

        </div>

    </div>
    @endif
    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/popper.min.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/main.min.js"></script>
    <script src="/static/layer/layer.js"></script>
    <script>
        $(document).ready(function() {
            $("#submit").click(function() {
                var oldpwd = $("#old-password").val();
                var newpwd = $("#new-password").val();
                var confirmpwd = $("#confirm-password").val();
                if (oldpwd == '') {
                    layer.msg('旧密码不能为空', {
                        icon: 5,
                        time: 1000
                    });
                    return false;
                }
                if (newpwd == '') {
                    layer.msg('新密码不能为空', {
                        icon: 5,
                        time: 1000
                    });
                    return false;
                }
                if (confirmpwd == '') {
                    layer.msg('确认密码不能为空', {
                        icon: 5,
                        time: 1000
                    });
                    return false;
                }
                if (newpwd != confirmpwd) {
                    layer.msg('两次密码不一致', {
                        icon: 5,
                        time: 1000
                    });
                    return false;
                }
                load = layer.load(2, {
                    shade: [0.3, '#000']
                });
                $.ajax({
                    type: "post",
                    url: "/home/api/changepassword",
                    data: {
                        oldpwd: oldpwd,
                        newpwd: newpwd,
                        confirmpwd: confirmpwd,
                        _token: "{{csrf_token()}}"
                    },
                    dataType: "json",
                    success: function(response) {
                        layer.close(load);
                        if (response.code == 200) {
                            window.location.href = '/acloud';
                        } else {
                            layer.msg(response.msg, {
                                icon: 5,
                                time: 1000
                            });
                        }
                    },
                    error: function() {
                        layer.close(load);
                        layer.msg('网络错误', {
                            icon: 5,
                            time: 1000
                        });
                    }
                });
            })
        });
    </script>
</body>

</html>