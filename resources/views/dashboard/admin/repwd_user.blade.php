
                        <div class="form-group">
                            <label for="new-password">新密码</label>
                            <input type="password" class="form-control" name="newpwd" id="new-password" placeholder="输入新的密码">
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">确认新密码</label>
                            <input type="password" class="form-control" name="confirmpwd" id="confirm-password" placeholder="请确认新密码">
                        </div>
                        <button id="submit" type="submit" class="btn btn-primary">提交</button>
 
    <script>
        $(document).ready(function() {
            $("#submit").click(function() {
                var newpwd = $("#new-password").val();
                var confirmpwd = $("#confirm-password").val();
                if (newpwd == '') {
                    layer.msg('新密码不能为空！', {
                        icon: 5,
                        time: 1000
                    });
                    return false;
                }
                if (confirmpwd == '') {
                    layer.msg('确认密码不能为空！', {
                        icon: 5,
                        time: 1000
                    });
                    return false;
                }
                if (newpwd != confirmpwd) {
                    layer.msg('两次密码不一致！', {
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
                    url: "/home/api/repwd_user",
                    data: {
                        'id': '{{Request::get("id")}}',
                        newpwd: newpwd,
                        confirmpwd: confirmpwd,
                        _token: "{{csrf_token()}}"
                    },
                    dataType: "json",
                    success: function(response) {
                        layer.close(load);
                        if (response.code == 200) {
                            layer.msg('密码重置成功', {
                                icon: 6,
                                time: 1000
                            }, function() {

                            });
                            setTimeout(() => {
                                window.location.href = "/home/dashboard/admin/user";
                            }, 500);
                        } else {
                            layer.msg(response.msg, {
                                icon: 5,
                                time: 1000
                            });
                        }
                    },
                    error: function() {
                        layer.close(load);
                        layer.msg('网络错误！', {
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