
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
            <div class="card">
                <div class="card-toolbar d-flex flex-column flex-md-row">
                    <div class="toolbar-btn-action">
                        <a class="btn btn-primary m-r-5 ajax-modal" link="/home/dashboard/create_flavor" title="创建实例规格"><i class="mdi mdi-plus"></i> 创建实例规格</a>
                    </div>
                </div>



                <div class="card-body">

                    <div class="">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>标识</th>
                                    <th>创建者</th>
                                    <th>规格名称</th>
                                    <th>VCPU数量</th>
                                    <th>内存类型</th>
                                    <th>内存数量</th>
                                    <th>TRX因子</th>
                                    <th>共享的</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $val)
                                <tr>
                                    <td>{{$val->id}}</td>
                                    <td><?php
                                        $user = \App\Users::find($val->userid);
                                        echo $user->username;
                                        
                                    ?></td>
                                    <td>{{$val->name}}</td>
                                    <td>{{$val->vcpu}}</td>
                                    <td>@if($val->type == 1) 动态内存 @else 静态内存 @endif</td>
                                    <td>{{$val->ram}} MB @if($val->type == 1) ： {{$val->min}}MB->{{$val->max}}MB @endif</td>
                                    <td>{{$val->trx}}</td>
                                    <td>@if($val->share == 1) True @else False @endif</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                操作
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/edit_flavor?id={{$val->id}}" title="编辑实例规格" >编辑实例规格</a></li>
                                                <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/delete_flavor?id={{$val->id}}" text="你确定要删除“{{$val->name}}”吗？">删除</a></li>
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