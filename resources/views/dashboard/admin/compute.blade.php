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
                        <a class="btn btn-primary m-r-5 ajax-modal" link="/home/dashboard/admin/create_compute" title="添加计算主机"><i class="mdi mdi-plus"></i> 添加新计算主机</a>
                    </div>
                </div>



                <div class="card-body">

                    <div class="">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>标识</th>
                                    <th>主机名</th>
                                    <th>模拟器类型</th>
                                    <th>VCPU资源量</th>
                                    <th>内存资源量</th>
                                    <th>磁盘资源量</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $val)
                                <tr>
                                    <td>{{$val->id}}</td>
                                    <td>{{$val->hostname}}</td>
                                    <td>Hyper-V</td>
                                    <td>{{$val->vcpu}}</td>
                                    <td>{{$val->ram}} MB</td>
                                    <td>{{$val->disk}} GB</td>

                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                操作
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/admin/rule_compute?id={{$val->id}}" title="管理计算主机访问权" >管理计算主机访问权</a></li>
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/admin/edit_compute?id={{$val->id}}" title="编辑计算主机" >编辑计算主机</a></li>
                                                <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/delete_compute?id={{$val->id}}" text="你确定要删除“{{$val->hostname}}”吗？该计算主机下的实例、卷、网络的记录都会被删除，但是不会影响实际计算主机下的计算资源">删除</a></li>
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