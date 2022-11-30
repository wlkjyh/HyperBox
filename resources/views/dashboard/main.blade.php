
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<link href="/static/css/bootstrap.min.css" rel="stylesheet">
<link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
<link href="/static/css/style.min.css" rel="stylesheet">
</head>
  
<body>
<div class="container-fluid p-t-15">
  
  <div class="row">
    <div class="col-md-6 col-xl-3">
      <div class="card bg-primary text-white">
        <div class="card-body clearfix">
          <div class="flex-box">
            <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-server fs-22"></i></span>
            <span class="fs-22 lh-22">{{$instance_count}}</span>
          </div>
          <div class="text-right">实例</div>
        </div>
      </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
      <div class="card bg-danger text-white">
        <div class="card-body clearfix">
          <div class="flex-box">
            <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-zip-disk fs-22"></i></span>
            <span class="fs-22 lh-22">{{$volume_count}}</span>
          </div>
          <div class="text-right">卷</div>
        </div>
      </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
      <div class="card bg-success text-white">
        <div class="card-body clearfix">
          <div class="flex-box">
            <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-ip-network fs-22"></i></span>
            <span class="fs-22 lh-22">{{$network_count}}</span>
          </div>
          <div class="text-right">网络</div>
        </div>
      </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
      <div class="card bg-purple text-white">
        <div class="card-body clearfix">
          <div class="flex-box">
            <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-security-network fs-22"></i></span>
            <span class="fs-22 lh-22">{{$backup_count}}</span>
          </div>
          <div class="text-right">备份</div>
        </div>
      </div>
    </div>
  </div>
  
  @if(userrow('username') == 'admin')
  <div class="row">
    
    <div class="col-lg-12">
      <div class="card">
        <header class="card-header"><div class="card-title">计算资源</div></header>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>主机名</th>
                  <th>类型</th>
                  <th>VCPU资源量</th>
                  <th>内存资源量</th>
                  <th>磁盘资源量</th>
                </tr>
              </thead>
              <tbody>
                @foreach($compute as $val)
                <tr>
                  <td>{{$val->hostname}}</td>
                  <td>Hyper-v</td>
                  <td>{{$val->vcpu}}</td>
                  <td>{{$val->ram}} MB</td>
                  <td>{{$val->disk}} GB</td>
                </tr>
                @endforeach
               
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    
  </div>
  @endif
  
</div>

<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script type="text/javascript" src="/static/js/popper.min.js"></script>
<script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/main.min.js"></script>
<script type="text/javascript" src="/static/js/Chart.min.js"></script>

</body>
</html>
