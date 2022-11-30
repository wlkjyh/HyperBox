<div class="alert alert-danger" role="alert" id="err" style="display:none"></div>

<div class="form-group">
    <label>目的地址</label>
    <input type="text" class="form-control" id="to" placeholder="" onchange="nextmask()">
</div>

<!-- 子网掩码 -->
<div class="form-group">
    <label>子网掩码</label>
    <input type="text" class="form-control" id="mask" placeholder="">
</div>

<!-- 下一跳地址 -->
<div class="form-group">
    <label>下一跳地址</label>
    <input type="text" class="form-control" id="next" placeholder="">
</div>

<div class="form-group">
    <label>
        <h3>说明</h3>
    </label><br>
    添加静态路由
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>


<script>
    function nextmask() {
        var to = $("#to").val();
        // 判断是不是有效的Ipv4
        var reg = /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/;
        // 如果是就计算子网掩码，不是就清空
        if (reg.test(to)) {
            var ip = to.split(".");
            var mask = "";
            for (var i = 0; i < 4; i++) {
                if (ip[i] == "0") {
                    mask += "0.";
                } else {
                    mask += "255.";
                }
            }
            mask = mask.substring(0, mask.length - 1);
            $("#mask").val(mask);
        } else {
            $("#mask").val("");
            $("#to").val("");
            // 将焦点回到to
            $("#to").focus();
            $("#err").html("错误：目的地址不是有效的IPV4");
            // show 
            $("#err").show();
        }

    }


    $(document).ready(function() {
        $("#submit").click(function(e) {
            e.preventDefault();
            var to = $("#to").val();
            var masks = $("#mask").val();
            var next = $("#next").val();
            if (to == '') {
                $("#err").html("错误：目的地址不能为空");
                $("#err").show();
                return false;
            }
            if (mask == '') {
                $("#err").html("错误：子网掩码不能为空");
                $("#err").show();
                return false;
            }
            if (next == '') {
                $("#err").html("错误：下一跳地址不能为空");
                $("#err").show();
                return false;
            }

            // 判断是不是有效的Ipv4
            var reg = /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/;
            if (reg.test(to)) {
                


            } else {
                $("#err").html("错误：目的地址不是有效的IPV4");
                $("#err").show();
                return false;
            }

        });
    });
</script>