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
                        <a class="btn btn-primary m-r-5 ajax-modal" link="/home/dashboard/admin/create_user" title="创建用户"><i class="mdi mdi-plus"></i> 创建用户</a>&nbsp;&nbsp;
                        <!-- 导入csv用户 -->
                        <a class="btn btn-warning m-r-5 ajax-modal" link="/home/dashboard/admin/import_user" title="导入csv用户信息">导入csv用户信息</a>&nbsp;&nbsp;
                    </div>
                </div>



                <div class="card-body">

                    <div class="">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>标识</th>
                                    <th>用户名</th>
                                    <th>Email</th>
                                    <th>组</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $val)
                                <tr>
                                    <td>{{$val->id}}</td>
                                    <td>{{$val->username}}</td>
                                    <td><a href="javascript:;" link="/home/dashboard/admin/remail_user?id={{$val->id}}" title="修改邮箱：{{$val->username}}" class="ajax-modal"><span class="mdi mdi-circle-edit-outline"></span>&nbsp;{{$val->email}}</a></td>
                                    <td>
                                        <?php
                                        $grouprow = \App\Group::where('id', $val->group)->first();
                                        if (!$grouprow) echo '未分组';
                                        else echo $grouprow->name;
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                操作
                                            </button>
                                            <ul class="dropdown-menu">


                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/admin/repwd_user?id={{$val->id}}" title="重置密码">重置密码</a></li>
                                                <li><a class="dropdown-item ajax-modal" link="/home/dashboard/admin/configoauth?id={{$val->id}}" title="MAC免密认证配置">MAC免密认证</a></li>
                                                    @if($val->username != 'admin')
                                                <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/delete_user?id={{$val->id}}" text="你确定要删除“{{$val->username}}”吗？">删除</a></li>
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