<!-- <div class="alert alert-info" role="alert">备份会将当前主硬盘完全复制一次，所以会先强制关闭电源后再复制</div> -->

<div class="form-group">
    <label for="old-password">备份名称</label>
    <input type="text" class="form-control" id="name" placeholder="请输入备份名称">
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>

<script>
    $(document).ready(function() {

        $("#submit").click(function(e) {
            e.preventDefault();
            var name = $("#name").val()
            if (name == '') {
                layer.msg('备份名称不能为空', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            load = layer.load();
            $.ajax({
                type: "get",
                url: "/home/api/backinstance",
                data: {
                    name,
                    id: '{{$row->id}}',
                    _token: '{{csrf_token()}}'
                },
                dataType: "json",
                success: function(response) {
                    layer.close(load)
                    if (response.code == 200) {
                        layer.msg('备份成功', {
                            icon: 6,
                            time: 1000
                        });
                        setTimeout(() => {
                            window.location.href = "/home/dashboard/instance"
                        }, 1000);
                    } else {
                        layer.msg(response.msg, {
                            icon: 5,
                            time: 1000
                        });
                    }
                },
                error: function() {
                    layer.close(load)
                    layer.msg('网络错误', {
                        icon: 5,
                        time: 1000
                    });
                }
            });

        });
    });
</script>