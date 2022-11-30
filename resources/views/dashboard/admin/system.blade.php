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
                            <label>网络拓扑可见性</label><br>
                                <input type="checkbox" id="networktop" @if($networktop == 'true') checked @endif>
                        </div>


                        <div class="form-group">
                            <label>系统时区</label>
                            <select class="form-control" id="timezone" value="{{$timezone}}">
                                @foreach(timezones() as $k => $val)
                                <option value="{{$val}}" @if($val==$timezone) selected @endif>{{$k}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 启用mac免密认证 -->
                        <div class="form-group">
                            <label>MAC免密认证</label><br>
                            <input type="checkbox" id="freeoauth" @if($freeoauth == 'enable') checked @endif><br>
                            <code>注意：开启后会获取未登录用户的IP地址，会在arp表中寻找对应的MAC地址，这可能会消耗大量的服务器网络带宽。你可以在“身份管理 -> 用户 -> 操作:MAC免密认证”中配置受信任的MAC地址。</code>
                        </div>

                        <!-- 本地网络接口自动登录 -->
                        <div class="form-group">
                            <label>本地网络接口自动登录</label><br>
                            <input type="checkbox" id="localauto" @if($localauto == 'enable') checked @endif><br>
                            <code>注意：开启后如果使用本地服务器的网络接口进入平台，会自动登录到管理员用户。（该功能需要开启MAC免密认证后才能使用）</code>
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
        $(document).ready(function() {
            $("#submit").click(function(e) {
                e.preventDefault();
                var timezone = $("#timezone").val();
                var networktop = $("#networktop").is(':checked');
                $.ajax({
                    type: "get",
                    url: "/home/api/systemconfig",
                    data: {
                        timezone: timezone,
                        networktop: networktop,
                        freeoauth: $("#freeoauth").is(':checked') ? 'enable' : 'disable',
                        localauto : $("#localauto").is(':checked') ? 'enable' : 'disable'

,

                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.code == 200) {
                            layer.msg('操作成功', {
                                icon: 6,
                                time: 1000
                            });
                        } else {
                            layer.msg(response.msg, {
                                icon: 5,
                                time: 1000
                            });
                        }
                    },
                    error: function(response) {

                        layer.msg('网络错误', {
                            icon: 5,
                            time: 1000
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>