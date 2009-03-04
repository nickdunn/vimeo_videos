<?php

Class VimeoHelper {

	public static function getClipId($data) {
		
		if (preg_match('/(.*)vimeo.com\/(?<id>[0-9]+)/i', $data, $match)) {
			return $match['id']; // TODO: test regex
		} else {
			return $data;
		}
		
	}
	
	public static function getClipXML($clip_id) {
		
		return DomDocument::load('http://vimeo.com/api/clip/' . $clip_id . '.xml');
		
	}

	public static function getClipInfo($clip_id) {
	
		$clip = VimeoHelper::getClipXML($clip_id);
		if (!$clip) return;
	
		$thumbnail_url = $clip->getElementsByTagName("thumbnail_large")->item(0)->nodeValue;
		// TODO: fetch thumbnail and dimensions
		//$thumbnail = load_image($thumbnail_url);
	
		$data = array(
			'clip_id' => $clip_id,
			'title' => $clip->getElementsByTagName("title")->item(0)->nodeValue,
			'caption' => $clip->getElementsByTagName("caption")->item(0)->nodeValue,
			'thumbnail_url' => $thumbnail_url,
			'thumbnail_width' => 100,
			'thumbnail_height' => 100,
			'width' => $clip->getElementsByTagName("width")->item(0)->nodeValue,
			'height' => $clip->getElementsByTagName("height")->item(0)->nodeValue,
			'duration' => $clip->getElementsByTagName("duration")->item(0)->nodeValue,
			'plays' => $clip->getElementsByTagName("stats_number_of_plays")->item(0)->nodeValue,
			'user_name' => $clip->getElementsByTagName("user_name")->item(0)->nodeValue,
			'user_url' => $clip->getElementsByTagName("user_url")->item(0)->nodeValue,
			'last_updated' => time()
		);
	
		return $data;
	
	}

	public static function updateClipInfo($clip_id, $field_id, $entry_id, $database) {
		
		$data = VimeoHelper::getClipInfo($clip_id);
		if (!$data) return;
		
		$database->update($data, "sym_entries_data_{$field_id}", "entry_id={$entry_id}");
		return $data;
		
	}

}

?>