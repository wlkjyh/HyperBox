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



            <div class="card-header">
                <h4 class="card-title">组成员</h4>
            </div>
                <div class="card-body">

                    <div class="">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>标识</th>
                                    <th>用户名</th>
                                    <th>邮箱</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $val)
                                <tr>
                                    <td>{{$val->id}}</td>
                                    <td>{{$val->username}}</td>
                                    <td>{{$val->email}}</td>
                                    <td>
                                        <!-- 移除组 -->
                                        <a href="javascript:;" class="btn btn-danger btn-sm ajax-delete" link="/home/api/remove_group?id={{$val->id}}" ts="移除组" text="你确定要把这个用户移除组吗">移除</a>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">



            <div class="card-header">
                <h4 class="card-title">可加入成员</h4>
            </div>
                <div class="card-body">

                    <div class="">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>标识</th>
                                    <th>用户名</th>
                                    <th>邮箱</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows2 as $val)
                                <tr>
                                    <td>{{$val->id}}</td>
                                    <td>{{$val->username}}</td>
                                    <td>{{$val->email}}</td>
                                    <td>
                                        <!-- 移除组 -->
                                        <a href="javascript:;" class="btn btn-primary btn-sm ajax-delete" link="/home/api/add_group?id={{$val->id}}&group={{$group->id}}" ts="加入组" text="你确定要这个用户加入组吗">加入</a>
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