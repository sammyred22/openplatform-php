<?php
/*
 * The Guardian Open Platform Content API PHP library
 * by Dave Nattriss (dave@natts.com)
 * V1.0 10/03/2009
 */

class GuardianAPI_tag {
	public function __construct(
		$name,
		$type,
		$filter,
		$web_url,
		$api_url,
		$section = ""
	) {
		$this->name = $name;
		$this->type = $type;
		$this->filter = $filter;
		$this->web_url = $web_url;
		$this->api_url = $api_url;
		$this->section = $section;
	}

	function get_name() {
		return $this->name;
	}

	function get_type() {
		return $this->type;
	}

	function get_filter() {
		return $this->filter;
	}

	function get_web_url() {
		return $this->web_url;
	}

	function get_api_url() {
		return $this->api_url;
	}

	function get_section() {
		return $this->section;
	}
}

?>