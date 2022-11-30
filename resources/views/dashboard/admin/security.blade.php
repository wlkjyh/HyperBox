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
    <div class="container-fluid p-t-15">

        <div class="row">

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <div class="form-group">
                            <label>BasicAuth认证</label><br>
                            <input type="checkbox" id="basicauth" @if(getconfig('basicauth') == '1') checked @endif>
                            <code>
                                启动BasicAuth认证后，可以防止被扫描到/home/dashboard/authentication，也为管理员登录增加了一道防护。
                            </code>
                        </div>

                        <h3>BasicAuth选项</h3>
                        <div class="form-group">
                            <label for="">认证用户名</label>
                            <input type="text" class="form-control" id="authuser" value="{{getconfig('authuser')}}">
                            <code>如果不启用BasicAuth，可以不填写</code>
                        </div>

                        <div class="form-group">
                            <label for="">认证密码</label>
                            <input type="text" class="form-control" id="authpass" placeholder="@if(getconfig('authpass') != '') 受到保护的敏感数据将不会显示 @endif">
                            <code>如果不启用BasicAuth，可以不填写</code>

                        </div>
                        <h3>Google Authenticator</h3>

                        <div class="form-group">
                            <label>动态身份认证口令</label><br>
                            <input type="checkbox" id="googleauth"  @if(getconfig('googleauthsecret') == '') onclick="setGoogleAuth()" @endif @if(getconfig('googleauth') == '1') checked @endif>
                            <code>
                                使用Google Authenticator对管理员登录进行动态口令身份认证。<br>@if(getconfig('googleauthsecret') == '') 开启后会弹出一个窗口，一定要扫码绑定！！ @else <a href="javascript:;" onclick="setGoogleAuth()">重新绑定</a>@endif
                            </code>
                        </div>




                        <button id="submit" type="submit" class="btn btn-primary">提交</button>
                        <!-- </form> -->

                    </div>
                </div>
            </div>

        </div>

    </div>
    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/popper.min.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/main.min.js"></script>
    <script src="/static/layer/layer.js"></script>
    <script>
        function setGoogleAuth(){
            if ($('#googleauth').is(':checked')) {
                // layer.open
                // 打开一个iframe层
                layer.open({
                    type: 2,
                    title: '绑定Google Authenticator',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['500px', '400px'],
                    content: '/home/dashboard/admin/bindGooleAuth' //iframe的url
                });
            }
        }
        $(document).ready(function () {
            $("#submit").click(function (e) { 
                e.preventDefault();
                var basesicauth = $("#basicauth").is(':checked') ? 1 : 0;
                var authuser = $("#authuser").val();
                var authpass = $("#authpass").val();
                var googleauth = $("#googleauth").is(':checked') ? 1 : 0;
                $.ajax({
                    type: "post",
                    url: "/home/api/security",
                    data: {
                        _token : "{{csrf_token()}}",
                        basicauth: basesicauth,
                        authuser: authuser,
                        authpass: authpass,
                        googleauth: googleauth
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.code == 200){
                            layer.msg('操作成功', {icon: 1, time: 2000}, function () {
                                location.reload();
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        }else{
                            layer.msg(response.msg, {icon: 2, time: 2000});
                        }

                    },
                    error : function(response){
                        layer.msg('网络错误', {icon: 2,time: 2000});
                        return false;
                    }
                });
                
            });
        });
    </script>


</body>

</html>