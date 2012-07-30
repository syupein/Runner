<html>
<head>
<meta charset='UTF-8'>

<style>
/* 後でcssfileに退避 */
body {
	background-color: #00DDDD;
}
.twitImg {
}
th,td {
	font-size: 20pt;
}
.twitText {
	font-size: 30pt;
}
#tweetBoxs {
	float: left;
	height: 100%;
	weight: 50%;
}
#map {
	height: 100%;
	weight: 50%;
	position: fixed !important;
	position: absolute;
}
.dataTable {
	background-color: #00FFFF;
	width: 100%;
}
</style>

<script src="http://maps.google.com/maps/api/js?v=3&sensor=false"
	type="text/javascript" charset="UTF-8"></script>
<script>
/* 後でjsfileに退避 */
// google maps のアイコン
var iconNow  = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=!|FF0000|FFFFFF";
var iconBack = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=|00FFFF|000000";
var map;
var mArray = new Array();
var mCount = 0;
var nowMarker = 0;

/**
 * マップの初期設定。
 */
function mapInit() {
	var opts = {
		zoom: 6,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		center: new google.maps.LatLng(39, 138)
	};
	m = document.getElementById("map");
	map = new google.maps.Map(m, opts);
	m.style.height = (window.innerHeight) + "px";
	m.style.width  = (window.innerWidth / 2) + "px";
	document.getElementById("tweetBoxs").style.padding =
		"0 0 0 "+(window.innerWidth / 2)+"px";
}
/**
 * マップのマーカーの生成
 */
function createMarker(id) {
	x = document.getElementById("x"+id).innerText;
	y = document.getElementById("y"+id).innerText;
	var marker = new google.maps.Marker({
		  position: new google.maps.LatLng(y, x),
		  map: map,
		  icon: iconBack
		});
	mArray[mCount++] = new Array(
			new google.maps.Marker(marker),
			x,y);
}
/**
 * マップのライン生成
 * マーカーの配列があるもの全てにラインを引く
 */
function setLine() {
    var path = new Array(mArray.length);
    for (var i = 0; i < mArray.length; i++) {
		path[i] = new google.maps.LatLng(mArray[i][2], mArray[i][1]);
    }
    // Polylineの初期設定
    var polylineOpts = {
      map: map,
      path: path
    };
    // 直前で作成したPolylineOptionsを利用してPolylineを作成
    // APIで色変えとか出来るなら外に出す。
    var polyline = new google.maps.Polyline(polylineOpts);
}
/**
 * 初期設定、主にテストの犠牲になる。
 */
function init() {
	mapInit();
	var c = 0;
	while (c < document.getElementsByName('twitBox').length) {
		createMarker(c++);
	}
	setLine();
}

</script>

</head>

<body onload='init()'>
<div id='tweetBoxs'>
<?php
	require_once 'PutTimeLine.php';
	$d = new PutTimeLine();
	$d->ver = 2;
	//$d->getTimelineJson("udonTest");
	$d->testTimeLineView("ok");
?>
</div>

<div id='map'>
<noscript>javascriptが使えるブラウザで見てね！</noscript>
</div>

</body>
</html>
