<?php
/*
 * The Guardian Open Platform Content API PHP library
 * by Dave Nattriss (dave@natts.com)
 * V1.0 10/03/2009
 */

class GuardianAPI_item {
	public function __construct(
		$id,
		$type = "",
		$publication = "",
		$headline = "",
		$standfirst = "",
		$byline = "",
		$section_name = "",
		$trail_text = "",
		$link_text = "",
		$trail_image_url = "",
		$web_url = "",
		$api_url = "",
		$publish_date = "",
		$tags = "",
		$type_specific = "",
		$api_key = GUARDIANAPI_KEY,
		$api_url = GUARDIANAPI_URL
	) {
		$this->api_key = $api_key;
		$this->api_url = $api_url;
		$this->id = $id;
		if ($type) {
			$this->type = $type;
			$this->publication = $publication;
			$this->headline = $headline;
			$this->standfirst = $standfirst;
			$this->byline = $byline;
			$this->section_name = $section_name;
			$this->trail_text = $trail_text;
			$this->link_text = $link_text;
			$this->trail_image_url = $trail_image_url;
			$this->web_url = $web_url;
			$this->api_url = $api_url;
			$this->publish_date = $publish_date;
			$this->tags = array();
			foreach ($tags as $tag) {
				$this->tags[] = new GuardianAPI_tag($tag['name'], $tag['type'], $tag['filter'], $tag['apiUrl'], $tag['webUrl']);
			}
			$this->type_specific = $type_specific;

		} else {
			$this->get_item();
		}		
	}

	function get_item() {
		if ($this->id) {
			$url = $this->api_url . 'item/' . $this->id . '?api_key=' . $this->api_key . '&format=json';
			if (GUARDIANAPI_DEBUG) echo "API URL used: " . $url . "<br />";

			$result = json_decode(file_get_contents($url), TRUE); // decode into an associative array

			$headers = array();
			if (isset($http_response_header)) {
				foreach ($http_response_header as $line) {
					$header = explode(": ", $line);
					$headers[$header[0]] = $header[1];
				}
			}
			$maximum_age = str_replace('max-age=', NULL, $headers['Cache-Control']);
			$item = $result['content'];
			$this->type = $item['type'];
			$this->publication = $item['publication'];
			$this->headline = $item['headline'];
			$this->standfirst = $item['standfirst'];
			$this->byline = $item['byline'];
			$this->section_name = $item['sectionName'];
			$this->trail_text = $item['trailText'];
			$this->link_text = $item['linkText'];
			$this->trail_image_url = $item['trailImage'];
			$this->web_url = $item['webUrl'];
			$this->api_url = $item['apiUrl'];
			$this->publish_date = strtotime($item['publicationDate']);
			$this->tags = array();
			foreach ($item['tags'] as $tag) {
				$this->tags[] = new GuardianAPI_tag($tag['name'], $tag['type'], $tag['filter'], $tag['apiUrl'], $tag['webUrl']);
			}
			$this->type_specific = $item['typeSpecific'];
		}
	}

	function get_id() {
		return $this->id;
	}
}

?>
