
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <link href="/static/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="/static/js/jquery-confirm/jquery-confirm.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid p-t-15">

        <!-- <div class="row"> -->
        <div class="col-lg-12">
            <div class="alert alert-info" role="alert">仅管理员可以创建映像文件</div>
            <div class="card">
                



                <div class="card-body">

                <div class="">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>标识</th>
                                    <th>计算主机</th>
                                    <th>映像名称</th>
                                    <th>映像格式</th>
                                    <th>VCPU规格要求</th>
                                    <th>内存规格要求</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $val)
                                <tr>
                                    <td>{{$val->id}}</td>
                                    <td>{{\App\Compute::getRow($val->compute,'hostname')}}</td>
                                    <td>{{$val->name}}</td>
                                    <td>{{$val->type}}</td>
                                    <td>{{$val->vcpu}}</td>
                                    <td>{{$val->ram}} MB</td>
                                    <td>@if($val->state == 1) 无状态 @elseif($val->state == 2) <img src="/static/load.gif" width=20px height=20px>正在下载 @else 错误 @endif</td>
                                    @if($val->state != 1)
                                    <script>
                                        setInterval(() => {
                                            $.ajax({
                                                type: "get",
                                                url: "/home/api/getimagestatus?id={{$val->id}}",
                                                // data: "",
                                                dataType: "json",
                                                success: function(response) {
                                                    if (response.code == 200) window.location.reload();
                                                }
                                            });
                                        }, 1000);
                                    </script>
                                    @endif
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle ajax-modal" link="/home/dashboard/create_instance" title="启动实例" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                启动实例
                                            </button>
                                            <ul class="dropdown-menu">
                                                </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <!-- </div> -->

    </div>
    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/popper.min.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/lyear-loading.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap-notify.min.js"></script>
    <script type="text/javascript" src="/static/js/jquery-confirm/jquery-confirm.min.js"></script>
    <script type="text/javascript" src="/static/js/main.min.js"></script>
    <script src="/static/layer/layer.js"></script>
    <script src="/static/main.js"></script>


</body>

</html>