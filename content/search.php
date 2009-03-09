<?php
/*
 * The Guardian Open Platform Content API PHP library
 * by Dave Nattriss (dave@natts.com)
 * V1.0 10/03/2009
 */

require_once("content/item.php");

class GuardianAPI_search {
	public function __construct($query = "") {
		$this->api_key = GUARDIANAPI_KEY;
		$this->api_url = GUARDIANAPI_URL;
		$this->filters = array();
		$this->query = $query;
		$this->items = array();
	}

	function set_query($query) {
		$this->query = $query;
	}

	function set_before($date) {
		$this->before = strtotime($date);
	}

	function set_after($date) {
		$this->after = strtotime($date);
	}

	function set_content_type($content_type) {
		$this->content_type = $content_type;
	}

	function set_start_index($start_index) {
		$this->start_index = $start_index;
	}

	function set_quantity($quantity) {
		$this->quantity = $quantity;
	}

	function set_ordering($field, $newest = TRUE) {
		if ($field == 'date') $this->order_by_date = ($newest ? "" : "asc");
	}

	function add_filter($value) {
		if (isset($this->filters) && !in_array($value, $this->filters)) {
			$this->filters[] = $value;
		}
	}

	function remove_filter($value) {
		$this->filters = array_values(array_diff($this->filters, array($value)));
	}

	function get_results() {
		$arguments['api_key'] = $this->api_key;
		if ($this->query) $arguments['q'] = $this->query;
		if ($this->before) $arguments['before'] = date("Ymd", $this->before);
		if ($this->after) $arguments['after'] = date("Ymd", $this->after);
		if ($this->content_type) $arguments['content-type'] = $this->content_type;
		if ($this->quantity) $arguments['count'] = $this->quantity;
		if ($this->start_index) $arguments['start-index'] = $this->start_index;
		if ($this->order_by_date) $arguments['order-by-date'] = $this->order_by_date;
		if ($this->filters) $arguments['filter'] = implode('&filter=', $this->filters);
		$arguments['format'] = 'json';

		$encoded_parameters = array();
		foreach ($arguments as $name => $value) {
			$encoded_parameters[] = urlencode($name) . '=' . urlencode($value);
		}

		$url = $this->api_url . 'search?' . implode('&', $encoded_parameters);

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

		foreach ($result['search']['results'] as $item) {
			$this->items[] = new GuardianAPI_item(
				$item['id'],
				$item['type'],
				$item['publication'],
				$item['headline'],
				$item['standfirst'],
				$item['byline'],
				$item['sectionName'],
				$item['trailText'],
				$item['linkText'],
				$item['trailImage'],
				$item['webUrl'],
				$item['apiUrl'],
				$item['publicationDate'],
				$item['tags'],
				$item['typeSpecific']
			);
		}
		$this->total = $result['search']['count'];
		$this->start_index = $result['search']['startIndex'];
		$this->maximum_age = $maximum_age;
	}

	function get_items() {
		return $this->items;
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
