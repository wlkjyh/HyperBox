
<div class="form-group">
    <label>卷名称</label>
    <input type="text" class="form-control" id="name" placeholder="">
</div>

<!-- 计算主机 -->
<div class="form-group">
    <label>计算主机</label>
    <select class="form-control" id="host">
        @foreach(\App\Compute::myCompute() as $host)
        <option value="{{$host->id}}">{{$host->id}} - {{$host->hostname}}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>卷大小</label>
    <input type="text" class="form-control" id="size" placeholder="">
    <code>卷大小，单位GB</code>
</div>

<div class="form-group">
    <label><h3>说明</h3></label><br>
    创建一个卷
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    $(document).ready(function() {
        $("#submit").click(function(e) {
            e.preventDefault();
            var name = $("#name").val();
            var compute = $("#host").val();
            var size = $("#size").val();

            if (name == '') {
                return layer.msg('请输入卷名称', {
                    icon: 2,
                    time: 500
                });
            }
            if (compute == '') {
                return layer.msg('请选择计算主机', {
                    icon: 2,
                    time: 500
                });
            }
            if (size == '') {
                return layer.msg('请输入卷大小', {
                    icon: 2,
                    time: 500
                });
            }
            load = layer.load(1, {
                shade: [0.1, '#fff'] //0.1透明度的白色背景
            });

            $.ajax({
                type: "post",
                url: "/home/api/create_volume",
                data: {
                    name: name,
                    compute: compute,
                    size: size,
                    _token: "{{csrf_token()}}"
                },
                dataType: "json",
                success: function(response) {
                    layer.close(load);
                    if (response.code == 200) {
                        layer.msg('创建成功', {
                            icon: 1,
                            time: 1000
                        });
                        setTimeout(function() {
                            window.location.href = '/home/dashboard/volume';
                        }, 1000);
                    } else {
                        layer.msg(response.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                },
                error: function(param) {
                    layer.close(load);
                    layer.msg('网络错误', {
                        icon: 2,
                        time: 500
                    });
                }
            });
        });
    });
</script>