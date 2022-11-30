
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>H - 仪表盘</title>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="stylesheet" type="text/css" href="/static/css/materialdesignicons.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/static/js/bootstrap-multitabs/multitabs.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/animate.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/style.min.css">
</head>

<body>
    <div class="lyear-layout-web">
        <div class="lyear-layout-container">
            <!--左侧导航-->
            <aside class="lyear-layout-sidebar">

                <!-- logo -->
                <div id="logo" class="sidebar-header">
                    <a href="/home/dashboard"><img src="/static/logo.png" title="dreamStack" alt="dreamStack" /></a>
                </div>
                <div class="lyear-layout-sidebar-info lyear-scroll">

                    <nav class="sidebar-main">
                        <ul class="nav-drawer">
                            <li class="nav-item active">
                                <a class="multitabs" href="/home/dashboard/main">
                                    <i class="mdi mdi-view-dashboard"></i>
                                    <span>概况</span>
                                </a>
                            </li>


                            @if(getconfig('networktop') == 'true')
                            <li class="nav-item">
                                <a class="multitabs" href="/home/dashboard/networktop">
                                    <i class="mdi mdi-group"></i>
                                    <span>网络拓扑</span>
                                </a>
                            </li>
                            @endif

                            <li class="nav-item nav-item-has-subnav">
                                <a href="javascript:void(0)"><i class="mdi mdi-apple-icloud"></i> <span>项目</span></a>
                                <ul class="nav nav-subnav">
                                    <li> <a class="multitabs" href="/home/dashboard/instance">实例</a> </li>
                                    <li> <a class="multitabs" href="/home/dashboard/volume">卷</a> </li>
                                    <li> <a class="multitabs" href="/home/dashboard/backup">备份 & 快照</a> </li>
                                </ul>
                            </li>


                            <li class="nav-item nav-item-has-subnav">
                                <a href="javascript:void(0)"><i class="mdi mdi-zip-disk"></i> <span>访问资源</span></a>
                                <ul class="nav nav-subnav">
                                    <li> <a class="multitabs" href="/home/dashboard/network">网络</a> </li>
                                    <li> <a class="multitabs" href="/home/dashboard/flavor">Flavor</a> </li>
                                    <li> <a class="multitabs" href="/home/dashboard/image">镜像</a> </li>
                                </ul>
                            </li>



                            @if($userrow->username == 'admin')
                            <li class="nav-item nav-item-has-subnav">
                                <a href="javascript:void(0)"><i class="mdi mdi-shield-account"></i> <span>管理员</span></a>
                                <ul class="nav nav-subnav">
                                    <li> <a class="multitabs" href="/home/dashboard/admin/network">网络</a> </li>
                                    <!-- <li> <a class="multitabs" href="/home/dashboard/admin/route">路由器</a> </li> -->
                                    <li> <a class="multitabs" href="/home/dashboard/admin/image">镜像</a> </li>
                                    <li> <a class="multitabs" href="/home/dashboard/admin/compute">计算资源</a> </li>
                                    <li> <a class="multitabs" href="/home/dashboard/admin/flavor">Flavor</a> </li>

                                    <!-- 到时候在做计划任务 -->
                                    <!-- <li><a class="multitabs" href="/home/dashboard/admin/tasks">计划任务</a></li> -->
                                    <li> <a class="multitabs" href="/home/dashboard/admin/license">许可证</a> </li>
                                    <li> <a class="multitabs" href="/home/dashboard/admin/system">系统设置</a> </li>
                                </ul>
                            </li>

                            <li class="nav-item nav-item-has-subnav">
                                <a href="javascript:void(0)"><i class="mdi mdi-account-group"></i> <span>身份管理</span></a>
                                <ul class="nav nav-subnav">
                                    <li> <a class="multitabs" href="/home/dashboard/admin/user">用户</a> </li>
                                    <li> <a class="multitabs" href="/home/dashboard/admin/group">组</a> </li>
                                </ul>
                            </li>

                            <!-- 访问安全 -->
                            <li class="nav-item">
                                <a class="multitabs" href="/home/dashboard/admin/security">
                                    <i class="mdi mdi-security"></i>
                                    <span>访问 & 安全</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </nav>

                    <div class="sidebar-footer">
                        <p class="copyright">Copyright &copy; {{date('Y')}}. <a target="_blank" href="http://dreamstack.baseyun.cn">baseyun</a></p>
                    </div>
                </div>

            </aside>
            <!--End 左侧导航-->

            <!--头部信息-->
            <header class="lyear-layout-header">

                <nav class="navbar">

                    <div class="navbar-left">
                        <div class="lyear-aside-toggler">
                            <span class="lyear-toggler-bar"></span>
                            <span class="lyear-toggler-bar"></span>
                            <span class="lyear-toggler-bar"></span>
                        </div>
                    </div>

                    <ul class="navbar-right d-flex align-items-center">
                        <li class="dropdown dropdown-profile">
                            <a href="javascript:void(0)" data-toggle="dropdown" class="dropdown-toggle">
                                <img class="img-avatar img-avatar-48 m-r-10" src="/static/users.png" alt="{{$userrow->username}}" />
                                <span>{{$userrow->username}}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                            
                                <li>
                                    <a class="multitabs dropdown-item" data-url="/home/dashboard/changepassword" href="javascript:void(0)"><i class="mdi mdi-lock-outline"></i> 更改密码</a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="/home/dashboard/logout"><i class="mdi mdi-logout-variant"></i>注销登录</a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                </nav>

            </header>
            <!--End 头部信息-->

            <!--页面主要内容-->
            <main class="lyear-layout-content">
                <div id="iframe-content"></div>
            </main>
            <!--End 页面主要内容-->
        </div>
    </div>

    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/popper.min.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap-multitabs/multitabs.min.js"></script>
    <script type="text/javascript" src="/static/js/jquery.cookie.min.js"></script>
    <script type="text/javascript" src="/static/js/index.min.js"></script>
    <script src="/static/layer/layer.js"></script>
    <script>
        $(document).ready(function() {
            function callback() {
                setTimeout(() => {
                    $.ajax({
                        type: "get",
                        url: "/home/api/getstatus",
                        // data: "data",
                        dataType: "json",
                        success: function(response) {
                            if (response.code != 200) {
                                window.location.reload()
                            } else {
                                callback()
                            }
                        },
                        error: function() {
                            window.location.reload()
                        }
                    });
                }, 1000);
            }
            callback();
        });
    </script>
</body>

</html>