<?php

/**
 * Configuration options.
 */
define('API_URL', "http://new-radar.squat.net/api/1.0/");
define('CACHE_PATH', '/tmp/radar-cache');


// Load radar client code and get an instance.
require('radar_client.php');
$client = radar_client();

// Make a filter.
$filter = new \Radar\Connect\Filter;
$filter->addCity('Berlin');
// Alternatives:-
//$filter->addCity('Amsterdam');
//$filter->addDate(new DateTime('tomorrow'));
//$filter->addDay();

// Get the request.
$request = $client->prepareEventsRequest($filter);

// Execute request.
$events = $client->retrieve($request);

// This would be good for a list of the titles.
print '<ul>';
foreach ($events as $event) {
  print '<li>' . $event->getTitle() . '</li>';
}

// But the search does not load all the group fields.
// So for something more detailed.
// Load all the groups.
$events = $client->retrieveEntityMultiple($events);

foreach ($events as $event) {
  print '<h1>' . $event->getTitle() . '</h1>';
  print $event->getBody();
  $dates = $event->getDates();
  $date = current($dates);
  print $date['start']->format('Y-m-d H:i:s');

  $groups = $client->retrieveEntityMultiple($event->getGroups());
  foreach ($groups as $group) {
    print '<p><strong>' . $group->getTitle() . '</strong></p>';
  }

  $locations = $client->retrieveEntityMultiple($event->getLocations());
  foreach ($locations as $location) {
    print '<p>' . $location->getAddress() . '</p>';
  }

  $categories = $client->retrieveEntityMultiple($event->getCategories());
  $category_names = array();
  foreach ($categories as $category) {
    $category_names[] = $category->getTitle();
  }
  if (! empty($category_names)) {
    print '<p>Categories: ' . implode(', ', $category_names);
  }

  $topics = $client->retrieveEntityMultiple($event->getTopics());
  $topic_names = array();
  foreach ($topics as $topic) {
    $topic_names[] = $topic->getTitle();
  }
  if (! empty($topic_names)) {
    print '<p>Topics: ' . implode(', ', $topic_names);
  }
}
