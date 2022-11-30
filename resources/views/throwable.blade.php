
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>500</title>
<meta name="author" content="yinqi">
<link href="/static/css/bootstrap.min.css" rel="stylesheet">
<link href="/static/css/materialdesignicons.min.css" rel="stylesheet">
<link href="/static/css/style.min.css" rel="stylesheet">
<style>
.error-page {
    height: 100%;
    position: fixed;
    width: 100%;
}
.error-body {
    padding-top: 5%;
}
.error-body h1 {
    font-size: 210px;
    font-weight: 700;
    text-shadow: 4px 4px 0 #f5f6fa, 6px 6px 0 #868e96;
    line-height: 210px;
    color: #868e96;
}
</style>
</head>
  
<body>
<section class="error-page">
  <div class="error-box">
    <div class="error-body text-center">
      <h1>500</h1>
      <h5 class="mb-5 mt-3 text-gray">{{$ERROR}}</h5>
      <a href="/home/dashboard" class="btn btn-primary">Dashboard</a>
    </div>
  </div>
</section>
<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
</body>
</html>