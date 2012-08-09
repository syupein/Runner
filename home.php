<?php
$user = "udonTest";
//*
if (isset($_POST['user'])) {
	$user = htmlspecialchars($_POST['user'], ENT_QUOTES);
}
//*/
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset='UTF-8'>
<link rel="stylesheet" type="text/css" href="./style.css">
<script src="http://maps.google.com/maps/api/js?v=3&sensor=false"
	type="text/javascript" charset="UTF-8"><noscript>javascriptをオンにして下さい</noscript></script>
<script type="text/javascript" src="http://www.google.com/jsapi"><noscript>javascriptをオンにして下さい</noscript></script>
<script type="text/javascript">google.load("jquery","1.7");</script>
<script type="text/javascript" src="gms.js"><noscript>javascriptをオンにして下さい</noscript></script>

</head>

<!-- init()
 * 初期設定、主にテストの犠牲になる。
 * u  : ユーザ名         : 決めてない // 本番時はadapter.php のコメントアウトを直す
 * gx : ゴールのX座標    : 決めてない
 * gy : ゴールのY座標    : 決めてない
 * zd : 初期ズーム量     : 5
 * it : 更新時刻         : 180000(3分)
 * pw : 画像の横幅       : 800px?
 * ph : 画像の縦幅       : 600px?
 * mw : マップの最小横幅 : 400px?
 */
  -->
<body onload="init('<?php echo $user ?>',135.1942, 34.6859, 14, 36000, 800, 600, 400)">
<div id='tweetHead'>
<!-- なにか上に表示したいものがあれば -->
<div id='tweetBoxs'>
<?php
	require_once 'PutTimeLine.php';
	$d = new PutTimeLine();
	$d->ver = 2;
	$d->getTimelineJson($user); // 本番用コード
	//$d->testTimeLineView("ok"); // テスト用コード
?>
</div>

</div>

<div id='map'>
<noscript>javascriptが使えるブラウザで見てね！</noscript>
</div>

</body>
</html>
