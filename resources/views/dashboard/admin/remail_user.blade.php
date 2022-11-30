<?php
$row = \App\Users::where('id', \Illuminate\Support\Facades\Request::get('id'))->first();
if (!$row) return redirect('/dashboard/admin/user');
?>
<div class="form-group">
    <label for="new-password">邮箱</label>
    <input type="text" class="form-control" id="email" value="{{$row->email}}">
</div>
<button id="submit" type="submit" class="btn btn-primary">提交</button>

<script>
    $(document).ready(function() {
        $("#submit").click(function() {
            var email = $("#email").val();
            if (email == '') {
                layer.msg('邮箱不能为空！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            $.ajax({
                type: "get",
                url: "/home/api/remail_user",
                data: {
                    email: email,
                    id: '{{Request::get("id")}}',
                },
                dataType: "json",
                success: function(response) {
                    if (response.code == 200) {
                        layer.msg('邮箱设置成功', {
                            icon: 6,
                            time: 1000
                        }, function() {
                            window.location.href = '/home/dashboard/admin/user';
                        });
                        setTimeout(() => {

                            window.location.href = '/home/dashboard/admin/user';
                        }, 500);
                    }else{
                        layer.msg(response.msg, {
                            icon: 5,
                            time: 1000
                        });
                    }
                },
                error: function(response) {
                    layer.msg(response.responseJSON.message, {
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