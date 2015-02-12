<?php

namespace Radar\Connect;

class Group extends Node {
  public $group_logo;
  public $image;
  public $email;
  public $weblink;
  public $offline;
  public $opening_times;
  public $phone;

  function __construct($data = array()) {
    parent::__construct($data);
    $this->type = 'group';
  }

  public function getGroupLogoRaw() {
    return $this->group_logo->file;
  }

  public function getImageRaw() {
    return $this->image->file;
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

  public function getPhone() {
    return $this->phone;
  }

  public function getPhoneRaw() {
    return $this->phone;
  }

  public function getOpeningTimes() {
    return $this->opening_times->value;
  }

  public function getOpeningTimesRaw() {
    return $this->opening_times;
  }
}
