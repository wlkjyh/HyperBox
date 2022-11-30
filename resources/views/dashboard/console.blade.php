
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>实例控制台</title>
    <link href="/static/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css" rel="stylesheet">
    <style>
        #conif {
            /* 隐藏滚轮 */
            overflow: hidden;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-t-15">

        <div class="row">

            <div class="col-lg-12">
                <p class="text-left">

                    <!-- 返回 -->
                    <!-- <a href="/home/dashboard/instance" class="btn btn-primary">返回</a> -->
                </p>

                <div class="card">
                    <header class="card-header">
                        <div class="card-title">实例控制台</div>
                    </header>
                    <div class="card-body">

                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#message" aria-selected="true">信息</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#cpu" aria-selected="true">资源利用率</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#volume" aria-selected="false">卷 & CD/DVD</a>
                            </li> -->
                            <li class="nav-item">
                                <a class="nav-link ajax-modal" link="/home/dashboard/boot_instance?id={{$row->id}}" title="管理启动顺序" data-toggle="tab" href="javascript:;" aria-selected="false">启动顺序</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($row->state != 1 && $row->state != 2) disabled @endif @if($row->vid == 'unknown') disabled @endif" data-toggle="tab" href="#console" aria-selected="false">控制台</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#log" aria-selected="false">实例日志</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="message">
                                <h3>基本</h3>
                                <hr>
                                <div class="form-group">
                                    <label><b>实例名称</b></label>：{{$row->name}}
                                </div>
                                <div class="form-group">
                                    <label><b>标识</b></label>：{{$row->id}}
                                </div>
                                <div class="form-group">
                                    <label><b>创建时间</b></label>：{{$row->created_at}}
                                </div>
                                <div class="form-group">
                                    <label><b>计算主机</b></label>：{{\App\Compute::getRow($row->compute,'hostname')}}
                                </div>
                                <div class="form-group">
                                    <label><b>状态</b></label>：@if($row->state == 1) 开机 @elseif($row->state == 2) 关机 @elseif($row->state == 3) 正在调度 @elseif($row->state == 7) 出错 @else 错误 @endif
                                </div>
                                @if($row->state == 7)
                                <div class="form-group">
                                    <label><b>事件</b></label>：<code>{{$row->error}}</code>
                                </div>


                                @endif
                                <h3>网络</h3>
                                <hr>
                                <?php
                                $network = \App\network::where('id', $row->network)->first();
                                if ($network) {
                                ?>
                                    <div class="form-group">
                                        <label><b>网络名称</b></label>：{{$network->name}}
                                    </div>
                                    <div class="form-group">
                                        <label><b>标识</b></label>：{{$network->id}}
                                    </div>
                                    <div class="form-group">
                                        <label><b>网络类型</b></label>：{{$network->type}}
                                    </div>
                                    <div class="form-group">
                                        <label><b>IP地址</b></label>：{{$row->ipaddr}}
                                    </div>
                                <?php

                                } ?>
                                <h3>实例规格</h3>
                                <hr>
                                <?php
                                $flavor = \App\Flavor::where('id', $row->flavor)->first();
                                if ($flavor) {
                                ?>
                                    <div class="form-group">
                                        <label><b>实例规格名称</b></label>：{{$flavor->name}}
                                    </div>

                                    <div class="form-group">
                                        <label><b>标识</b></label>：{{$flavor->id}}
                                    </div>


                                    <div class="form-group">
                                        <label><b>VCPU数量</b></label>：{{$flavor->vcpu}}
                                    </div>


                                    <div class="form-group">
                                        <label><b>内存容量</b></label>：{{$flavor->ram}} MB
                                    </div>
                                <?php
                                }
                                ?>
                                <hr>
                                <h3>已连接的卷</h3>
                                @if(count($myVolume) == 0) 没有连接任何的卷 @endif

                                @foreach($myVolume as $v)
                                <div class="form-group">
                                    <label><b>已连接</b></label>：{{$v->name}} - {{$v->id}}
                                </div>
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="cpu">
                                <canvas id="cpuages" width="400" height="100"></canvas>
                            </div>
                            <div class="tab-pane fade" id="console">
                                <div class="alert alert-primary" role="alert">如果控制台无法操作，请点击：<a href="{{$freerdp}}" target="_blank">仅显示FreeRDP控制台</a><br>实例开启了端口安全，IP地址必须和分配的IP地址相同，否则会被防火墙拦截</div>
                                <iframe id="conif" src="{{$freerdp}}" width="100%" height="1024" frameborder="0"></iframe>
                            </div>
                            <div class="tab-pane fade" id="log">
                                <textarea class="form-control" id="logs" rows="20" style="resize:none" readonly>@foreach($log as $val){{$val->data."\n"}}@endforeach
                            </textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>






        </div>

    </div>

    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/popper.min.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/main.min.js"></script>
    <script type="text/javascript" src="/static/js/Chart.min.js"></script>
    <script src="/static/main.js"></script>
    <script src="/static/layer/layer.js"></script>

    <script>
        $(document).ready(function() {
            cpuuse = [0]

            function getcpuuse() {
                $.ajax({
                    type: "get",
                    url: "/home/api/getcpu?id={{$row->id}}",
                    // data: "data",
                    dataType: "json",
                    success: function(response) {
                        if (response.code == 200) {
                            cpu = response.cpu
                        } else {
                            cpu = 0
                        }
                    },
                    error: function(response) {
                        cpu = 0
                    }
                });
                // 添加到数组前面
                cpuuse.unshift(cpu)
                // chartline1.data.labels = [];
                chartline1.data.datasets[0].data = cpuuse;
                chartline1.update();
                console.log(cpuuse);
            }
            setInterval(() => {
                getcpuuse()
            }, 3000);


            var chartline1 = new Chart($("#cpuages"), {
                type: 'line',
                data: {
                    labels: ['', '', '', '', '', '', '', ''],
                    datasets: [{
                        label: "CPU使用率",
                        fill: false,
                        borderWidth: 3,
                        pointRadius: 5,
                        borderColor: "#9966ff",
                        pointBackgroundColor: "#9966ff",
                        pointBorderColor: "#9966ff",
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: "#9966ff",
                        data: [0]
                    }],
                },
                options: {
                    legend: {
                        display: false
                    },
                }
            });
            getcpuuse()
        });
    </script>
</body>

</html>