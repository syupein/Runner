<?php

class PutTimeLine
{
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
	 * ストリームを表示する。
	 * @param int $userid
	 */
	public function getStream($userid) {
		set_time_limit(0);
		$user = 'udonTest';
		$password = 'testesudon831';
		$keyword = 'test';
		$userid = $this->getUserId($userid);


		// 動的表示サンプル ob_endなどの理由
		// http://www.enbridge.jp/blog/2007/08/17232951.php

		// バッファを事前に処理
		echo str_pad(" ", 4096);
		ob_end_flush();
		ob_start('mb_output_handler');

		//$stream = fopen("http://{$user}:{$password}@stream.twitter.com/spritzer.json", "r");
		$stream = fopen("https://{$user}:{$password}@stream.twitter.com/1/statuses/filter.json?follow={$userid}&include_entities=true", "r");
		//$stream = fopen("https://{$user}:{$password}@stream.twitter.com/1/statuses/filter.json?track={$keyword}", "r");
		while ($json = @fgets($stream )) {
			$line = json_decode($json,true);
			echo $this->setData($line);
			ob_flush();
			flush();
			sleep(1);
		}
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
		$resdata=@file_get_contents($api_url);
		$twitterdata=json_decode($resdata,true);
		$array = $twitterdata["results"];
		$c = 0;
		$str = "";
		for ($i = count($array)-1; $i >= 0; $i--) {
			$d = $this->setData($array[$i]);
			if ($d != null) {
				$c++;
				$str = $d.$str;
			}
		}
		if ($c == 0){
			echo '<!-- 出力なし -->';
		} else {
			echo $str;
		}
	}
	/**
	 * １行を処理する
	 * @return bool 出力したかどうか
	 * @param  $line 一行
	 */
	public function setData($data) {
		// -------------------------------------------
		// 画像取得
		// -------------------------------------------
		$media = "なし";
		if (!isset($data['entities'])) {
			return false;
		}
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
		$x = $data['geo']["coordinates"][1];
		$y = $data['geo']["coordinates"][0];
		if (!isset($x) || $x == "0" || $x == "" || !isset($y) || $y == "0" || $y == "") {
			return null;
		} else {}

		// idが大きいので文字列での数値比較
		if ( strlen($id) > strlen($this->since) ||
				(strcmp($id , $this->since) > 0 &&
						strlen($id) == strlen($this->since)) ) {
			return $this->putline(
						$data['text'],
						$media,
						$x,
						$y,
						$id
					);
		}
		return null;
	}

	/**
	 * つぶやき一件におけるデータ表示処理
	 * @param string $user
	 * @param string $text
	 * @param string $picurl
	 * @param float $posx
	 * @param float $posy
	 */
	function putline($text, $picurl, $posx, $posy, $id) {
		$str = "<div name='twitBox' class='streamUnit' id='".$this->count."'>";

		if ($picurl !== 'なし') {
			$str .= "<img src='".htmlspecialchars($picurl, ENT_QUOTES)."' alt='不明な画像' class='twitImg'>";
		}
		$str .= "<span class='streamTitle' id='t".$this->count."'>".htmlspecialchars($text, ENT_QUOTES)."</span>";
		$str .= "<input type='hidden' id='x".$this->count."' value='".$posx."'>";
		$str .= "<input type='hidden' id='y".$this->count."' value='".$posy."'>";
		$str .= "<input type='hidden' id='h".$this->count."' value='".$id."'>";
		$str .= "</div>\n";
		$this->count++;
		return $str;
	}

	/**
	 * !消去対象!
	 * 緯度と経度を指定し、一番近い町名まで取得する。
	 * @param int $x
	 * @param int $y
	 */
	function getPosName($x, $y) {
		$api_url="http://geoapi.heartrails.com/api/json?method=searchByGeoLocation&x=".$x."&y=".$y;
		$resdata=@file_get_contents($api_url);
		$data=json_decode($resdata,true);
		$res = "街データ取得失敗";
		if ( isset($data["response"]["location"][0]) ) {
			$d = $data["response"]["location"][0];
			$res = $d['prefecture'].$d['city'].$d['town'];
		}
		return $res;
	}
	/**
	 * スクリーンネームからユーザーIDを取得する
	 * @param string $screan_name
	 * @return string
	 */
	function getUserId($screan_name) {
		$api_url="https://twitter.com/users/show/".$screan_name.".xml";
		$xml =  simplexml_load_file($api_url);
		return $xml->id;
	}
}