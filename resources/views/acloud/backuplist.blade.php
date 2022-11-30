<table class="table" id="dataTable">
    <thead>
        <tr>
            <!-- <th>标识</th> -->
            <th>名称</th>
            <!-- <th>源</th> -->
            <th>源类型</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($Backup as $val)
        <tr>
            <!-- <td>{{$val->id}}</td> -->
            <td>{{$val->name}}</td>
           
            <td>{{$val->type}}</td>
            <td>
                <!-- <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        操作
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                        <li><a class="dropdown-item ajax-delete" link="/home/api/reback_backup?id={{$val->id}}" text="你确定要将“{{$val->name}}”恢复到源吗？" ts="恢复到源">恢复到源</a></li>
                        <li><a class="dropdown-item ajax-delete" style="color:red" link="/home/api/delete_backup?id={{$val->id}}" text="你确认要删除“{{$val->name}}”吗？">删除</a></li>
                    </ul>
                </div> -->
            <button class="btn btn-info btn-sm ajax-delete"  link="/home/api/reback_backup?id={{$val->id}}" text="你确定要将“{{$val->name}}”恢复到源吗？" ts="恢复到源">恢复到源</button>
            <button class="btn btn-danger btn-sm ajax-delete"  link="/home/api/delete_backup?id={{$val->id}}" text="你确定要删除“{{$val->name}}”吗？">删除</button>
            </td>
        </tr>
        @endforeach

    </tbody>

    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/popper.min.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/main.min.js"></script>
    <script type="text/javascript" src="/static/layer/layer.js"></script>
    <script src="/static/main.js"></script>