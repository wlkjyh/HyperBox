<div class="form-group">
    <label>标识</label>
    <input type="text" class="form-control" placeholder="{{$row->id}}" readonly>
</div>

<div class="form-group">
    <label>镜像名称</label>
    <input type="text" class="form-control" value="{{$row->name}}" id="name">
</div>


<div class="form-group">
    <label>VCPU规格要求</label>
    <input type="text" class="form-control" value="{{$row->vcpu}}" id="vcpu">
</div>

<div class="form-group">
    <label>内存规格要求</label>
    <input type="text" class="form-control" value="{{$row->ram}}" id="ram">
</div>

<div class="form-group">
    <label><h3>说明</h3></label><br>
    编辑映像
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    $("#submit").click(function() {
        var name = $("#name").val();
        var vcpu = $("#vcpu").val();
        var ram = $("#ram").val();
        if (name == '') {
            layer.msg('请输入映像名称', {icon: 2, time: 1000});
            return false;
        }
        if (vcpu == '') {
            layer.msg('请输入VCPU规格要求', {icon: 2, time: 1000});
            return false;
        }
        if (ram == '') {
            layer.msg('请输入内存规格要求', {icon: 2, time: 1000});
            return false;
        }

        $.ajax({
            type: "post",
            url: "/home/api/edit_image",
            data: {
                id : '{{$row->id}}',
                name : name,
                vcpu : vcpu,
                ram : ram,
                _token : "{{csrf_token()}}"
            },
            dataType: "json",
            success: function (response) {
                if(response.code == 200){
                    layer.msg(response.msg, {icon: 1, time: 1000});
                    setTimeout(function(){
                        window.location.href = '/home/dashboard/admin/image';
                    },500);
                }else{
                    layer.msg(response.msg, {icon: 2, time: 1000});
                }
            },
            error : function (param) {
                layer.msg('网络错误', {icon: 2, time: 1000});
              }
        });
    })
</script>