<?php

namespace Radar\Connect\Entity;

class RadarFile extends Entity {
  public $title;
  public $mime;
  public $size;
  public $url;

  function __construct($data = array()) {
    $this->set($data);
    $this->type = 'file';
  }

  public function set($data) {
    $data = (array) $data;
    parent::set($data);
    if (isset($data['fid'])) {
      $this->drupalId = $data['fid'];
    }
  }

  public function apiUri() {
    if (isset($this->apiUri)) {
      return $this->apiUri;
    }
    elseif (isset($this->uuid)) {
      return $this->apiBase . 'file/' . $this->uuid;
    }

    throw new Exception();
  }

  /**
   * Title is usually filename.
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Mimetype, eg image/jpeg
   */
  public function getImageRaw() {
    return $this->mime;
  }

  /**
   * URL to the file itself.
   *
   * @return string
   */
  public function getUrl() {
    return $this->url;
  }

  /**
   * Size, in bytes.
   *
   * @return int
   */
  public function getSize() {
    return $this->size;
  }

}
