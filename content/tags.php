<?php
/*
 * The Guardian Open Platform Content API PHP library
 * by Dave Nattriss (dave@natts.com)
 * V1.0 10/03/2009
 */

require_once("content/tag.php");

class GuardianAPI_tags {
	public function __construct($query = "") {
		$this->api_key = GUARDIANAPI_KEY;
		$this->api_url = GUARDIANAPI_URL;
		$this->query = $query;
		$this->tags = array();
	}

	function set_query($query) {
		$this->query = $query;
	}

	function set_start_index($start_index) {
		$this->start_index = $start_index;
	}

	function set_quantity($quantity) {
		$this->quantity = $quantity;
	}

	function get_results() {
		$arguments['api_key'] = $this->api_key;
		if ($this->query) $arguments['q'] = $this->query;
		if ($this->quantity) $arguments['count'] = $this->quantity;
		if ($this->start_index) $arguments['start-index'] = $this->start_index;
		$arguments['format'] = 'json';

		$encoded_parameters = array();
		foreach ($arguments as $name => $value) {
			$encoded_parameters[] = urlencode($name) . '=' . urlencode($value);
		}

		$url = $this->api_url . 'tags?' . implode('&', $encoded_parameters);

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

		foreach ($result['subjects']['tags'] as $tag) {
			$this->tags[] = new GuardianAPI_tag(
				$tag['name'],
				$tag['type'],
				$tag['filter'],
				$tag['webUrl'],
				$tag['apiUrl'],
				$tag['section']
			);
		}
		$this->total = $result['subjects']['count'];
		$this->start_index = $result['subjects']['startIndex'];
		$this->maximum_age = $maximum_age;
	}

	function get_tags() {
		return $this->tags;
	}

	function get_total() {
		return $this->total;
	}

	function get_start_index() {
		return $this->start_index;
	}

	function get_maximum_age() {
		return $this->maximum_age;
	}
}

?>
