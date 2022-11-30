<div class="col-lg-12">
    <div class="alert alert-info" role="alert">实例磁盘采用差分磁盘技术，实例主硬盘不会纳入计算主机磁盘使用量计算中。</div>
    <!-- 
    <div class="card">
        <div class="card-body"> -->
    <div class="row">

        <div class="col-3">
            <div class="nav nav-tabs flex-column" aria-orientation="vertical">
                <a class="nav-link active" data-toggle="pill" href="#v-jinyong" aria-selected="true">信息</a>
                <a class="nav-link" data-toggle="pill" href="#v-gulong" aria-selected="false" onclick="boot()">启动</a>
                <a class="nav-link" data-toggle="pill" href="#v-liangyusheng" aria-selected="false">网络</a>
            </div>
        </div>
        <div class="col-9">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-jinyong">
                    <div class="form-group">
                        <label>实例名称</label>
                        <input type="text" class="form-control" id="name" placeholder="" value="desktop-{{getrandom()}}">
                    </div>

                    <div class="form-group">
                        <label>计算主机</label>
                        <select class="form-control" id="compute">
                            @foreach(\App\Compute::myCompute() as $val)
                            <option value="{{$val->id}}">{{$val->hostname}}</option>
                            @endforeach

                        </select>
                    </div>

                    <div class="form-group">
                        <label>选择用户</label>
                        <input type="text" class="form-control" id="search" placeholder="搜索一个用户名" onchange="getuserbysearch()">
                        <select class="form-control" id="userid">
                            <?php
                            if (userrow('username') == 'admin') {
                                foreach (\App\Users::get() as $val) {
                                    echo '<option value="' . $val->id . '">' . $val->username . '</option>';
                                }
                            } else {
                                echo '<option value="' . userrow('id') . '">' . userrow('username') . '</option>';
                            }
                            ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>启动数量</label>
                        <input type="text" class="form-control" id="number" value="1" placeholder="">
                    </div>

                </div>
                <div class="tab-pane fade" id="v-gulong" role="tabpanel">
                    <div class="form-group">
                        <label>实例规格</label>
                        <select class="form-control" id="flavor">

                            <?php
                            // 查询userid为当前用户的flavor和shared为1的flavor
                            $flavor = \App\Flavor::where('userid', userrow('id'))->orWhere('share', 1)->get();
                            foreach ($flavor as $val) {
                                echo '<option value="' . $val->id . '">' . $val->name . '</option>';
                            }
                            ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>映像</label>
                        <select class="form-control" id="image">
                            <option value="">请先选择计算主机</option>
                        </select>
                    </div>

                </div>
                <div class="tab-pane fade" id="v-liangyusheng">
                    <div class="form-group">
                        <label>网络</label>
                        <select class="form-control" id="network">

                            <option value="">请先选择镜像</option>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>端口安全</label><br>
                        <input type="checkbox" id="portsafe">
                    </div>


                    <button class="btn btn-default ajax-modal-close">关闭</button>
                    <button id="submit" class="btn btn-primary">确定</button>

                </div>


            </div>
        </div>
    </div>
    <!-- </div>
    </div> -->

</div>
<script>
    function getuserbysearch() {
        var search = $('#search').val();
        // 在userid中搜索text为search的项，如果有就选中，没有就不选中
        $('#userid option').each(function() {
            if ($(this).text() == search) {
                $(this).prop('selected', true);
            } else {
                $(this).prop('selected', false);
            }
        });
    }


    $(document).ready(function() {
        // 监听search的变化，搜索用户名
        $('#search').bind('input propertychange', function() {
            getuserbysearch();
        });
        $("#submit").click(function() {
            var name = $("#name").val();
            var compute = $("#compute").val();
            var flavor = $("#flavor").val();
            var image = $("#image").val();
            var network = $("#network").val();
            var number = $("#number").val();
            var portsafe = $("#portsafe").is(":checked");
            var userid = $("#userid").val();

            if (portsafe) {
                portsafe = 1;
            } else {
                portsafe = 0;
            }
            // alert(network)
            if (name == '') {
                layer.msg('请输入实例名称', {
                    icon: 2,
                    time: 2000
                });
                return;
            }
            if (compute == '' || compute == null) {
                layer.msg('请选择计算主机', {
                    icon: 2,
                    time: 2000
                });
                return;
            }
            if (flavor == '' || flavor == null) {
                layer.msg('请选择实例规格', {
                    icon: 2,
                    time: 2000
                });
                return;
            }
            if (image == '' || image == null) {
                layer.msg('请选择镜像', {
                    icon: 2,
                    time: 2000
                });
                return;
            }
            if (network == '' || network == null) {
                layer.msg('请选择网络', {
                    icon: 2,
                    time: 2000
                });
                return;
            }
            if (number == '') {
                layer.msg('请输入启动数量', {
                    icon: 2,
                    time: 2000
                });
                return;
            }
            load = layer.load(1, {
                shade: [0.1, '#fff'] //0.1透明度的白色背景
            });
            $.ajax({
                type: "post",
                url: "/home/api/create_instance",
                data: {
                    name: name,
                    compute: compute,
                    flavor: flavor,
                    image: image,
                    network: network,
                    number: number,
                    portsafe: portsafe,
                    userid: userid,
                    _token: "{{csrf_token()}}"
                },
                dataType: "json",
                success: function(response) {
                    layer.close(load);
                    if (response.code == 200) {
                        layer.msg(response.msg, {
                            icon: 1,
                            time: 2000
                        });
                        setTimeout(function() {
                            window.location.href = '{{Request::get("next","/home/dashboard/instance")}}';
                        }, 500);
                    } else {
                        layer.msg(response.msg, {
                            icon: 2,
                            time: 2000
                        });
                    }
                },
                error: function(response) {
                    layer.close(load);
                    layer.msg('网络错误', {
                        icon: 2,
                        time: 2000
                    });
                }
            });

        })
    });

    function boot() {
        var compute = $('#compute').val();
        $.ajax({
            type: "get",
            url: "/home/api/getResource",
            data: {
                compute: compute,
            },
            dataType: "json",
            success: function(response) {
                if (response.code == 200) {
                    image = response.image;
                    network = response.network;

                    // 遍历image插入到select中
                    $('#image').empty();
                    $.each(image, function(index, val) {
                        $('#image').append('<option value="' + val.id + '">' + val.name + '</option>');
                    });
                    // $('#image').val(image[0].id);
                    $("#network").empty();
                    $.each(network, function(index, val) {
                        $("#network").append('<option value="' + val.id + '">' + val.name + ' - ' + val.subnet + '/' + val.netmask + '</option>');
                    });
                    // $("#network").val(network[0].id);
                } else {
                    layer.msg(response.msg, {
                        icon: 2
                    });
                }
            },
            error: function(response) {
                console.log(response);
            }
        });
    }
</script>