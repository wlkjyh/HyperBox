<link href="/static/css/bootstrap.min.css" rel="stylesheet">
<link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
<link href="/static/js/jquery-tagsinput/jquery.tagsinput.min.css" rel="stylesheet">
<link href="/static/css/style.min.css" rel="stylesheet">
<div class="form-group">
    <input class="form-control js-tags-input" type="text" id="example-tags" name="tags" data-height="100px" placeholder="受信任的MAC地址" value="{{$auth}}">
</div>
<div class="form-group">
    <label>
        <h3>说明</h3>
    </label><br>
    配置受信任的MAC地址，这个用户可以通过认证访问控制台
    <br>
    <h5>输入后直接回车，格式：XX-XX-XX-XX-XX-XX</h5>
</div>

<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script type="text/javascript" src="/static/js/popper.min.js"></script>
<script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jquery-tagsinput/jquery.tagsinput.min.js"></script>
<script type="text/javascript" src="/static/js/main.min.js"></script>

<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>

<script>
    $(document).ready(function () {
        $("#submit").click(function (e) { 
            e.preventDefault();
            var auth = $("#example-tags").val();
            $.ajax({
                type: "post",
                url: "/home/api/configauth",
                data: {
                    auth,
                    id : '{{$row->id}}',
                    _token: '{{csrf_token()}}'
                
                },
                dataType: "json",
                success: function (response) {
                    if (response.code == 200) {
                        layer.msg('配置成功！', {
                            icon: 6
                        });
                        setTimeout(() => {
                            window.location.href = '/home/dashboard/admin/user'
                        }, 500);
                    } else {
                        layer.msg(response.msg, {
                            icon: 5
                        });
                    }
                },
                error:function (param) {
                    return layer.msg('网络错误', {
                        icon: 5
                    });
                  }
            });
        });
    });
</script>