<div class="form-group">
    <label>路由器名称</label>
    <input type="text" class="form-control" id="name" placeholder="">
</div>

<div class="form-group">
    <label>网络</label>
    <select class="form-control" id="network">
        @foreach(\App\network::get() as $host)
        <option value="{{ $host->id }}">{{ $host->id }} - {{ $host->name }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>
        <h3>说明</h3>
    </label><br>
    创建一个路由器可连接多个逻辑上分开的网络
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    $(document).ready(function() {
        $("#submit").click(function(e) {
            e.preventDefault();
            var name = $("#name").val();
            var network = $("#network").val();
            if (name == '') {
                layer.msg('请输入路由器名称', {
                    icon: 2,
                    time: 1000
                });
                return;
            }
            if (network == '') {
                layer.msg('请选择网络', {
                    icon: 2,
                    time: 1000
                });
                return;
            }
            var load = layer.msg('正在进行', {
                icon: 16,
                shade: 0.01
            });
            $.ajax({
                type: "post",
                url: "/home/api/create_route",
                data: {
                    _token: "{{ csrf_token() }}",
                    name,
                    network
                },
                dataType: "json",
                success: function(response) {
                    layer.close(load)
                    if (response.code == 200) {
                        layer.msg('创建成功', {
                            icon: 6,
                            time: 1000
                        });
                        setTimeout(() => {
                            window.location.href = "/home/dashboard/admin/route"
                        }, 500);
                    } else {
                        layer.msg(response.msg, {
                            icon: 5,
                            time: 1000
                        });
                    }
                },
                error: function(param) {
                    layer.close(load);
                    layer.msg('网络错误', {
                        icon: 2,
                        time: 1000
                    });
                }
            });
        });
    });
</script>