<div class="form-group">
    <label>用户名</label>
    <input type="text" class="form-control" id="username" placeholder="">
</div>

<div class="form-group">
    <label>邮箱</label>
    <!-- <input type="email" class="form-control" id="email" placeholder=""> -->
    <input type="text" class="form-control" id="email" placeholder="">
</div>

<!-- 组 -->
<div class="form-group">
    <label>组</label>
    <select class="form-control" id="group">
        @foreach(\App\Group::get() as $val)
        <option value="{{$val->id}}">{{$val->name}}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>密码</label>
    <input type="password" class="form-control" id="password" placeholder="">
</div>




<div class="form-group">
    <label><h3>说明</h3></label><br>
    创建一个新的用户，用于对计算资源的管理。
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    $("#submit").click(function() {
        var username = $("#username").val();
        var email = $("#email").val();
        var password = $("#password").val();
        var group = $("#group").val();
        if (username == '') {
            layer.msg('用户名不能为空！', {
                icon: 5,
                time: 1000
            });
            return false;
        }
        if (email == '') {
            layer.msg('邮箱不能为空！', {
                icon: 5,
                time: 1000
            });
            return false;
        }
        if (password == '') {
            layer.msg('密码不能为空！', {
                icon: 5,
                time: 1000
            });
            return false;
        }
        if (group == '') {
            layer.msg('组不能为空！', {
                icon: 5,
                time: 1000
            });
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/home/api/create_user",
            data: {
                _token: "{{csrf_token()}}",
                username: username,
                email: email,group,
                password: password
            },
            success: function(data) {
                if (data.code == 200) {
                    layer.msg('创建成功', {
                        icon: 6,
                        time: 1000
                    });
                    setTimeout(function() {
                        window.location.href = '/home/dashboard/admin/user';
                    }, 500);
                } else {
                    layer.msg(data.msg, {
                        icon: 5,
                        time: 1000
                    });
                }
            },
            error: function(data) {
                layer.msg('网络错误', {
                    icon: 5,
                    time: 1000
                });
            }
        });
    })
</script>