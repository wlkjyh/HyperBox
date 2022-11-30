
<div class="form-group">
    <label>标识</label>
    <input type="text" class="form-control" placeholder="{{$row->id}}" readonly>
</div>

<div class="form-group">
    <label>规格名称</label>
    <input type="text" class="form-control" placeholder="{{$row->name}}" readonly>
</div>

<div class="form-group">
    <label>VCPU资源数</label>
    <input type="text" class="form-control" id="vcpu" value="{{$row->vcpu}}" placeholder="">
</div>

<div class="form-group">
    <label>@if($row->type == 1) 启动内存 @else 内存资源数量 @endif</label>
    <input type="text" class="form-control" id="ram" value="{{$row->ram}}" placeholder="">
</div>

<div class="form-group" @if($row->type != 1) style="display:none" @endif>
    <label>最小内存</label>
    <input type="text" class="form-control" id="min" value="{{$row->min}}" placeholder="">
</div>

<div class="form-group"  @if($row->type != 1) style="display:none" @endif>
    <label>最大内存</label>
    <input type="text" class="form-control" id="max" value="{{$row->max}}" placeholder="">
</div>



<div class="form-group">
    <label>T/RX因子</label>
    <input type="text" class="form-control" id="trx" placeholder="单位bit，不限制为0" value="{{$row->trx}}">
</div>

<div class="form-group">
    <label>共享的</label><br>
    <input type="checkbox" id="share" @if($row->share == 1) checked @endif>
</div>



<div class="form-group">
    <label><h3>说明</h3></label><br>
    @if($row->type == 1) 不能改变这个是动态内存的事实 @else none @endif
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
        $("#submit").click(function() {
            var vcpu = $("#vcpu").val();
            var ram = $("#ram").val();
            var name = $("#name").val();
            var min = $("#min").val();
            var max = $("#max").val();
            var trx = $("#trx").val();
            var share = $("#share").is(':checked') ? 1 : 0;
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
            if (name == '') {
                layer.msg('规格名称不能为空', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            $.ajax({
                type: "post",
                url: "/home/api/edit_flavor",
                data: {
                    id:'{{$row->id}}',
                    vcpu: vcpu,
                    ram: ram,
                    name: name,
                    min: min,
                    max: max,
                    trx: trx,
                    share: share
                ,_token : "{{csrf_token()}}"
                
                },
                dataType: "json",
                success: function (response) {
                    if(response.code == 200){
                        window.location.href = '/home/dashboard/flavor';
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