<div class="form-group">
    <label>标识</label>
    <input type="text" class="form-control" placeholder="{{$row->id}}" readonly>
</div>

<div class="form-group">
    <label>网络名称</label>
    <input type="text" class="form-control" value="{{$row->name}}" id="name">
</div>

<div class="form-group">
    <label>DHCP代理程序</label><br>
    <input type="checkbox" id="dhcp" value="1" @if($row->dhcp == 1) checked @endif>
</div>

<div class="form-group">
    <label>DNS</label>
    <input type="text" class="form-control" value="{{$row->dns}}" id="dns">
</div>


<div class="form-group">
    <label><h3>说明</h3></label><br>
    编辑网络
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    $("#submit").click(function() {
        var name = $("#name").val();
        var dhcp = $("#dhcp").is(':checked')
        var dns = $("#dns").val();
        if (name == '') {
            layer.msg('网络名称不能为空！', {
                icon: 5,
                time: 1000
            });
            return false;
        }
        if (dns == '') {
            layer.msg('DNS不能为空！', {
                icon: 5,
                time: 1000
            });
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/home/api/edit_network",
            data: {
                id: "{{$row->id}}",
                name: name,
                dhcp: dhcp,
                dns: dns,
                _token: "{{csrf_token()}}"
            },
            success: function(data) {
                if (data.code == 200) {
                    layer.msg('编辑成功', {
                        icon: 6,
                        time: 1000
                    });
                    setTimeout(function() {
                        window.location.href = '/home/dashboard/admin/network';
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