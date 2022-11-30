
<div class="alert alert-primary" role="alert">如果实例处于开机状态，提交事务后会先将实例关机然后再修改</div>
<button type="button" class="btn btn-default btn-xs disabled" style="outline: none;" id="shang">上移</button>&nbsp;<button type="button" style="outline: none;" class="btn btn-default btn-xs disabled" id="xia">下移</button><br><br>
<ul class="list-group">
    @foreach($boot as $key => $val)
    <li class="list-group-item" id="f{{$key + 1}}">{{$val}}</li>
    @endforeach
</ul>

<br><br>

<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
    $(function() {
        $("#submit").click(function() {
            var f1 = $("#f1").text();
            var f2 = $("#f2").text();
            var f3 = $("#f3").text();
            list = [f1, f2, f3];
            load = layer.load(1, {
                shade: [0.1, '#fff'] //0.1透明度的白色背景
            });
            // alert(list)
            $.ajax({
                type: "post",
                url: "/home/api/boot_instance",
                data: {
                    id: '{{$id}}',
                    list: list,
                    _token: "{{csrf_token()}}"
                },
                dataType: "json",
                success: function(response) {
                    layer.close(load);
                    if (response.code == 200) {
                        layer.msg('操作成功', {
                            icon: 1,
                            time: 1000
                        });
                        setTimeout(function() {
                            window.location.href = '/home/dashboard/console/' + '{{$id}}';
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
                        icon: 5,
                        time: 1000
                    });
                }
            });
        })

        // 把以前dreamStack的偷来了哈哈哈哈哈
        function setliclick(f) {
            click = f;
            $("#shang").attr('class', 'btn btn-default btn-xs')
            $("#xia").attr('class', 'btn btn-default btn-xs')
            if (f == 'f1') {
                $("#shang").attr("class", "btn btn-default btn-xs disabled")
                $("#xia").attr('class', 'btn btn-default btn-xs');
                $("#f1").attr("style", "background:#0078D7;")
                $("#f2").attr("style", "");
                $("#f3").attr("style", "");
            }
            if (f == 'f2') {
                $("#f2").attr("style", "background:#0078D7;")
                $("#f1").attr("style", "");
                $("#f3").attr("style", "");
            }
            if (f == 'f3') {
                $("#xia").attr("class", "btn btn-default btn-xs disabled")
                $("#shang").attr('class', 'btn btn-default btn-xs');
                $("#f3").attr("style", "background:#0078D7;")
                $("#f2").attr("style", "");
                $("#f1").attr("style", "");
            }
        }

        click = 0;
        $("#f1").click(function() {
            setliclick('f1')
        })
        $("#f2").click(function() {
            setliclick('f2')
        })
        $("#f3").click(function() {
            setliclick('f3')
        })

        function setliclick(f) {
            click = f;
            $("#shang").attr('class', 'btn btn-default btn-xs')
            $("#xia").attr('class', 'btn btn-default btn-xs')
            if (f == 'f1') {
                $("#shang").attr("class", "btn btn-default btn-xs disabled")
                $("#xia").attr('class', 'btn btn-default btn-xs');
                $("#f1").attr("style", "background:#0078D7;")
                $("#f2").attr("style", "");
                $("#f3").attr("style", "");
            }
            if (f == 'f2') {
                $("#f2").attr("style", "background:#0078D7;")
                $("#f1").attr("style", "");
                $("#f3").attr("style", "");
            }
            if (f == 'f3') {
                $("#xia").attr("class", "btn btn-default btn-xs disabled")
                $("#shang").attr('class', 'btn btn-default btn-xs');
                $("#f3").attr("style", "background:#0078D7;")
                $("#f2").attr("style", "");
                $("#f1").attr("style", "");
            }
        }

        $("#shang").click(function() {
            // alert(click)
            if (click == 0) return false;
            //开始移动
            var f1 = $("#f1").html();
            var f2 = $("#f2").html();
            var f3 = $("#f3").html();
            if (click == 'f3') {
                $("#shang").attr('class', 'btn btn-default btn-xs');
                $("#xia").attr('class', 'btn btn-default btn-xs');
                click = 'f2'
                $("#f2").html(f3);
                $("#f3").html(f2);
                $("#f2").attr("style", "background:#0078D7;")
                $("#f3").attr("style", "");
                return true
            }
            if (click == 'f2') {
                $("#shang").attr("class", "btn btn-default btn-xs disabled")
                $("#xia").attr('class', 'btn btn-default btn-xs');
                click = 'f1'
                $("#f1").html(f2);
                $("#f2").html(f1);
                $("#f1").attr("style", "background:#0078D7;")
                $("#f2").attr("style", "");
                return true
            }
        })

        $("#xia").click(function() {
            if (click == 0) return false;
            //开始移动
            var f1 = $("#f1").html();
            var f2 = $("#f2").html();
            var f3 = $("#f3").html();
            if (click == 'f1') {
                click = 'f2'
                $("#shang").attr('class', 'btn btn-default btn-xs');
                $("#xia").attr('class', 'btn btn-default btn-xs');
                $("#f2").html(f1);
                $("#f1").html(f2);
                $("#f2").attr("style", "background:#0078D7;")
                $("#f1").attr("style", "");
                return true
            }
            if (click == 'f2') {
                $("#xia").attr("class", "btn btn-default btn-xs disabled")
                $("#shang").attr('class', 'btn btn-default btn-xs');
                click = 'f3'
                $("#f3").html(f2);
                $("#f2").html(f3);
                $("#f3").attr("style", "background:#0078D7;")
                $("#f2").attr("style", "");
                return true
            }
        })
    })
</script>