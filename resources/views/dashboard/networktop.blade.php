
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
    <script type="text/javascript" src="/static/networktop/vis-network.min.js"></script>
    <!--<script type="text/javascript" src="js/vis-network.min.js"></script>-->

    <style type="text/css">
        #mynetwork {
            width: calc(100vw);
            height: calc(100vh);
        }

        * {
            padding: 0;
            margin: 0;
        }

        .menu {
            position: absolute;
            border-radius: 5px;
            left: -99999px;
            top: -999999px;
        }
        html, body {
            overflow: hidden;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-t-15">
    <p class="text-right">
        @if(userrow('username') == 'admin')
        <a link="/home/dashboard/admin/create_compute?next=/home/dashboard/networktop" class="btn btn-primary ajax-modal" title="添加计算主机">添加计算主机</a>&nbsp;&nbsp;
            <a link="/home/dashboard/admin/create_network?next=/home/dashboard/networktop" class="btn btn-primary ajax-modal" title="创建网络">创建网络</a>&nbsp;&nbsp;
        @endif
        <a link="/home/dashboard/create_instance?next=/home/dashboard/networktop" class="btn btn-primary ajax-modal" title="启动实例">启动实例</a>
    </p>

        <!-- <div class="row"> -->
        <div class="col-lg-12">
            <div id="mynetwork"></div>
            <!--菜单操作-->

            <!--节点悬停-->
            <div class="menu" id="divHoverNode" style="display: none;">
                <!--<ul></ul>-->
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
    <script type="text/javascript">
        <?php
        echo 'var nodes = ' . $nodes . ';' . "\n";
        echo 'var edges = ' . $edges;
        ?>

        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].type == 'compute') {
                nodes[i].image = '/static/networktop/server.png';
            }
            if (nodes[i].type == 'network') {
                nodes[i].image = '/static/networktop/network.png';
            }
            if (nodes[i].type == 'instance') {
                nodes[i].image = '/static/networktop/instance.png';
            }
            if (nodes[i].type == 'route') {
                nodes[i].image = '/static/networktop/route.png';
            }
        }

        var container = document.getElementById('mynetwork');
        var data = {
            nodes: nodes,
            edges: edges
        };
        console.log('nodes', data.nodes);

        var options = {
            nodes: {
                shape: 'image' //设置图片
            },
            interaction: {
                hover: true,
                hoverConnectedEdges: true
            }
        };

        var network = new vis.Network(container, data, options);

        function getNode(option) {
            for (var i = 0; i < nodes.length; i++) {
                if (option == nodes[i].id) {
                    // console.log('i',nodes[i]);
                    return nodes[i];
                }
            }
        }

        function getEdge(option) {
            var linkId = option;
            var linkIdFirst = linkId.substring(0, 1);
            var linkIdLast = linkId.substring(linkId.length - 1, linkId.length);
            var dataList = [];
            for (var j = 0; j < nodes.length; j++) {
                if (linkIdFirst == nodes[j].id || linkIdLast == nodes[j].id) {
                    dataList.push(nodes[j]);
                }
            }
            return dataList;
        }

        network.on('hoverNode', function(properties) {
            var hoveNodeList = getNode(properties.node);
            var deviceType = hoveNodeList.type;
            var imgPathSrc = hoveNodeList.image;
            var $ul = "<ul class=\"list-group\">" +
                "<li class=\"list-group-item\"><img src=' " + imgPathSrc + " ' width='30px' height='30px'>&nbsp;<span>名称：" + hoveNodeList.namespace + " </span></li>" +
                "<li class=\"list-group-item\">标识：" + hoveNodeList.id + "</li>" +
                "<li class=\"list-group-item\">类型：" + hoveNodeList.typename + "</li>"
                "</ul>";
            $("#divHoverNode").append($ul);


            $('#divHoverNode').css({
                'display': 'block',
                'left': properties.event.offsetX + 15,
                'top': properties.event.offsetY + 15
            });
        });
        network.on('blurNode', function() {
            $("#divHoverNode").hide();
            $("#divHoverNode").empty(); //移除之后清空div
        });

        network.on('blurEdge', function(properties) {
            $("#divHoverNode").hide();
            $("#divHoverNode").empty();
        });
    </script>

</body>

</html>