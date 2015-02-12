<?php

namespace Radar\Connect;
use phayes\geoPHP\geoPHP;

class Location extends Entity {
  public $title;
  public $address;
  public $directions;
  public $map;
  public $timezone;

  public function __construct($data = array()) {
    $this->set($data);
    $this->type = 'location';
  }

  public function set($data) {
    $data = (array) $data;
    parent::set($data);
    if (isset($data['id'])) {
      $this->drupalId = $data['id'];
    }
  }

  public function apiUri() {
    if (isset($this->apiUri)) {
      return $this->apiUri;
    }
    elseif (isset($this->uuid)) {
      return API_URL . 'location/' . $this->uuid;
    }

    throw new Exception();
  }

  public function getTitle() {
    return $this->title;
  }

  /**
   * country - two letter code
   * name_line - locally known as
   * first_name: -
   * last_name: -
   * organisation_name: -
   * administrative_area: -
   * sub_administrative_area: -
   * locality: city name
   * dependent_locality: -
   * postal_code: postcode
   * thoroughfare: street
   * premise: -
   */
  public function getAddressRaw() {
    return $this->address;
  }

  public function getAddress($include = array('name_line', 'thoroughfare', 'locality'), $seperator = ', ') {
    $address = array();
    foreach ($include as $part) {
      if (! empty($this->address->$part)) {
        $address[] = $this->address->$part;
      }
    }
    return implode($seperator, $address);
  }

  public function getDirectionsRaw() {
    return $this->directions;
  }

  public function getDirections() {
    return $this->directions['value'];
  }

  public function getLocationRaw() {
    return $this->map;
  }

  public function getLocation() {
    return new geoPHP($this->map->geom);
  }
}
