<?php

namespace Radar\Connect;

class Event extends Node {
  public $og_group_ref;
  public $date_time;
  public $image;
  public $price;
  public $email;
  public $weblink;
  public $offline;
  public $phone;

  public function __construct($data = array()) {
    parent::__construct($data);
    $this->type = 'event';
  }

  public function set($data) {
    parent::set($data);
    if (isset($data['title_field'])) {
      // @todo always title_field?
      $this->title = $data['title_field'];
    }
  }

  public function getGroupsRaw() {
    return $og_group_ref;
  }

  public function getGroups() {
    $groups = array();
    foreach ($this->og_group_ref as $group) {
      $groups[] = new Group($group);
    }
    return $groups;
  }

  public function getDateRaw() {
    return $this->date_time;
  }

  public function getDates() {
    $dates = array();
    foreach ($this->date_time as $feed_date) {
      $this_date = array();
      $this_date['start'] = new \DateTime($feed_date['time_start']);
      $this_date['end'] = empty($feed_date['time_end']) ? null : new \DateTime($feed_date['time_end']);
      $this_date['rrule'] = $feed_date['rrule']; // Null if not set.
      $dates[] = $this_date;
    }
    return $dates;
  }

  public function getImageRaw() {
    return $this->image->file;
  }

  public function getPrice() {
    return $this->price;
  }

  public function getPriceRaw() {
    return $this->price;
  }

  public function getEmail() {
    return $this->email;
  }

  public function getEmailRaw() {
    return $this->email;
  }

  public function getLink() {
    return $this->weblink->url;
  }

  public function getLinkRaw() {
    return $this->weblink;
  }

  public function getLocationsRaw() {
    return $this->offline;
  }

  public function getLocations() {
    $locations = array();
    foreach ($this->offline as $location) {
      $locations[] = new Location($location);
    }
    return $locations;
  }

  public function getPhone() {
    return $this->phone;
  }

  public function getPhoneRaw() {
    return $this->phone;
  }

}
