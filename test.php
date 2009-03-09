<?php
/*
 * The Guardian Open Platform Content API PHP library
 * by Dave Nattriss (dave@natts.com)
 * V1.0 10/03/2009
 */

require_once("conf.php");
require_once("content/search.php");
require_once("content/tag.php");
require_once("content/tags.php");
require_once("content/item.php");

define("GUARDIANAPI_DEBUG", true);

$search = new GuardianAPI_search();
$search->set_query('Obama');
$search->set_after('November 1st 2008');
$search->set_before('November 10th 2008');
$search->set_content_type('article');
$search->set_quantity(20);
$search->set_ordering('date', FALSE);
$search->add_filter('/world/george-bush');

$search->get_results();

echo "Got " .
	(count($search->get_items()) == $search->get_total() ? "all " . $search->get_total() . " items" : count($search->get_items()) .
	" items from the total of " . $search->get_total() . 
	", starting from " . ($search->get_start_index() + 1) . ",") .
	" which should be cached for the next " . $search->get_maximum_age() . " seconds<br />";

if (GUARDIANAPI_DEBUG) var_dump($search->get_items());

$memcache = new Memcache;
$memcache->connect("localhost",11211);

foreach ($search->get_items() as $item) {
	$memcache->set("GuardianAPI-item-" . $item->get_id(), $item, FALSE, $search->get_maximum_age());  // store the item in the memcache, using its ID as part of the key, and setting the cache appropriately
	if (GUARDIANAPI_DEBUG) var_dump($memcache->get("GuardianAPI-item-" . $item->get_id()));
	$last_item_id = $item->get_id();
}

$last_item = new GuardianAPI_item($last_item_id);

echo "<br />The last item cached was this: ";
var_dump($last_item);

?>