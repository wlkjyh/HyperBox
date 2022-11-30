
<div class="form-group">
    <label>标识</label>
    <input type="text" class="form-control" placeholder="{{$row->id}}" readonly>
</div>

<div class="form-group">
    <label>卷名称</label>
    <input type="text" class="form-control" value="{{$row->name}}" id="name">
</div>


<div class="form-group">
    <label><h3>说明</h3></label><br>
    编辑卷
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    $("#submit").click(function() {
        var name = $("#name").val();
        if (name == '') {
            layer.msg('卷名称不能为空', {
                icon: 5,
                time: 1000
            });
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/home/api/edit_volume",
            data: {
                id: "{{$row->id}}",
                name: name,
                _token: "{{csrf_token()}}"
            },
            success: function(data) {
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
                layer.msg('网络错误', {
                    icon: 5,
                    time: 1000
                });
            }
        });
    })
</script>