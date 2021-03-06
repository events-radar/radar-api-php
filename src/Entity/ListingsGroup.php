<?php

namespace Radar\Connect\Entity;

 class ListingsGroup extends Node {
  public $group_logo;
  public $email;
  public $link;
  public $offline;
  public $phone;
  public $groups_listed;

   function __construct($data = array()) {
    $this->set($data);
    $this->type = 'listings_group';
  }

  /**
   * TODO not appearing in the API output.
   */
  public function getGroupLogoRaw() {
    return $this->group_logo;
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
   * Which groups are listed for.
   *
   * @return Group[]
   */
  public function getGroupsListed() {
    $groups = array();
    foreach ($this->groups_listed as $group) {
      $groups[] = new Group($group);
    }
    return $groups;
  }

  public function getGroupsListedRaw() {
    return $this->groups_listed;
  }
}
