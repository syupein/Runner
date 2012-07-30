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
}
#map {
	height: 100%;
	width: 100%;
}
.moveButton {
	width: 100%;
	font-size: 25pt;
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
var mArray;
var mCount = 0;
var nowMarker = 0;

function mapInit() {
	var opts = {
		zoom: 6,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		center: new google.maps.LatLng(39, 138)
	};
	map = new google.maps.Map(document.getElementById("map"), opts);
}
function getMarker(x, y) {
	var marker = new google.maps.Marker({
		  position: new google.maps.LatLng(y, x),
		  map: map,
		  icon: iconBack
		});
	return new google.maps.Marker(marker);
}
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
function setChangeNowMarker(index) {
	mArray[nowMarker][0].setIcon(iconBack);
	mArray[index][0].setIcon(iconNow);
	nowMarker = index;
}

function init() {
	mArray = new Array();
	mapInit();
	mArray[mCount++] = new Array(
			getMarker(138, 39),
			138,
			39);
	mArray[mCount++] = new Array(
			getMarker(139, 39),
			139,
			39);
	mArray[mCount++] = new Array(
			getMarker(140, 39),
			140,
			39);
	mArray[mCount++] = new Array(
			getMarker(140, 40),
			140,
			40);
	mArray[0][0].setIcon(iconNow);
	setLine();
	d = document.getElementById('0');
	d.setAttribute("style","box");
}

</script>

</head>



<body onload='init()'>

<table id='work'>
<tr>

<td id='tweetBoxs'>
<input type='button' value='↑' id='upButton' class='moveButton'>
<?php
	require_once 'PutTimeLine.php';
	$d = new PutTimeLine();
	///$d->getTimelineJson("udonTest");
	$d->testTimeLineView("ok");
?>
<input type='button' value='↓' id='downButton' class='moveButton' disabled>
</td>

<td id='map'><noscript>javascriptが使えるブラウザで見てね！</noscript></td>
</tr></table>
</body>
</html>