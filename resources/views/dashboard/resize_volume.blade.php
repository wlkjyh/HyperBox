
<div class="form-group">
    <label>标识</label>
    <input type="text" class="form-control" placeholder="{{$row->id}}" readonly>
</div>

<div class="form-group">
    <label>卷大小</label>
    <input type="text" class="form-control" value="{{$row->size}}" id="size">
    <code>只能比当前大，不能小于当前大小</code>
</div>


<div class="form-group">
    <label><h3>说明</h3></label><br>
    调整卷大小
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    $("#submit").click(function() {
        var size = $("#size").val();
        if (size == '') {
            layer.msg('卷大小不能为空', {
                icon: 5,
                time: 1000
            });
            return false;
        }
        load = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        $.ajax({
            type: "POST",
            url: "/home/api/resize_volume",
            data: {
                id: "{{$row->id}}",
                size: size,
                _token: "{{csrf_token()}}"
            },
            success: function(data) {
                layer.close(load);
                if (data.code == 200) {
                    layer.msg('编辑成功', {
                        icon: 6,
                        time: 1000
                    });
                    setTimeout(function() {
                        window.location.href = '/home/dashboard/volume';
                    }, 500);
                } else {
                    layer.msg(data.msg, {
                        icon: 5,
                        time: 1000
                    });
                }
            },
            error : function(data) {
                layer.close(load);
                layer.msg('网络错误', {
                    icon: 5,
                    time: 1000
                });
            }
        });
    })
</script>