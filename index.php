<?php
$user = "udonTest";
//*
if (isset($_GET['user'])) {
	$user = htmlspecialchars($_GET['user'], ENT_QUOTES);
} else if (isset($_POST['user'])) {
	$user = htmlspecialchars($_POST['user'], ENT_QUOTES);
} else {
	$user = 'udonTest';
}
//*/
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>ゆるゆるまらそん</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="js/jquery.1.7.2.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="css/default.css" rel="stylesheet">

<script src="http://maps.google.com/maps/api/js?v=3&sensor=false"
	type="text/javascript" charset="UTF-8"><noscript>javascriptをオンにして下さい</noscript></script>
<script type="text/javascript" src="http://www.google.com/jsapi"><noscript>javascriptをオンにして下さい</noscript></script>
<script type="text/javascript">google.load("jquery","1.7");</script>
<script type="text/javascript" src="contents.js"><noscript>javascriptをオンにして下さい</noscript></script>


<!--[if lt IE 9]>
<script src="../html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="shortcut icon" href="favicon.ico">
</head>
<!-- ★userid★,ゴール座標経度,ゴール座標緯度,ズーム -->
<body onload="init('<?php echo $user; ?>',135.1942, 34.6859, 14)">
<div class="container">
<div class="row">
<div class="span6">
<div id="titleArea">
<div id="titleleft">
<h1>Live</h1>
</div>
<div id="titleright">
<h1>ゆるゆる マラソン</h1>
</div>
</div>
<div id="mapArea">
<div id="map_canvas" style="width:100%; height:500px" class="span6"></div>
</div>
</div>
<div class="span6" id="streamArea">
<?php
	require_once 'PutTimeLine.php';
	$d = new PutTimeLine();
	/// ★ユーザー名★
	$d->getTimelineJson($user); // 本番用コード
?>
</div>
</div>
</div> <!-- /container -->
<footer>
<div class="container">
<div id="copyright">
<a href="../yuruyuru-kobe.org/index.html">ゆるゆる神戸</a>
</div>
</div>
</footer>
</body>
</html>
