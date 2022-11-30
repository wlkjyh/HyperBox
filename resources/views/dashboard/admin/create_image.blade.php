<div class="form-group">
    <label>映像名称</label>
    <input type="text" class="form-control" id="name" placeholder="">
</div>

<div class="form-group">
    <label>计算主机</label>
    <select class="form-control" id="compute">
        @foreach(\App\Compute::get() as $host)
        <option value="{{ $host->id }}">{{ $host->id }} - {{ $host->hostname }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>镜像位置</label>
    <select class="form-control" id="local" onchange="a()">
        <option value="1">文件位置</option>
        <option value="2">网络位置</option>
    </select>
</div>

<div class="form-group">
    <label id="paths">请输入映像文件位置</label>
    <input type="text" class="form-control" id="path" placeholder="">
    <code id="aaa" style="display:none">必须是一个有效的HTTP/HTTPS网络位置，重定向或网页错误会导致实例无法启动</code>
</div>

<div class="form-group">
    <label>映像格式</label>
    <select class="form-control" id="type">
        <option value="vhdx">VHDX - Hyper-V虚拟硬盘文件</option>
        <option value="vhd">VHD - Hyper-V旧版虚拟硬盘文件</option>
        <!-- <option value="iso">ISO - 光盘映像文件</option> -->
    </select>
</div>


<div class="form-group">
    <label>VCPU规格要求</label>
    <input type="text" class="form-control" id="vcpu" value="0" placeholder="">
    <code>实例启动这个映像的VCPU最低规格要求，0为不限制</code>
</div>

<div class="form-group">
    <label>内存规格要求</label>
    <input type="text" class="form-control" id="ram" value="0" placeholder="">
    <code>实例启动这个映像的内存最低规格要求，0为不限制，单位MB</code>
</div>


<div class="form-group">
    <label><h3>说明</h3></label><br>
    添加一个映像用于启动实例
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    function a() {
        var local = $("#local").val();
        if (local == 1) {
            $("#paths").text('请输入映像文件位置');
            $("#path").attr('placeholder', '');
            $("#aaa").hide();
        } else {
            $("#paths").text('请输入映像网络位置');
            $("#path").attr('placeholder', 'http://');
            $("#aaa").show()
        }
    }
    $(document).ready(function () {
        $("#submit").click(function(){
            var name = $("#name").val();
            var compute = $("#compute").val();
            var path = $("#path").val();
            var vcpu = $("#vcpu").val();
            var ram = $("#ram").val();
            var local = $("#local").val();
            if (name == '') {
                layer.msg('请输入映像名称', {icon: 2, time: 1000});
                return false;
            }
            if (compute == '') {
                layer.msg('请选择计算主机', {icon: 2, time: 1000});
                return false;
            }
            if (local == 1) {
                if (path == '') {
                    layer.msg('请输入映像文件位置', {icon: 2, time: 1000});
                    return false;
                }
            } else {
                if (path == '' || path.indexOf('http') != 0) {
                    layer.msg('请输入有效的映像网络位置', {icon: 2, time: 1000});
                    return false;
                }
            }
            if (vcpu == '') {
                layer.msg('请输入VCPU规格要求', {icon: 2, time: 1000});
                return false;
            }
            if (ram == '') {
                layer.msg('请输入内存规格要求', {icon: 2, time: 1000});
                return false;
            }
            var  type = $("#type").val();
            $.ajax({
                type: "post",
                url: "/home/api/create_image",
                data: {
                    name: name,
                    compute: compute,
                    path: path,
                    vcpu: vcpu,
                    ram: ram,
                    local: local,
                    type: type,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function (response) {
                    if(response.code == 200){
                        layer.msg(response.msg, {icon: 1, time: 1000});
                        setTimeout(() => {
                            window.location.href = '/home/dashboard/admin/image';
                        }, 1000);
                    }else{
                        layer.msg(response.msg, {icon: 2, time: 1000});
                    }
                },
                error : function(response) {
                    layer.msg('网络错误', {icon: 2, time: 1000});
                }
            });


        })
    });
</script>