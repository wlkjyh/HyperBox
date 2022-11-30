<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>云计算机</title>
    <meta name="author" content="yinqi">
    <link href="/static/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css" rel="stylesheet">
    
    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
</head>

<body>
    <div class="container-fluid p-t-15">

        <div class="lyear-divider">您有{{count($myInstance)}}台云计算机</div>

        <div class="row">
            @foreach($myInstance as $row)
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <div class="card-title">{{$row->name}}</div>
                        <ul class="card-actions">
                            <li><a href="#!" class="card-btn-slide"><i class="mdi mdi-chevron-up"></i></a></li>
                        </ul>
                    </header>
                    <div class="card-body">
                        <div class="form-group">
                            <label>云计算机名称</label>：{{$row->name}}&nbsp
                        </div>
                        <!-- 电源 -->
                        <div class="form-group">
                            <label>云计算机状态</label>：@if($row->state == 1) 正在运行 @elseif($row->state == 2) 关机 @elseif($row->state == 3) <img src="/static/load.gif" width=20px height=20px>正在调度 @elseif($row->state == 7) 资源调度器出错 @elseif($row->state == 20) <img src="/static/load.gif" width=20px height=20px>正在开机 @elseif($row->state == 21) <img src="/static/load.gif" width=20px height=20px>正在重启 @elseif($row->state == 22) <img src="/static/load.gif" width=20px height=20px>正在关机 @else 错误 @endif
                        </div>
                        @if($row->state == 20 || $row->state == 21 || $row->state == 22)
                        <script>
                            // 每秒获取一次实例状态，直到任务完成或者发送错误
                            $(document).ready(function() {
                                setInterval(() => {
                                    $.ajax({
                                        type: "get",
                                        url: "/home/api/getInstance",
                                        data: {
                                            id: '{{$row->id}}'
                                        },
                                        dataType: "json",
                                        success: function(response) {
                                            if (response.code != '{{$row->state}}') {
                                                window.location.reload();
                                            }
                                        }
                                    });
                                }, 1000);
                            });
                        </script>
                        @endif
                        <!--IP地址 -->
                        <div class="form-group">
                            <label>IP地址</label>：{{$row->ipaddr}}
                        </div>

                        <div class="callout callout-warning mt-3" style="display:none">
                            <?php
                            $flavor = \App\Flavor::getRow($row->flavor);

                            ?>
                            CPU：{{$flavor->vcpu}} 核&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;内存：{{$flavor->ram}} MB
                        </div><br>
                        <div class="form-group">
                            <?php
                            $volume = \App\Volume::where('instance', $row->id)->get();
                            foreach ($volume as $v) {
                                echo '数据盘:' . $v->name . '&nbsp;&nbsp容量:' . $v->size . 'GB<br>';
                            }
                            ?>
                        </div>
                    </div>
                    <footer class="card-footer flex-box">
                        <div class="custom-control custom-checkbox mt-2">
                        </div>
                        <!-- <div class="example-left text-right"> -->
                        <!-- 居左 -->
                        <div style="position: absolute; left: 5;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    电源
                                </button>
                                <ul class="dropdown-menu">
                                    @if($row->state == 1 || $row->state == 2)
                                    <li><a class="dropdown-item ajax-delete" link="/home/api/start_instance?id={{$row->id}}" text="你确定要启动”{{$row->name}}“吗？" ts="开机">开机</a></li>
                                    <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/stop_instance?id={{$row->id}}" text="你确定要给实例”{{$row->name}}“断电吗？" ts="关机">关机</a></li>
                                    <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/restart_instance?id={{$row->id}}" text="你确定要重启实例”{{$row->name}}“吗？" ts="重启">重启</a></li>
                                    <li><a class="dropdown-item ajax-modal" link="/home/dashboard/backup_instance?id={{$row->id}}" title="创建备份">创建备份</a></li>
                                    @elseif($row->state == 3)
                                    <li><a class="dropdown-item ajax-modal">实例正在构建</a></li>
                                    @else
                                    @endif

                                </ul>
                            </div>
                            @if($row->vid == 'unknown')

                            <button class="btn btn-label btn-info" disabled><label><i class="mdi mdi-keyboard-tab"></i></label> 进入云计算机</button>
                            @else
                            <?php
                            $compute = \App\Compute::where('id', $row->compute)->first();
                            // $hostname = $compute->hostname;
                            // $arr = explode(':', $hostname);
                            $exp = explode("\n", $compute->console);
                            $rdp = $exp[mt_rand(0, count($exp) - 1)];
                            ?>
                            <a href="http://{{$rdp}}/#vid={{$row->vid}}" target="_"><button class="btn btn-label btn-info"><label><i class="mdi mdi-keyboard-tab"></i></label> 进入云计算机</button></a>
                            @endif


                            <button type="button" class="btn btn-info ajax-modal" link="/home/dashboard/backup_instance?id={{$row->id}}" title="创建数据备份">
                                数据备份
                            </button>

                            <button type="button" class="btn btn-info ajax-modal" link="/home/dashboard/restore_instance?id={{$row->id}}" title="数据恢复">
                                数据恢复
                            </button>

                        </div>

                </div>


                </footer>
            </div>
        </div>
        @endforeach

    </div>

    <br><br>
    <center>

        <button class="btn btn-w-md btn-round btn-success ajax-modal" title="修改密码" link="/home/dashboard/changepassword">修改密码</button>
        <a href="/acloud/logout.middleware">
            <button class="btn btn-w-md btn-round btn-success">安全退出</button>
        </a>
    </center>




    </div>

    <script type="text/javascript" src="/static/js/popper.min.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/main.min.js"></script>
    <script type="text/javascript" src="/static/layer/layer.js"></script>
    <script src="/static/main.js"></script>
    <script>
        // 你是不是想研究下为什么这个页面没有modal,但是可以显示出modal😀
    </script>
</body>

</html>