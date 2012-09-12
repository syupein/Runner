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

		// 動的表示サンプル ob_endなどの理由
		// http://www.enbridge.jp/blog/2007/08/17232951.php

		// バッファを事前に処理
		echo str_pad(" ", 4096);
		ob_end_flush();
		ob_start('mb_output_handler');

		//$stream = fopen("http://{$user}:{$password}@stream.twitter.com/spritzer.json", "r");
		$stream = fopen("https://{$user}:{$password}@stream.twitter.com/1/statuses/filter.json?follow={$userid}&include_entities=true", "r");
		//$stream = fopen("https://{$user}:{$password}@stream.twitter.com/1/statuses/filter.json?track={$keyword}", "r");
		while ($json = fgets($stream )) {
			$line = json_decode($json,true);
			$this->setData($line);
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
		$resdata=file_get_contents($api_url);
		$twitterdata=json_decode($resdata,true);
		$c = 0;
		foreach ($twitterdata["results"] as $data) {
			if ($this->setData($data)) {
				$c++;
			}
		}
		if ($c == 0){
			echo '<!-- 出力なし -->';
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

		if (isset($data['geo']["coordinates"][1]) && isset($data['geo']["coordinates"][0])) {
			$x = $data['geo']["coordinates"][1];
			$y = $data['geo']["coordinates"][0];
		} else {
			return;
		}

		// idが大きいので文字列での数値比較
		if ( strlen($id) > strlen($this->since) ||
				(strcmp($id , $this->since) > 0 &&
						strlen($id) == strlen($this->since)) ) {
			$this->putline(
					$data['text'],
					$media,
					$x,
					$y,
					$id);
			return true;
		}
		return false;
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
		echo "<div name='twitBox' class='streamUnit' id='".$this->count."'>";

		if ($picurl !== 'なし') {
			echo "<img src='".htmlspecialchars($picurl, ENT_QUOTES)."' alt='不明な画像' class='twitImg'>";
		}
		echo "<span class='streamTitle' id='t".$this->count."'>".htmlspecialchars($text, ENT_QUOTES)."</span>";
		echo "<input type='hidden' id='x".$this->count."' value='".$posx."'>";
		echo "<input type='hidden' id='y".$this->count."' value='".$posy."'>";
		echo "<input type='hidden' id='h".$this->count."' value='".$id."'>";
		echo "</div>";

		$this->count++;
	}

	/**
	 * !消去対象!
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