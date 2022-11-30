<div class="form-group">
    <label>CSV文件</label><br>
    <input type="file"  id="csv" accept=".csv">

</div>

<div class="form-group">
    <label><h3>说明</h3></label><br>
    批量导入CSV用户信息(文件第一行请留空)<br>
    格式: 用户名,密码,邮箱,组名称(如果不划分组则填-)
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>
<script>
        $("#submit").click(function() {
            var csv = $("#csv").val();
            if (csv == '') {
                layer.msg('CSV文件不能为空！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            // 判断文件类型
            var fileName = csv.split(".")[1];
            if (fileName != 'csv') {
                layer.msg('文件类型不正确！', {
                    icon: 5,
                    time: 1000
                });
                return false;
            }
            // 上传文件
            var formData = new FormData();
            formData.append('csv', $('#csv')[0].files[0]);
            // _token
            formData.append('_token', '{{csrf_token()}}');
            $.ajax({
                url: "/home/api/import_user",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.code == 200) {
                        layer.msg('导入成功', {
                            icon: 6,
                            time: 2000
                        });
                        setTimeout(function() {
                            window.location.href = '/home/dashboard/admin/user';
                        }, 500);
                    } else {
                        layer.msg(data.msg, {
                            icon: 5,
                            time: 2000
                        });
                    }
                },
                error: function(data) {
                    layer.msg('服务器错误！', {
                        icon: 5,
                        time: 2000
                    });
                }
            });
        })
</script>