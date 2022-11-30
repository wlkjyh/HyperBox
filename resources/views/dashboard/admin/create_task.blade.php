<link href="/static/css/bootstrap.min.css" rel="stylesheet">
<link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
<link href="/static/js/bootstrap-datepicker/bootstrap-datepicker3.min.css" rel="stylesheet">
<link href="/static/js/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">
<link href="/static/js/bootstrap-clockpicker/bootstrap-clockpicker.min.css" rel="stylesheet">
<link href="/static/css/style.min.css" rel="stylesheet">
<div class="form-group">
    <label>名称</label>
    <input type="text" class="form-control" id="name" placeholder="">
</div>

<!-- 选择框 -->
<div class="form-group">
    <label>计划时间</label>
    <select class="form-control" id="time" onchange="changeTime()">
        <option value="1">每天</option>
        <option value="2">每周</option>
        <option value="3">每月</option>
        <option value="4">指定时间</option>
    </select>
</div>

<div class="form-group">
    <label>选定时间</label>
    <div id="a">
        <input type="text" class="form-control" data-provide="clockpicker" data-autoclose="true" placeholder="点击开始选时间" />
    </div>

    <div id="b" style="display:none">
        <select class="form-control" id="time">
            <option value="1">周一</option>
            <option value="2">周二</option>
            <option value="3">周三</option>
            <option value="4">周四</option>
            <option value="5">周五</option>
            <option value="5">周六</option>
            <option value="5">周天</option>
        </select>

        <input type="text" class="form-control" data-provide="clockpicker" data-autoclose="true" placeholder="点击开始选时间" />

    </div>

    <div id="c" style="display:none">
        <select class="form-control">
            <?php
            for ($i = 1; $i < 32; $i++) {
                echo '<option value="' . $i . '">' . $i . '号</option>';
            }
            ?>
        </select>

        <input type="text" class="form-control" data-provide="clockpicker" data-autoclose="true" placeholder="点击开始选时间" />

    </div>

    <div id="d" style="display:none">
        <input class="form-control" type="text" data-provide="datetimepicker" name="datetime" placeholder="点击开始选时间" value="" data-side-by-side="true" data-format="YYYY-MM-DD HH:mm" />
    </div>
</div>


<div class="form-group">
    <label>行为</label>
    <!-- select -->
    <select class="form-control" id="action">
        <option value="1">重启实例</option>
        <option value="2">关闭实例</option>
        <option value="3">启动实例</option>
        <option value="4">删除实例</option>
        <option value="5">创建实例备份</option>
        <option value="5">断开卷连接</option>
        <option value="5">同步所有云主机状态</option>
    </select>
</div>



<div class="form-group">
    <label>
        <h3>说明</h3>
    </label><br>
    创建一个计划任务，可以在特定时间完成指定任务。类似于Linux的crontab。
</div>


<button class="btn btn-default ajax-modal-close">关闭</button>
<button id="submit" class="btn btn-primary">提交</button>


<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script type="text/javascript" src="/static/js/popper.min.js"></script>
<script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="/static/js/bootstrap-datepicker/locales/bootstrap-datepicker.zh-CN.min.js"></script>
<script type="text/javascript" src="/static/js/moment.js/moment.min.js"></script>
<script type="text/javascript" src="/static/js/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="/static/js/moment.js/locale/zh-cn.min.js"></script>
<script type="text/javascript" src="/static/js/bootstrap-clockpicker/bootstrap-clockpicker.min.js"></script>
<script type="text/javascript" src="/static/js/main.min.js"></script>

<script>
    function changeTime() {
        var time = $("#time").val();
        if (time == '1') {
            $("#a").show();
            $("#b").hide();
            $("#c").hide();
            $("#d").hide();
        }
        if (time == '2') {
            $("#a").hide();
            $("#b").show();
            $("#c").hide();
            $("#d").hide();
        }
        if (time == '3') {
            $("#a").hide();
            $("#b").hide();
            $("#c").show();
            $("#d").hide();
        }
        if (time == '4') {
            $("#a").hide();
            $("#b").hide();
            $("#c").hide();
            $("#d").show();
        }
    }

    $(document).ready(function() {

    });
</script>