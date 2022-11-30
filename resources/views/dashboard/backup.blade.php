
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
              



                <div class="card-body">

                    <div class="">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>标识</th>
                                    <th>名称</th>
                                    <th>源</th>
                                    <th>源类型</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $val)
                                <tr>
                                    <td>{{$val->id}}</td>
                                    <td>{{$val->name}}</td>
                                    <td>
                                        @if($val->type == 'instance')
                                        <?php
                                            $s = \App\Instance::where('id',$val->uuid)->first();
                                            echo $s->name;
                                        ?>
                                        @elseif($val->type == 'volume')
                                        <?php
                                            $s = \App\Volume::where('id',$val->uuid)->first();
                                            echo $s->name;
                                        ?>
                                        @endif
                                    </td>
                                    <td>{{$val->type}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                操作
                                            </button>
                                            <ul class="dropdown-menu">
                                            <li>
                                            <li><a class="dropdown-item ajax-delete" link="/home/api/reback_backup?id={{$val->id}}" text="你确定要将“{{$val->name}}”恢复到源吗？" ts="恢复到源">恢复到源</a></li>
                                                <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/delete_backup?id={{$val->id}}" text="你确认要删除“{{$val->name}}”吗？">删除</a></li>
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