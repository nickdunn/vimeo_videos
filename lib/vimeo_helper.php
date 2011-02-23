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
	
		$width = intval($clip->getElementsByTagName("width")->item(0)->nodeValue);
		$height = intval($clip->getElementsByTagName("height")->item(0)->nodeValue);
		$ratio = $height / $width;
		
		$thumb_large = array(
			'url' => $clip->getElementsByTagName("thumbnail_large")->item(0)->nodeValue,
			'width' => $width,
			'height' => $height,
		);
		
		$thumb_medium = array(
			'url' => $clip->getElementsByTagName("thumbnail_medium")->item(0)->nodeValue,
			'width' => 200,
			'height' => 200 * $ratio,
		);
		
		$thumb_small = array(
			'url' => $clip->getElementsByTagName("thumbnail_small")->item(0)->nodeValue,
			'width' => 100,
			'height' => 100 * $ratio,
		);
		
		
		// TODO: fetch thumbnail and dimensions
		//$thumbnail = load_image($thumbnail_url);
	
		$data = array(
			'clip_id' => $clip_id,
			'title' => $clip->getElementsByTagName("title")->item(0)->nodeValue,
			'caption' => $clip->getElementsByTagName("caption")->item(0)->nodeValue,
			'thumbnail_url' => $thumb_large['url'],
			'thumbnail_width' => $thumb_large['width'],
			'thumbnail_height' => $thumb_large['height'],
			'thumbnail_medium_url' => $thumb_medium['url'],
			'thumbnail_medium_width' => $thumb_medium['width'],
			'thumbnail_medium_height' => $thumb_medium['height'],
			'thumbnail_small_url' => $thumb_small['url'],
			'thumbnail_small_width' => $thumb_small['width'],
			'thumbnail_small_height' => $thumb_small['height'],
			'width' => $width,
			'height' => $height,
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