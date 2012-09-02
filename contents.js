// ---------------------------------- 設定項目 -------------------------------------------
var userid;
var goalX;
var goalY;
var zoomD;
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
 * u  : ユーザID         : 決めてない
 * gx : ゴールのX座標    : 決めてない
 * gy : ゴールのY座標    : 決めてない
 * zd : 初期ズーム量     : 14
 */
function init(u, gx, gy, zd) {
	userid = u;
	goalX = gx;
	goalY = gy;
	zoomD = zd;
	mapInit();
	mapUpdate();
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
	map = new google.maps.Map(document.getElementById("map_canvas"), opts);
	// ゴールの位置設定
	var marker = new google.maps.Marker({
		  position: new google.maps.LatLng(goalY, goalX),
		  map: map,
		  icon: iconGoal
		});
	goalMarker = new google.maps.Marker(marker);
}

/**
 * マップのマーカーの生成
 */
function createMarker() {
	var mid = mCount++;
	var x = document.getElementById("x"+mid).value;
	var y = document.getElementById("y"+mid).value;
	var marker = new google.maps.Marker({
		  position: new google.maps.LatLng(y, x),
		  map: map,
		  icon: iconNow
		});
	mArray[mid] = new Array(
			new google.maps.Marker(marker),
			x,y);

	// 吹き出し
	var infowin = new google.maps.InfoWindow({content:document.getElementById("t"+mid).innerHTML});
	google.maps.event.addListener(mArray[mid][0], 'mouseover', function(){
		infowin.open(map, marker);
	});
	// mouseoutイベントを取得するListenerを追加
	google.maps.event.addListener(mArray[mid][0], 'mouseout', function(){
		infowin.close();
	});
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
		path: path,
		strokeColor: '#FF0000',
		strokeOpacity: 1.0,
		strokeWeight: 2
	};
	// 直前で作成したPolylineOptionsを利用してPolylineを作成
	// APIで色変えとか出来るなら外に出す。
	var polyline = new google.maps.Polyline(polylineOpts);
}
/**
 * マップの更新
 */
function mapUpdate() {
	var d;
	var i;
	while (mCount < document.getElementsByName('twitBox').length) {
		createMarker();// mCountの更新が中で行われる。
	}
	setLine();
	d = getBigIdx();
	for (i = 0; i < mCount; i++) {
		if (i == d) {
			mArray[i][0].setIcon(iconNow);
			map.panTo(new google.maps.LatLng(
					document.getElementById("y"+i).value,
					document.getElementById("x"+i).value));
		} else {
			mArray[i][0].setIcon(iconBack);
		}
	}
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
	//XMLHttpRequestオブジェクト生成
	var httpobj = createHttpRequest();
	var tid;
	var res;
	var keep;
	var w;
	tid = getBigIdx();
	if (tid != -1) {
		tid = document.getElementById("h"+tid).value;
	}
	var fileName = "adapter.php?st="+ tid +"&count="+ mCount +"&userid="+userid;
	//open メソッド
	httpobj.open( 'GET' , fileName , true );
	keep = "";

	//受信時に起動するイベント
	httpobj.onreadystatechange = function() {
		if(httpobj.readyState == 2){
			// connect完了
		}else if(httpobj.readyState == 3){
			// レスポンスを取得
			// ずっと同じ文章を保持するため、表示したデータを保存して切り捨てに利用
			w = httpobj.responseText;
			res = w.substring(keep.length);
			keep = w;
			// 追加処理
			$(res).prependTo('#streamArea').hide().fadeIn('slow');
			mapUpdate();
		}else if(httpobj.readyState == 4){
			$('#streamArea').prepend('接続が切れました。リロードしてください。').css('color', 'red');
		}
	}
	//send メソッド
	httpobj.send( null );
}
/**
 * ツイートIDが一番大きいデータを持つ添字を返す
 */
function getBigIdx() {
	var i;
	var tid = "-1";
	var idx = -1;
	for (i = 0; i < mCount; i++) {
		var w = document.getElementById("h"+i);
		if (w !== null) {
			w = w.value;
			if (tid == -1) {
				tid = w;
				idx = i;
			} else if (checkStrNumber(w, tid) == 1) {
				tid = w;
				idx = i;
			}
		}
	}
	return idx;
}
/**
 * 数値にあたる文字列を比較する
 * 第一引数が大きいと1
 * 第二引数が大きいと-1
 * 等しいと0
 */
function checkStrNumber(l, r) {
	if (l == r) return 0;
	if (l.length > l.length || (l > r && l.length === r.length)) {
		return 1;
	} else {
		return -1;
	}
}
