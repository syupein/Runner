// ---------------------------------- 設定項目 -------------------------------------------
var user;
var goalX;
var goalY;
var zoomD;
var intervalTime;
var pWidth;
var pHeight;
var mWidth;
// ---------------------------------------------------------------------------------------


/* 後でjsfileに退避 */
// google maps のアイコン
var iconNow  = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=!|FF0000|FFFFFF";
var iconBack = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=|00FFFF|000000";
var iconGoal = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=|FFFF00|000000";
var goalMarker;
var map;
var mArray = new Array();
var mCount = 0;
var timer = false;

/**
 * 初期設定、主にテストの犠牲になる。
 * u  : ユーザ名         : 決めてない
 * gx : ゴールのX座標    : 決めてない
 * gy : ゴールのY座標    : 決めてない
 * zd : 初期ズーム量     : 5
 * it : 更新時刻         : 180000(3分)
 * pw : 画像の横幅       : 800px?
 * ph : 画像の縦幅       : 600px?
 * mw : マップの最小横幅 : 400px?
 */
function init(u, gx, gy, zd, it, pw, ph, mw) {
	user = u;
	goalX = gx;
	goalY = gy;
	zoomD = zd;
	intervalTime = it;
	pWidth = pw;
	pHeight = ph;
	mWidth = mw;
	mapInit();
	mapUpdate();
	mapResize();
	requestFile();
}

/**
 * マップの初期設定。
 */
function mapInit() {
	var opts = {
		zoom: zoomD,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		center: new google.maps.LatLng(goalY, goalX)
	};
	m = document.getElementById("map");
	map = new google.maps.Map(m, opts);
	// ゴールの位置設定
	var marker = new google.maps.Marker({
		  position: new google.maps.LatLng(goalY, goalX),
		  map: map,
		  icon: iconGoal
		});
	goalMarker = new google.maps.Marker(marker);
}
/**
 * 画面の変更のたびにgooglemapの大きさを変更
 */
window.onresize = function () {mapResize();}
function mapResize() {
	var m = document.getElementById("map");
	var pw = pWidth;
	var ph = pHeight;
	var xch = 20;
	var c = 1;
	// 画像サイズの調整
	while (window.innerWidth - pw < mWidth){
		c++;
		pw = pWidth / c;
		ph = pHeight / c;
	}
	// マップサイズの変更
	m.style.height = (window.innerHeight-10) + "px";
	m.style.width  = (window.innerWidth - pw - xch) + "px";
	document.getElementById("tweetHead").style.padding
		 = "0 0 0 "+(window.innerWidth - pw-xch)+"px";
	// 画像サイズの変更
	var css_list = document.styleSheets;
	if (css_list) for (var i = 0; i < css_list.length; i++) {
		var rule_list = (css_list[i].cssRules) ? css_list[i].cssRules : css_list[i].rules;

		for (var ii = 0; ii < rule_list.length; ii++)
		 if (rule_list[ii].selectorText === '.twitImg')
		 with (rule_list[ii].style) {
			width = (pw - xch) + "px";
			height = ph + "px";
		}

	}

}
/**
 * マップのマーカーの生成
 */
function createMarker() {
	var x = document.getElementById("x"+mCount).value;
	var y = document.getElementById("y"+mCount).value;
	var marker = new google.maps.Marker({
		  position: new google.maps.LatLng(y, x),
		  map: map,
		  icon: iconNow
		});
	mArray[mCount++] = new Array(
			new google.maps.Marker(marker),
			x,y);
	// アイコンを戻す。
	mArray[(mCount-2 > 0 ? mCount-2 : 0)][0].setIcon(iconBack);
	// アイコンの位置に地図の座標を合わせる。
	map.panTo(new google.maps.LatLng(y,x));
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
 * マップの更新
 */
function mapUpdate() {
	while (mCount < document.getElementsByName('twitBox').length) {
		createMarker();// mCountの更新が中で行われる。
	}
	setLine();
}

// 元sample
// http://allabout.co.jp/gm/gc/24097/
/**
 * XMLHttpRequestオブジェクト生成
 */
function createHttpRequest(){

  //Win ie用
  if(window.ActiveXObject){
      try {
          //MSXML2以降用
          return new ActiveXObject("Msxml2.XMLHTTP")
      } catch (e) {
          try {
              //旧MSXML用
              return new ActiveXObject("Microsoft.XMLHTTP")
          } catch (e2) {
              return null
          }
       }
  } else if(window.XMLHttpRequest){
      //Win ie以外のXMLHttpRequestオブジェクト実装ブラウザ用
      return new XMLHttpRequest()
  } else {
      return null
  }
}
/**
 * ファイルにアクセスし受信内容を確認します
 */
function requestFile()
{
	if (timer == false) {
		setTimeout(function() {
			timer = true;
			requestFile();
			timer = false;
			requestFile();
		} , intervalTime);
		return;
	}
	//XMLHttpRequestオブジェクト生成
	var httpoj = createHttpRequest();
	var tid = "-1";
	var i;
	for (i = 0; i < mCount; i++) {
		var w = document.getElementById("h"+i).value;
		if (w.length > tid.length || (w > tid && w.length === tid.length)) {
			tid = w;
		}
	}
	var fileName = "adapter.php?st="+ tid +"&count="+ mCount +"&user="+user;
	//open メソッド
	httpoj.open( 'GET' , fileName , true );

	//受信時に起動するイベント
	httpoj.onreadystatechange = function() {
		//readyState値は4で受信完了
		if (httpoj.readyState==4) {
			//レスポンスを取得
			res  = httpoj.responseText;
			// ★ 追加処理 ★
			$(res).prependTo('#tweetBoxs').hide().fadeIn('slow');
			mapUpdate();
		}
	}
	//send メソッド
	httpoj.send( null );
}