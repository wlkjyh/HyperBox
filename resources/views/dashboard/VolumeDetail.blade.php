
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
            <p class="text-left">

                <!-- 返回 -->
                <!-- <a href="/home/dashboard/volume" class="btn btn-primary">返回</a> -->
            </p>

            <p class="text-right">
                <a link="/home/dashboard/resize_volume?id={{$row->id}}" title="调整卷大小" class="btn btn-primary ajax-modal">调整卷大小</a>
            </p>
            <div class="card">

                <div class="card-header">
                    {{$row->name}}
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label>卷名称</label>：{{$row->name}}
                    </div>
                    <div class="form-group">
                        <label>创建时间</label>：{{$row->created_at}}
                    </div>
                    <div class="form-group">
                        <label>卷大小</label>：{{$row->size}} GB
                    </div>
                    <div class="form-group">
                        <label>计算主机</label>：{{\App\Compute::getRow($row->compute,'hostname')}}
                    </div>
                    <div class="form-group">
                        <label>状态</label>：@if($row->state == 1) 无状态 @elseif($row->state == 10) 创建卷出错 @else 错误 @endif
                    </div>
                    @if($row->state == 10)
                    <div class="form-group">
                        <label>事件</label>：<code>{{$row->path}}</code>
                    </div>
                    @endif
                </div>

            </div>


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