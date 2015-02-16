<?php

namespace Radar\Connect;

class Node extends Entity {
  public $title;
  public $body;
  public $category;
  public $topics;
  public $view_url;
  public $edit_url;
  public $status;
  public $created;

  public function __construct($data = array()) {
    $this->set($data);
  }

  public function set($data) {
    parent::set($data);
    if (isset($data['nid'])) {
      $this->drupalId = $data['nid'];
    }
    if (isset($data['vid'])) {
      $this->drupalVersionId = $data['vid'];
    }
  }

  public function apiUri() {
    if (isset($this->apiUri)) {
      return $this->apiUri;
    }
    elseif (isset($this->uuid)) {
      return $this->apiBase . 'node/' . $this->uuid;
    }

    throw new Exception();
  }

  public function getTitle() {
    return $this->title;
  }

  /**
   * Body or Description of the Entity.
   *
   * @return string
   *   Filtered HTML.
   */
  public function getBody() {
    return $this->body['value'];
  }

  public function getBodyRaw() {
    return $this->body;
  }

  public function getCategoriesRaw() {
    return $this->category;
  }

  public function getCategories() {
    $categories = array();
    if (is_array($this->category)) {
      foreach ($this->category as $category) {
        $categories[] = new TaxonomyTerm($category);
      }
    }
    return $categories;
  }

  public function getTopicsRaw() {
    return $this->topics;
  }

  public function getTopics() {
    $topics = array();
    if (is_array($this->topics)) {
      foreach ($this->topic as $topic) {
        $topics[] = new TaxonomyTerm($topic);
      }
    }
    return $topics;
  }

  public function getUrlView() {
    return $this->url;
  }

  public function getUrlEdit() {
    return $this->edit_url;
  }

  public function getStatus() {
    return (bool) $this->status;
  }

  public function getCreated() {
    $created = new DateTime();
    $created->setTimestamp($this->created);
    return $created;
  }

  public function getUpdated() {
    $updated = new DateTime();
    $updated->setTimestamp($this->changed);
    return $updated;
  }
}
