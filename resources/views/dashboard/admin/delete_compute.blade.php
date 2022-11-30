你确定要删除“{{$row->hostname}}”吗？删除后计算主机下的实例、卷、安全组、网络会被删除记录，并不会删除计算主机上的资源，请手动删除相关的资源。<br><br>
<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">删除</button>
<script>
    $(document).ready(function() {
        $("#submit").click(function(e) {
            e.preventDefault();
            $.ajax({
                type: "get",
                url: "/home/api/delete_compute?id={{$row->id}}",
                // data: "data",
                dataType: "json",
                success: function(response) {
                    if (response.code == 200) {
                        window.location.href = '/home/dashboard/admin/compute';
                    } else {
                        layer.msg(response.msg, {
                            icon: 5,
                            time: 1000
                        });
                    }
                },
                error: function(param) {
                    layer.msg('提交失败！', {
                        icon: 5,
                        time: 1000
                    });
                }
            });
        });
    });
</script>