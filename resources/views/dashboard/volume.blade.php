
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
    
    <script src="/static/coco-message.min.js"></script>
</head>

<body>
    <div class="container-fluid p-t-15">

        <!-- <div class="row"> -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-toolbar d-flex flex-column flex-md-row">
                    <div class="toolbar-btn-action">
                        <a class="btn btn-primary m-r-5 ajax-modal" link="/home/dashboard/create_volume" title="创建卷"><i class="mdi mdi-plus"></i> 创建卷</a>
                    </div>
                </div>



                <div class="card-body">

                    <div class="">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>标识</th>
                                    <th>计算主机</th>
                                    <th>卷名称</th>
                                    <th>卷大小</th>
                                    <th>卷已连接</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $val)
                                <tr>
                                    <td><a href="javascript:void(0)" class="js-create-tab" data-title="卷详情 - {{$val->name}}" data-url="/home/dashboard/volume/{{$val->id}}">{{$val->id}}</a></td>
                                    <td>{{\App\Compute::getRow($val->compute,'hostname')}}</td>
                                    <td>{{$val->name}}</td>
                                    <td>{{$val->size}} GB</td>
                                    <td>
                                        @if($val->instance != '') {{\App\Instance::getRow($val->instance,'name')}} @else 无 @endif
                                    </td>
                                    <td>@if($val->state == 1) 无状态 @elseif($val->state == 10) 创建卷出错 @else 错误 @endif</td>
                                    
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                操作
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if($val->state == 1)
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/edit_volume?id={{$val->id}}" title="编辑卷">编辑卷</a></li>
                                                @if($val->instance == '')
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/connect_volume?id={{$val->id}}" title="连接卷">连接卷</a></li>
                                                @else
                                                <li><a class="dropdown-item ajax-delete" link="/home/api/unconnect_volume?id={{$val->id}}" text="你确定要分离卷“{{$val->name}}”卷吗？" ts="分离卷">分离卷</a></li>
                                                @endif
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/resize_volume?id={{$val->id}}" title="调整卷大小">调整卷大小</a></li>
                                                <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/delete_volume?id={{$val->id}}" text="你确定要删除“{{$val->name}}”吗？">删除</a></li>
                                                @else
                                                <li><a class="dropdown-item ajax-delete" link="/home/api/reset_volume?id={{$val->id}}" text="你确定要重建“{{$val->name}}”卷吗？" ts="重建卷">重建卷</a></li>
                                                    <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/delete_volume?id={{$val->id}}" text="你确定要删除“{{$val->name}}”吗？">删除</a></li>
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