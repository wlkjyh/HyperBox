<?php
$row = \App\Group::where('id', Request::get('id'))->first();
if (!$row) {
    return redirect('/home/dashboard/admin/group');
}
?>
<div class="form-group">
    <label>标识</label>
    <input type="text" class="form-control" placeholder="{{$row->id}}" readonly>
</div>

<div class="form-group">
    <label>组名称</label>
    <input type="text" class="form-control" value="{{$row->name}}" id="name">
</div>
<div class="form-group">
    <label>描述</label>
    <textarea class="form-control" rows="3" id="description" style="resize:none">{{$row->description}}</textarea>
</div>


<div class="form-group">
    <label><h3>说明</h3></label><br>
    编辑组
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    $("#submit").click(function() {
        var name = $("#name").val();
        var description = $("#description").val();
        if (description == '') description = '-';
        if (name == '') {
            layer.msg('组名称不能为空', {
                icon: 5,
                time: 1000
            });
            return false;
        }

        $.ajax({
            type: "post",
            url: "/home/api/edit_group",
            data: {
                id: '{{$row->id}}',
                name,description,
                _token: "{{csrf_token()}}"

            },
            dataType: "json",
            success: function(response) {
                if (response.code == 200) {
                    window.location.href = '/home/dashboard/admin/group';
                } else {
                    layer.msg(response.msg, {
                        icon: 5,
                        time: 1000
                    });
                }
            },
            error: function() {
                layer.msg('提交失败！', {
                    icon: 5,
                    time: 1000
                });
            }
        });
    })
</script>