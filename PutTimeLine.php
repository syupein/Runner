<?php

class PutTimeLine
{
	private $count = 0;
	/**
	 * レイアウトの調整を行うための一覧表示
	 * @see
	 */
	public function testTimeLineView($user) {
		$c = 1;
		$this->putline("test", "test".$c++, "http://k.yimg.jp/images/mht/2012/0725_london_soc.png", 135, 34);
		$this->putline("test", "test".$c++, "http://k.yimg.jp/images/mht/2012/0725_london_soc.png", 135, 35);
		$this->putline("test", "test".$c++, "http://k.yimg.jp/images/mht/2012/0725_london_soc.png", 135, 36);
		$this->putline("test", "test".$c++, "http://k.yimg.jp/images/mht/2012/0725_london_soc.png", 135, 37);
		$this->putline("test", "test".$c++, "http://k.yimg.jp/images/mht/2012/0725_london_soc.png", 136, 37);
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

		foreach ($twitterdata["results"] as $data) {
			// -------------------------------------------
			// 画像取得
			// -------------------------------------------
			$media = "存在しないとき用の画像";
			$e = $data['entities'];
			if (isset($e['media'][0]['media_url'])) {
				// twitterの中にあるデフォルトの画像取得
				$media = $e['media'][0]['media_url'];
				echo "test????";
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
			$this->putline($data['from_user'],
					$data['text'],
					$media,
					$data['geo']["coordinates"][1],
					$data['geo']["coordinates"][0]);
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
	function putline($user, $text, $picurl, $posx, $posy) {
		// style='display:none' あとで追加
		echo "<div class='twitBox' id='t".$this->count."' >".
			"<img src=".$picurl." alt='画像の投稿はありません' class='twitImg'>".
			$user.":".$text.
			"経度:".$posx."緯度:".$posy."</div>";
	}
}