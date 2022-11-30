<div class="col-lg-12">
    <!-- 
    <div class="card">
        <div class="card-body"> -->
    <div class="row">
        <div class="col-3">
            <div class="nav nav-tabs flex-column" aria-orientation="vertical">
                <a class="nav-link active" data-toggle="pill" href="#v-jinyong" aria-selected="true">信息</a>
                <a class="nav-link" data-toggle="pill" href="#v-gulong" aria-selected="false">子网</a>
                <a class="nav-link" data-toggle="pill" href="#v-liangyusheng" aria-selected="false">分配</a>
            </div>
        </div>
        <div class="col-9">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-jinyong">
                    <div class="form-group">
                        <label>网络名称</label>
                        <input type="text" class="form-control" id="name" placeholder="">
                    </div>

                    <div class="form-group">
                        <label>计算主机</label>
                        <select class="form-control" id="compute">
                            @foreach(\App\Compute::get() as $val)
                            <option value="{{$val->id}}">{{$val->hostname}}</option>
                            @endforeach

                        </select>
                    </div>

                    <div class="form-group">
                        <label>网络类型</label>
                        <select class="form-control" id="type" onchange="set()">
                            <option value="physics">桥接网络</option>
                            <option value="vlan">VLAN</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>交换机名称</label>
                        <input type="text" class="form-control" id="switchname" placeholder="连接外部网络的虚拟交换机名称">
                    </div>

                </div>
                <div class="tab-pane fade" id="v-gulong" role="tabpanel">
                    <div class="form-group" style="display:none" id="vlan">
                        <label>VLANID</label>
                        <input type="text" class="form-control" id="vlanid" placeholder="">
                    </div>

                    <div class="form-group">
                        <label>子网</label>
                        <input type="text" class="form-control" id="subnet" placeholder="">
                    </div>

                    <div class="form-group">
                        <label>子网掩码</label>
                        <input type="text" class="form-control" id="netmask" value="255.255.255.0" placeholder="">
                    </div>

                    <div class="form-group">
                        <label>网关</label>
                        <input type="text" class="form-control" id="gateway" placeholder="">
                    </div>

                </div>
                <div class="tab-pane fade" id="v-liangyusheng">
                    <div class="form-group">
                        <label>DHCP代理程序</label><br>
                        <input type="checkbox" id="dhcp" checked>
                    </div>

                    <div class="form-group">
                        <label>分配池</label>
                        <input type="text" class="form-control" id="list" placeholder="">
                        <small class="help-block">例如：<code>192.168.10.100-192.168.10.200</code></small>
                    </div>

                    <div class="form-group">
                        <label>DNS</label>
                        <input type="text" class="form-control" id="dns" placeholder="">
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
    function set() {
        var type = $('#type').val();
        if (type == 'vlan') {
            $('#vlan').show();
        } else {
            $('#vlan').hide();
        }
    }
    $(document).ready(function() {
        $('#submit').click(function(e) {
            e.preventDefault();
            var name = $('#name').val();
            var compute = $('#compute').val();
            var type = $('#type').val();
            var switchname = $('#switchname').val();
            var vlanid = $('#vlanid').val();
            var subnet = $('#subnet').val();
            var netmask = $('#netmask').val();
            var gateway = $('#gateway').val();
            var dhcp = $('#dhcp').is(':checked');
            var list = $('#list').val();
            var dns = $('#dns').val();
            if (name == '') {
                return layer.msg('请输入网络名称', {
                    icon: 2,
                    time: 500
                });
            }
            if (compute == '') {
                return layer.msg('请选择计算主机', {
                    icon: 2,
                    time: 500
                });
            }
            if (type == 'vlan') {
                if (vlanid == '') {
                    return layer.msg('请输入VLANID', {
                        icon: 2,
                        time: 500
                    });
                }
            }
            if (subnet == '') {
                return layer.msg('请输入子网', {
                    icon: 2,
                    time: 500
                });
            }
            if (netmask == '') {
                return layer.msg('请输入子网掩码', {
                    icon: 2,
                    time: 500
                });
            }
            if (gateway == '') {
                return layer.msg('请输入网关', {
                    icon: 2,
                    time: 500
                });
            }
            if (list == '') {
                return layer.msg('请输入分配池', {
                    icon: 2,
                    time: 500
                });
            }
            if (dns == '') {
                return layer.msg('请输入DNS', {
                    icon: 2,
                    time: 500
                });
            }
            load = layer.load(1, {
                shade: [0.1, '#fff'] //0.1透明度的白色背景
            });
            $.ajax({
                type: "post",
                url: "/home/api/create_network",
                data: {
                    name: name,
                    compute: compute,
                    type: type,
                    switchname: switchname,
                    vlanid: vlanid,
                    subnet: subnet,
                    netmask: netmask,
                    gateway: gateway,
                    dhcp: dhcp,
                    list: list,
                    dns: dns,
                    _token: '{{csrf_token()}}'
                },
                dataType: "json",
                success: function(response) {
                    layer.close(load);
                    if (response.code == 200) {
                        window.location.href="{{Request::get('next','/home/dashboard/admin/network')}}";
                    }else{
                        layer.msg(response.msg, {
                            icon: 2,
                            time: 500
                        });
                    }
                },
                error: function() {
                    layer.close(load);
                    layer.msg('网络错误', {
                        icon: 2,
                        time: 500
                    });
                }
            });



        });
    });
</script>