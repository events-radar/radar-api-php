<?php

/**
 * @file
 *   Example creating HTML of retrieved radar events based on a filter.
 */

/**
 * Configuration options.
 */
define('CACHE_PATH', '/tmp/radar-cache');


// Load radar client code and get an instance.
require('radar_client.php');

// Shared radar connect client.
$client = radar_client();

// Basic cache for output.
$cache = radar_cache();
// If you want to empty the cache completely
//$cache->flushAll();
// If you want to remove one item like the stored HTML for evets.php
$cache->delete('events.php');

// Add a prefered language for requests. If none is set 'und' is used and
// content is returned in its original language, first language posted..
$client->setLanguage('de');

// Check to see if there is a copy in the cache.
if ($cache->contains('events.php') && $page = $cache->fetch('events.php')) {
  // We can handle expiring data, and serve a stale page.
  print $page['html'];
  // If it's more than an hour old, get a new one.
  if ($page['created'] + 60 * 60 < time()) {
    $html = radar_events_page_html($client);
    $cache->delete('events.php');
  }
}
else {
  // Generate the page and output it.
  $html = radar_events_page_html($client);
  print $html;
}

if (!empty($html)) {
  // Save generated HTML into the cache.
  $page = array('html' => $html, 'created' => time());
  $cache->save('events.php', $page);
}

/**
 * Make HTML page.
 */
function radar_events_page_html($client) {
	$request = radar_prepare_events_request($client);
  $response = $client->retrieveResponse($request);
	$events = $client->parseResponse($response);
	$metadata = $client->parseResponseMeta($response);
  return radar_events_format($client, $events, $metadata);
}

/**
 * Set a filter and retrieve events matching the filter.
 *
 * @param \Radar\Connect\Connect $client
 *   The connect client.
 *
 * @return Radar\Connect\Event[]
 *   Array of radar connect events.
 */
function radar_prepare_events_request(\Radar\Connect\Connect $client) {
  $filter = new \Radar\Connect\Filter;
  $filter->addCity('Berlin');
  // Alternatives:-
  //$filter->addCity('Amsterdam');
  //$filter->addDate(new DateTime('tomorrow'));
	//$filter->addDay();
	//$filter->addCategory('music');
	//Some filters don't have explicit methods to set them so for tags...
	//$filter->add('tag', 'Punk');
	// See docs/classes/Radar.Connect.Filter.html for full list of methods.
	// You can also see all the filter values and their counts in the metadata
	// returned. See the examples at the top of radar_events_format().

  // Get the request.
  // arguments:
  //   $filter - prepared above,
  //   $fields - array of field names to collect, empty for default
  //   $limit - maximum number of events to return.
  $request = $client->prepareEventsRequest($filter, array(), 50);
  return $request;


  // Execute request.
  return $client->retrieve($request);
}

/**
 * Create HTML of an array of events.
 *
 * @param \Radar\Connect\Connect $client
 *   The connect client.
 * @param \Radar\Connect\Event[] $events
 *   Array of Event entities, for example response to events request.
 * @param array $metadata
 *   Array of counts and facets.
 *
 * @return string
 *   The HTML output.
 */
function radar_events_format(\Radar\Connect\Connect $client, array $events, array $metadata) {
  ob_start();
  ob_implicit_flush(TRUE);
  $html = '';

  // Metadata includes the result count.
	print "<p>There are {$metadata['count']} results for the query</p>\n";

	// Retrieve some facets. Summaries of filters you can use
	// in further narrowed queries, and their result counts.
	print '<h1>Forthcoming days</h1><ul>';
	foreach ($metadata['facets']['date'] as $facet) {
    print '<li>' . date('Y-m-d', $facet['formatted']) . " has {$facet['count']} events</li>\n";
	}
	print "</ul>\n";

	// For other factets it's even more convenient. The 'filter' value is also the value you set to filter the query.
  print "<h1>Categories</h1><ul>\n";
	foreach ($metadata['facets']['category'] as $facet) {
    print "<li>{$facet['formatted']} has {$facet['count']} events you could add a filter for them with the \$filter->addCategory('{$facet['filter']}');</li>\n";
	}
	print "<ul>\n";

	// There's no direct method to set the tag filter. They can all be set using the key that is in this array - here tag.
	// So in the example above instead of $filter->addCategory you could have equally $filter->add('category', $facet['filter']);
	print "<h1>Tags</h1><ul>\n";
	foreach ($metadata['facets']['tag'] as $facet) {
	  print "<li>{$facet['formatted']} has {$facet['count']} events you could add a filter for them with the \$filter->add('tag', '{$facet['filter']}');</li>\n";
	}
	print "<ul>\n";

  foreach ($events as $event) {
    // Title and date.
    print '<h1>' . $event->getTitle() . '</h1>';
    print $event->getBody();
    $dates = $event->getDates();
    $date = current($dates);
    print $date['start']->format('Y-m-d H:i:s');

    // The groups are references. If we want to get details about
    // them we actually load the group itself as well.
    $groups = $event->getGroups();
    $groups = $client->retrieveEntityMultiple($groups);
    foreach ($groups as $group) {
	    print '<p><strong>' . $group->getTitle() . '</strong></p>';
	    print '<p>' . var_dump($group->getLink(), true) . ' ' . var_dump($group->getLinkRaw(), true) . '</strong></p>';
    }

    // Just as with the groups the locations are just the references.
    // So we load them here.
    $locations = $event->getLocations();
    $locations = $client->retrieveEntityMultiple($locations);
    foreach ($locations as $location) {
      print '<p>' . $location->getAddress() . '</p>';
    }

    // Yep and the categories, and topics.
    $categories = $event->getCategories();
    $categories = $client->retrieveEntityMultiple($categories);
    $category_names = array();
    foreach ($categories as $category) {
      $category_names[] = $category->getTitle();
    }
    if (! empty($category_names)) {
      print '<p>Categories: ' . implode(', ', $category_names);
    }

    $topics = $event->getTopics();
    $topics = $client->retrieveEntityMultiple($topics);
    $topic_names = array();
    foreach ($topics as $topic) {
      $topic_names[] = $topic->getTitle();
    }
    if (! empty($topic_names)) {
      print '<p>Topics: ' . implode(', ', $topic_names);
    }

    // Outputs the HTML if requested.
    $html .= ob_get_contents();
    ob_clean();
  }

  ob_end_clean();
  return $html;
}
