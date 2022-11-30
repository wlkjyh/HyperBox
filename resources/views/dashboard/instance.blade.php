
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

    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script src="/static/coco-message.min.js"></script>
</head>

<body>
    <div class="container-fluid p-t-15">

        <!-- <div class="row"> -->
        <div class="col-lg-12">
            <div class="alert alert-info" role="alert">实例数量：<b>{{count($rows)}}</b>台，其中正在运行：<b>@if(userrow('username') == 'admin') {{count(App\Instance::where('state',1)->get())}} @else {{count(App\Instance::where('state',1)->where('userid',userrow('id'))->get())}} @endif</b>台，已关机：<b>@if(userrow('username') == 'admin') {{count(App\Instance::where('state',2)->get())}} @else {{count(App\Instance::where('state',2)->where('userid',userrow('id'))->get())}} @endif</b>台</div>
            <div class="card">
                <div class="card-toolbar d-flex flex-column flex-md-row">
                    <div class="toolbar-btn-action">
                        <a class="btn btn-primary m-r-5 ajax-modal" link="/home/dashboard/create_instance" title="启动实例"><i class="mdi mdi-plus"></i> 启动实例</a>
                    </div>
                </div>



                <div class="card-body">

                    <div class="">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>标识</th>
                                    <th>实例名称</th>
                                    <th>镜像</th>
                                    <th>IP地址</th>
                                    <th>用户已连接</th>
                                    <th>电源状态</th>
                                    <th>状态</th>
                                    <th>已创建</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $val)
                                <tr>
                                    <td><a href="javascript:void(0)" class="js-create-tab" data-title="实例控制台 - {{$val->name}}" data-url="/home/dashboard/console/{{$val->id}}">{{$val->id}}</a></td>
                                    <td>{{$val->name}}</td>
                                    <td>{{\App\Image::getRow($val->image,'name')}}</td>
                                    <td>{{$val->ipaddr}}</td>
                                    <td>
                                        <?php
                                        $user = \App\Users::getRow($val->userid);
                                        // echo $user->username;
                                        if (!$user) echo '错误';
                                        else echo $user->username;
                                        ?>
                                    </td>
                                    <td>@if($val->state == 1) 运行中 @else 关机 @endif</td>
                                    <td>@if($val->state == 1 || $val->state == 2) 无任务 @elseif($val->state == 3) <img src="/static/load.gif" width=20px height=20px>正在调度 @elseif($val->state == 7) 资源调度器出错 @elseif($val->state == 20) <img src="/static/load.gif" width=20px height=20px>正在开机 @elseif($val->state == 21) <img src="/static/load.gif" width=20px height=20px>正在重启 @elseif($val->state == 22) <img src="/static/load.gif" width=20px height=20px>正在关机 @else 错误 @endif</td>
                                    @if($val->state == 7)

                                    <script>
                                        $.ajax({
                                            type: "get",
                                            url: "/home/api/getInstance",
                                            data: {
                                                id: '{{$val->id}}'
                                            },
                                            dataType: "json",
                                            success: function(response) {
                                                if (response.code == 207) {
                                                    notify(response.data.error);
                                                }
                                            }
                                        });
                                    </script>
                                    @endif

                                    @if($val->state == 3)
                                    <script>
                                        setInterval(() => {
                                            $.ajax({
                                                type: "get",
                                                url: "/home/api/getInstance",
                                                data: {
                                                    id: '{{$val->id}}'
                                                },
                                                dataType: "json",
                                                success: function(response) {
                                                    if (response.code == 200 || response.code == 207) {
                                                        window.location.href = "/home/dashboard/instance";
                                                    }

                                                },

                                            });
                                        }, 500);
                                    </script>
                                    @endif
                                    @if($val->state == 20)
                                    <script>
                                        setInterval(() => {
                                            $.ajax({
                                                type: "get",
                                                url: "/home/api/getInstance",
                                                data: {
                                                    id: '{{$val->id}}'
                                                },
                                                dataType: "json",
                                                success: function(response) {
                                                    if (response.code != 20) {
                                                        window.location.href = "/home/dashboard/instance";
                                                    }

                                                },

                                            });
                                        }, 500);
                                    </script>

                                    @endif
                                    @if($val->state == 21)
                                    <script>
                                        setInterval(() => {
                                            $.ajax({
                                                type: "get",
                                                url: "/home/api/getInstance",
                                                data: {
                                                    id: '{{$val->id}}'
                                                },
                                                dataType: "json",
                                                success: function(response) {
                                                    if (response.code != 21) {
                                                        window.location.href = "/home/dashboard/instance";
                                                    }

                                                },

                                            });
                                        }, 500);
                                    </script>

                                    @endif
                                    @if($val->state == 22)
                                    <script>
                                        setInterval(() => {
                                            $.ajax({
                                                type: "get",
                                                url: "/home/api/getInstance",
                                                data: {
                                                    id: '{{$val->id}}'
                                                },
                                                dataType: "json",
                                                success: function(response) {
                                                    if (response.code != 22) {
                                                        window.location.href = "/home/dashboard/instance";
                                                    }

                                                },

                                            });
                                        }, 500);
                                    </script>

                                    @endif
                                    <td>{{createAt($val->created_at)}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                操作
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if($val->state == 1 || $val->state == 2)
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/edit_instance?id={{$val->id}}" title="编辑实例信息">编辑实例信息</a></li>
                                                <li><a class="dropdown-item ajax-delete" link="/home/api/start_instance?id={{$val->id}}" text="你确定要启动”{{$val->name}}“吗？" ts="启动实例">启动实例</a></li>
                                                <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/stop_instance?id={{$val->id}}" text="你确定要给实例”{{$val->name}}“断电吗？" ts="实例断电">实例断电</a></li>
                                                <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/restart_instance?id={{$val->id}}" text="你确定要重启实例”{{$val->name}}“吗？" ts="重启实例">重启实例</a></li>
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/volume_instance_connect?id={{$val->id}}" title="管理卷连接">管理卷连接</a></li>
                                                <!-- <li><a class="dropdown-item ajax-modal" link="/home/dashboard/changeflavor_instance?id={{$val->id}}" title="改变实例规格">改变实例规格</a></li> -->
                                                <!-- <li><a class="dropdown-item ajax-modal" link="/home/dashboard/admin/edit_compute?id={{$val->id}}" title="添加网络接口">添加网络接口</a></li> -->
                                                <!-- 把dvd搁浅了，懒得做 -->
                                                <!-- <li><a class="dropdown-item ajax-modal" link="/home/dashboard/admin/edit_compute?id={{$val->id}}" title="挂载CD/DVD">挂载CD/DVD</a></li> -->
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/boot_instance?id={{$val->id}}" title="管理启动顺序">管理启动顺序</a></li>
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/backup_instance?id={{$val->id}}" title="创建备份">创建备份</a></li>
                                                <li><a class="dropdown-item ajax-delete" link="/home/api/virtual_instance?id={{$val->id}}" ts="嵌套虚拟化" text="你确定要为实例启用Intel VT-x/EPT或AMD/RVI吗？开启嵌套虚拟化后你将可用在实例中继续创建虚拟机。">嵌套虚拟化</a></li>
                                                <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/delete_instance?id={{$val->id}}" text="你确定要删除”{{$val->name}}“实例吗？">删除</a></li>
                                                @elseif($val->state == 3)
                                                <li><a class="dropdown-item ajax-modal">实例正在构建</a></li>
                                                @else
                                                <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/delete_instance?id={{$val->id}}" text="你确定要删除”{{$val->name}}“实例吗？">删除</a></li>
                                                @endif

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