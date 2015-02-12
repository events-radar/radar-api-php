<?php

namespace Radar\Connect;

class TaxonomyTerm extends Entity {
  public $name;
  public $description;
  public $node_count;

  public function __construct($data = array()) {
    $this->set($data);
    $this->type = 'taxonomy_term';
  }

  public function set($data) {
    $data = (array) $data;
    parent::set($data);
    if (isset($data['tid'])) {
      $this->drupalId = $data['tid'];
    }
  }

  public function apiUri() {
    if (isset($this->apiUri)) {
      return $this->apiUri;
    }
    elseif (isset($this->uuid)) {
      return API_URL . 'taxonomy_term/' . $this->uuid;
    }

    throw new Exception();
  }

  public function getTitle() {
    return $this->name;
  }

  public function getNodeCount() {
    return $this->node_count;
  }
}
