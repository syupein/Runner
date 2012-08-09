<?php

class PutTimeLine
{
	public $ver = 2; // testVerCode
	/**
	 * 現在の要素数, 配列を作るときなど、idを出力するときなど
	 * @var int
	 */
	private $count = 0;
	/**
	 * 現在の出力した回数。
	 * @var int
	 */
	private $since = "0";

	/**
	 * スタート位置のtweetIDを設定する
	 * @param int $id
	 */
	public function setStart($id) {
		$this->since = $id;
	}
	/**
	 * 今まで表示した回数の設定
	 * @param int $count
	 */
	public function setCount($count) {
		$this->count = $count;
	}
	/**
	 * レイアウトの調整を行うための一覧表示
	 * @see
	 */
	public function testTimeLineView($user) {
		$c = 1;
		$this->putline("test", "うわっ...私のかつおぶし、少なすぎ...？", "http://idea.anikipedia.com/image/upim/1319021260.jpg", 135, 34, 190);
		$this->putline("test", "test".$c++, "http://k.yimg.jp/images/mht/2012/0725_london_soc.png", 135, 35, 30);
		$this->putline("test", "test".$c++, "http://k.yimg.jp/images/mht/2012/0725_london_soc.png", 135, 36, 24);
		$this->putline("test", "test".$c++, "http://k.yimg.jp/images/mht/2012/0725_london_soc.png", 135, 37, 21);
		$this->putline("test", "test".$c++, "http://k.yimg.jp/images/mht/2012/0725_london_soc.png", 136, 37, 20);
		$this->putline("test", "にゃっにゃんだと！", "http://idea.anikipedia.com/image/upim/1319021260.jpg", 137, 37, 10);
	}
	/**
	 * レイアウトの調整を行うための追加処理
	 * @see
	 */
	public function testAddRealTime() {
		$c = 1;
		$this->putline("test",
				"ペロッ…これは青酸カリ！！！",
				"http://idea.anikipedia.com/image/upim/1319021260.jpg",
				135+$this->count,
				34+$this->count,
				$this->count+1);
	}
	/**
	 * json形式で出力を行う。
	 * つぶやき一件のデータ処理はputline関数で行う。
	 * 使用制限有り 表示だけなら別の関数使用すること
	 * @see putline
	 * @param string $user
	 */
	public function getTimelineJson($user) {
		// -------------------------------------------
		// データ取得
		// -------------------------------------------
		// 	q=from          : ユーザー名
		// rpp              : つぶやきを返す個数を指定
		// include_entities : エンティティを返すかどうかのオプション->画像の取得に使用
		$api_url="http://search.twitter.com/search.json".
				"?q=from:".$user."&rpp=100&include_entities=true";
		$resdata=file_get_contents($api_url);
		$twitterdata=json_decode($resdata,true);
		$count = 0;
		foreach ($twitterdata["results"] as $data) {
			// -------------------------------------------
			// 画像取得
			// -------------------------------------------
			$media = "なし";
			$e = $data['entities'];
			if (isset($e['media'][0]['media_url'])) {
				// twitterの中にあるデフォルトの画像取得
				$media = $e['media'][0]['media_url'];
			}
			if (isset($e['urls'][0]['display_url'])) {
				// twipic 画像のフルサイズを取得するものを使用しているが、
				// 存在しないAPIなので使用はあまりしないこと。
				$w = $e['urls'][0]['display_url'];
				if (strpos($w,"twitpic.com/") == 0) {
					$media  = "http://twitpic.com/show/full/";
					$media .= substr($w, strlen("twitpic.com/"));
				}
			}
			// -------------------------------------------
			// 表示
			// -------------------------------------------
			$id = $data['id_str'];

			if (isset($data['geo']["coordinates"][1]) && isset($data['geo']["coordinates"][0])) {
				$x = $data['geo']["coordinates"][1];
				$y = $data['geo']["coordinates"][0];
			} else {
				$x = 0;$y = 0;
			}

			// idが大きいので文字列での数値比較
			if ( strlen($id) > strlen($this->since) ||
					(strcmp($id , $this->since) > 0 &&
							strlen($id) == strlen($this->since)) ) {
				$count++;
				$this->putline($data['from_user'],
					$data['text'],
					$media,
					$x,
					$y,
					$id);
			}
		}
		if ($count == 0){
			echo '<!-- 出力なし -->';
		}
	}

	/**
	 * つぶやき一件におけるデータの処理
	 * @param string $user
	 * @param string $text
	 * @param string $picurl
	 * @param float $posx
	 * @param float $posy
	 */
	function putline($user, $text, $picurl, $posx, $posy, $id) {
		$p = $this->getPosName($posx, $posy);
		if ($this->ver == 1) {
			echo "<div class='twitBox' id='".$this->count."' style='display:none'>";
			if ($picurl !== 'なし') {
				echo "<img src='".$picurl."' alt='画像の投稿はありません' class='twitImg'>";
			}

			echo "<table class='dataTable'><tr><th>名前</td><td>".$user."</td>";
			echo "<td rowspan=2 class='twitText' id='t".$this->count."'>".$text."</td></tr>";
			echo "<tr><td colspan=2>".$p."</td></tr>";
			echo "<input type='hidden' id='x".$this->count."' value='".$posx."'>";
			echo "<input type='hidden' id='y".$this->count."' value='".$posy."'>";
			echo "<input type='hidden' id='h".$this->count."' value='".$id."'>";
			echo "</table></div>";
		} else if ($this->ver == 2) {
			echo "<div name='twitBox' class='twitBox' id='".$this->count."'>";

			if ($picurl !== 'なし') {
				echo "<img src='".$picurl."' alt='画像の投稿はありません' class='twitImg'>";
			}

			echo "<table class='dataTable'><tr><th>名前</td><td>".$user."</td>";
			echo "<td rowspan=2 class='twitText' id='t".$this->count."'>".$text."</td></tr>";
			echo "<tr><td colspan=2>".$p."</td></tr>";
			echo "<input type='hidden' id='x".$this->count."' value='".$posx."'>";
			echo "<input type='hidden' id='y".$this->count."' value='".$posy."'>";
			echo "<input type='hidden' id='h".$this->count."' value='".$id."'>";
			echo "</table></div>";
		}
		$this->count++;
	}

	/**
	 * 緯度と経度を指定し、一番近い町名まで取得する。
	 * @param int $x
	 * @param int $y
	 */
	function getPosName($x, $y) {
		$api_url="http://geoapi.heartrails.com/api/json?method=searchByGeoLocation&x=".$x."&y=".$y;
		$resdata=file_get_contents($api_url);
		$data=json_decode($resdata,true);
		$res = "街データ取得失敗";
		if ( isset($data["response"]["location"][0]) ) {
			$d = $data["response"]["location"][0];
			$res = $d['prefecture'].$d['city'].$d['town'];
		}
		return $res;
	}
}