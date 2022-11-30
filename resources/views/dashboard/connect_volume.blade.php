
<div class="form-group">
    <label for="exampleFormControlSelect2">连接至</label>
    <select class="form-control" id="instance">

        @foreach($myInstance as $val)
        <option value="{{$val->id}}">{{$val->id}} - {{$val->name}}</option>
        @endforeach

    </select>
    <code>选择一个实例，将卷连接至这个实例中</code>
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">确定</button>
<script>
    $(function() {
        $("#submit").click(function(e) {
            var instance = $("#instance").val();
            if (instance == '') {
                layer.msg('请选择一个实例！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            load = layer.load(1, {
                shade: [0.1, '#fff']
            });
            $.ajax({
                type: "get",
                url: "/home/api/connect_volume",
                data: {
                    id: "{{$row->id}}",
                    instance: instance,

                },
                dataType: "json",
                success: function(response) {
                    layer.close(load);
                    if (response.code == 200) {
                        layer.msg('操作成功', {
                            icon: 6,
                            time: 1000
                        });
                        setTimeout(function() {
                            window.location.href = '/home/dashboard/volume';
                        }, 500);
                    } else {
                        layer.msg(response.msg, {
                            icon: 5,
                            time: 1000
                        });
                    }
                },
                error: function(response) {
                    layer.close(load);
                    layer.msg('网络错误', {
                        icon: 5,
                        time: 1000
                    });
                }
            });

        });

    });
</script>