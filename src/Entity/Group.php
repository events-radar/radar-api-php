<?php

namespace Radar\Connect\Entity;

class Group extends Node {
  public $group_logo;
  public $image;
  public $email;
  public $link;
  public $offline;
  public $opening_times;
  public $phone;

  function __construct($data = array()) {
    parent::__construct($data);
    $this->type = 'group';
  }

  /**
   * Logo raw data.
   */
  public function getGroupLogoRaw() {
    return $this->group_logo;
  }

  /**
   * Logo file object.
   *
   * @return File|NULL
   */
  public function getGroupLogo() {
    if (!empty($this->group_logo)) {
      return new RadarFile($this->group_logo);
    }
    return NULL;
  }


  /**
   * Raw image entity array.
   */
  public function getImageRaw() {
    return $this->image;
  }

  /**
   * Return image entity object.
   *
   * @return RadarFile|NULL
   */
  public function getImage() {
    if (!empty($this->image->file)) {
      return new RadarFile($this->image);
    }
    return NULL;
  }

  /**
   * Return email.
   *
   * @return string
   */
  public function getEmail() {
    return $this->email;
  }

  public function getEmailRaw() {
    return $this->email;
  }

  /**
   * Return array of url links for the event.
   *
   * @return string[]
   */
  public function getLink() {
    $links = array();
    foreach ($this->link as $link) {
      $links[] = $link['url'];
    }
    return $links;
  }

  /**
   * Return array of array url links for the event.
   *
   * Keyed with 'url', and unused 'attributes'.
   *
   * @return array
   */
  public function getLinkRaw() {
    return $this->link;
  }

  /**
   * Return group locations.
   *
   * @return Location[]
   */
  public function getLocations() {
    $locations = array();
    foreach ($this->offline as $location) {
      $locations[] = new Location($location);
    }
    return $locations;
  }

  public function getLocationsRaw() {
    return $this->offline;
  }

  /**
   * Return phone number.
   *
   * @return string
   */
  public function getPhone() {
    return $this->phone;
  }

  public function getPhoneRaw() {
    return $this->phone;
  }

  /**
   * Free text description of opening times.
   *
   * @return string
   */
  public function getOpeningTimes() {
    return (!empty($this->opening_times['value'])) ? $this->opening_times['value'] : '';
  }

  /**
   * Free text description of opening times.
   *
   * Keyed array with 'value', and 'format' the Radar internal name of the
   * filter format used.
   */
  public function getOpeningTimesRaw() {
    return $this->opening_times;
  }
}
