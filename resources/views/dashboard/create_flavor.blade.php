
<div class="form-group">
    <label>规格名称</label>
    <input type="text" class="form-control" id="name" placeholder="">
</div>

<div class="form-group">
    <label>VCPU资源数</label>
    <input type="text" class="form-control" id="vcpu" placeholder="">
</div>

<div class="form-group">
    <label>动态内存</label><br>
    <input type="checkbox" id="type" onchange="setof()">
</div>


<div class="form-group">
    <label id="c">内存资源数量</label>
    <input type="text" class="form-control" id="ram" placeholder="">
</div>

<div class="form-group" id="a" style="display:none">
    <label>最小内存</label>
    <input type="text" class="form-control" id="min" placeholder="">
</div>

<div class="form-group" id="b" style="display:none">
    <label>最大内存</label>
    <input type="text" class="form-control" id="max" placeholder="">
</div>


<div class="form-group">
    <label>T/RX因子</label>
    <input type="text" class="form-control" id="trx" placeholder="单位bit，不限制为0" value="0">
</div>

<div class="form-group">
    <label>共享的</label><br>
    <input type="checkbox" id="share" checked>
</div>

<div class="form-group">
    <label><h3>说明</h3></label><br>
    创建一个实例规格用于创建实例
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    function setof() {
        type = $('#type').is(':checked');
        if (type) {
            $('#a').show();
            $('#b').show();
            $('#c').text('启动内存');
        } else {
            $('#a').hide();
            $('#b').hide();
            $('#c').text('内存资源数量');
        }
    }
    $("#submit").click(function() {
        var name = $("#name").val();
        var vcpu = $("#vcpu").val();
        var ram = $("#ram").val();
        var min = $("#min").val();
        var max = $("#max").val();
        var type = $('#type').is(':checked');
        var trx = $("#trx").val();
        var share = $('#share').is(':checked') ? 1 : 0;
        if (name == '') {
            layer.msg('规格名称不能为空！', {
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
            layer.msg('内存资源数不能为空！', {
                icon: 5,
                time: 1000
            });
            return false;
        }


        if (type) {
            if (min == '') {
                layer.msg('最小内存不能为空！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            if (max == '') {
                layer.msg('最大内存不能为空！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            
        } 
        $.ajax({
            type: "post",
            url: "/home/api/create_flavor",
            data: {
                name: name,
                vcpu: vcpu,
                ram: ram,
                type: type,
                min: min,
                max: max,
                trx: trx,
                share: share,
                _token: "{{csrf_token()}}"

            },
            dataType: "json",
            success: function(response) {
                if (response.code == 200) {
                    window.location.href = '/home/dashboard/flavor';
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