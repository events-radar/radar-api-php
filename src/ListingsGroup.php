<?php

namespace Radar\Connect;

class ListingsGroup extends Node {
  public $group_logo;
  public $email;
  public $link;
  public $offline;
  public $phone;

  function __construct($data = array()) {
    $this->set($data);
    $this->type = 'listings_group';
  }
}
