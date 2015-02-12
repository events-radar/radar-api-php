<?php

namespace Radar\Connect;

abstract class Entity {

  public $drupalId;
  public $drupalVersionId;
  public $uuid;
  public $vuuid;
  public $type;
  protected $apiUri;

  /**
   * @todo automatic class mappings from available.
   */
  static function className($type) {
    $classes = array(
      'node' => 'Node',
      'group' => 'Group',
      'event' => 'Event',
      'category' => 'Category',
      'listings_group' => 'ListingsGroup',
      'location' => 'Location',
      'taxonomy_term' => 'TaxonomyTerm',
      'category' => 'TaxonomyTerm',
      'topic' => 'TaxonomyTerm',
    );
    return $classes[$type];
  }

  abstract public function __construct($data = array());

  public function set($data) {
    foreach ($this as $key => $value) {
      if (isset($data[$key])) {
        $this->$key = $data[$key];
      }
    }
    if (isset($data['uri'])) {
      $this->apiUri = $data['uri'];
    }
  }

  abstract function apiUri();

  public function getUuid() {
    return $this->uuid;
  }

  public function getVuuid() {
    return $this->vuuid;
  }

  public function getInternalId() {
    return $this->drupalId;
  }

  public function getInternalVid() {
    return $this->drualVersionId;
  }
}
