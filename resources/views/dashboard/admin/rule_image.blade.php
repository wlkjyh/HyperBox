<div class="form-group">
    <label for="exampleFormControlSelect2">可访问的用户</label>
    <select multiple class="form-control" id="rule">
        <option value="ALL" @if($row->rule == 'ALL') selected @endif>任何人</option>
        @php
            if($row->rule == 'ALL') $arr = [];
            else $arr = json_decode($row->rule,true);
        @endphp

        @foreach(\App\Users::get() as $val)
            <option value="{{$val->id}}" @if(in_array($val->id,$arr)) selected @endif>{{$val->id}} - {{$val->username}}</option>
        @endforeach

    </select>
    <code>按住Ctrl键可进行多选</code>
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">确定</button>
<script>
    $(function() {
        $("#submit").click(function (e) { 
            // alert($("#rule").val())
            rule = $("#rule").val();
            if(rule == '') {
                layer.msg('请选择可访问的用户！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            // 如果包含ALL，则只设置ALL
            if(rule.indexOf('ALL') != -1) {
                rule = 'ALL';
            }else {
                rule = JSON.stringify(rule);
            }
            $.ajax({
                type: "POST",
                url: "/home/api/rule_image",
                data: {
                    id: "{{$row->id}}",
                    rule: rule,
                    _token: "{{csrf_token()}}"
                },
                success: function(data) {
                    if (data.code == 200) {
                        layer.msg('编辑成功', {
                            icon: 6,
                            time: 1000
                        });
                        setTimeout(function() {
                            window.location.href = '/home/dashboard/admin/image';
                        }, 1000);
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
            
        });

    });
</script>