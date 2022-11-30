<div class="form-group">
    <label>主机名</label>
    <input type="text" class="form-control" id="hostname" placeholder="">
</div>


<div class="form-group">
    <label>FreeRDP远程地址</label>
    <!-- <input type="text" class="form-control" id="console" placeholder="IP:端口"> -->
    <textarea id="console" class="form-control" rows="10" placeholder="一行一个，IP:端口"></textarea>
</div>

<div class="form-group">
    <label>VCPU资源数</label>
    <input type="text" class="form-control" id="vcpu" placeholder="">
</div>

<div class="form-group">
    <label>内存资源数量</label>
    <input type="text" class="form-control" id="ram" placeholder="">
</div>

<div class="form-group">
    <label>磁盘资源数量</label>
    <input type="text" class="form-control" id="disk" placeholder="">
</div>


<div class="form-group">
    <label><h3>说明</h3></label><br>
    将计算主机加入至资源池中可用于对实例的创建
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
        $("#submit").click(function() {
            var hostname = $("#hostname").val();
            var vcpu = $("#vcpu").val();
            var ram = $("#ram").val();
            var disk = $("#disk").val();
            var console = $("#console").val()
            if (hostname == '') {
                layer.msg('主机名不能为空！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            if (console == '') {
                layer.msg('FreeRDP远程地址', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }

            if (vcpu == '') {
                layer.msg('VCPU资源数不能为空！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            if (ram == '') {
                layer.msg('内存资源数量不能为空！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            if (disk == '') {
                layer.msg('磁盘资源数量不能为空！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            $.ajax({
                type: "post",
                url: "/home/api/create_compute",
                data: {
                    hostname: hostname,
                    vcpu: vcpu,
                    ram: ram,
                    disk: disk,console:console
                ,_token : "{{csrf_token()}}"
                
                },
                dataType: "json",
                success: function (response) {
                    if(response.code == 200){
                        window.location.href="{{Request::get('next','/home/dashboard/admin/compute')}}";
                    }else{
                        layer.msg(response.msg, {
                            icon: 5,
                            time: 1000
                        });
                    }
                },
                error: function () {
                    layer.msg('提交失败！', {
                        icon: 5,
                        time: 1000
                    });
                }
            });
        })
</script>