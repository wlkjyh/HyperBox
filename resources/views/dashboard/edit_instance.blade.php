<?php
if (userrow('username') == 'admin') {
    $row = \App\Instance::where('id', \Illuminate\Support\Facades\Request::get('id'))->first();
} else {
    $row = \App\Instance::where('id', \Illuminate\Support\Facades\Request::get('id'))->where('userid',userrow('id'))->first();
}
if (!$row) return redirect('/home/dashboard/instance');
?>
<div class="form-group">
    <label for="new-password">标识</label>
    <input type="text" class="form-control" placeholder="{{$row->id}}" readonly>
</div>

<div class="form-group">
    <label for="new-password">实例名称</label>
    <input type="text" class="form-control" id="name" value="{{$row->name}}">
</div>
<button id="submit" type="submit" class="btn btn-primary">提交</button>

<script>
    $(document).ready(function() {
        $("#submit").click(function() {
            var name = $("#name").val();
            if (name == '') {
                layer.msg('实例名称不能为空！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            $.ajax({
                type: "post",
                url: "/home/api/edit_instance",
                data: {
                    name: name,
                    id: '{{Request::get("id")}}',
                    _token: '{{csrf_token()}}'
                },
                dataType: "json",
                success: function(response) {
                    if (response.code == 200) {
                        layer.msg('操作成功', {
                            icon: 6,
                            time: 1000
                        }, function() {
                            window.location.href = '/home/dashboard/instance';
                        });
                        setTimeout(() => {

                            window.location.href = '{{Request::get("next","/home/dashboard/instance")}}';
                        }, 500);
                    } else {
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