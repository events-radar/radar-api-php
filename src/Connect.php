<?php

namespace Radar\Connect;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Radar\Connect\Entity\Entity;

class Connect {

  /**
   * @var ClientInterface Guzzle HTTP Client
   */
  protected $client;

  /**
   * @var Cache Doctrine cache for entities.
   */
  protected $cache;

  /**
   * @var string URL of API endpoint.
   */
  public $apiUrl;

  /**
   * @var bool Debug switch true for verbose.
   */
  public $debug;

  /**
   * @var string ISO 639-1 code.
   */
  public $language;

  /**
   * Constructor.
   *
   * @param ClientInterface $client
   *   Guzzle HTTP Client.
   * @param array $configuration
   */
  public function __construct(ClientInterface $client, $configuration = array()) {
    $this->client = $client;
    $this->client->setDefaultOption('headers', array('Accept' => 'application/json'));

    if (!empty($configuration['api_url'])) {
      $this->apiUrl = $configuration['api_url'];
    }
    else {
      $this->apiUrl = 'https://radar.squat.net/api/1.1/';
    }
    $this->debug = !empty($configuration['debug']);
  }

  /**
   * For now also just allow direct access to guzzle itself.
   */
  public function __call($name, $arguments) {
    return call_user_func_array(array($this->client, $name), $arguments);
  }

  /**
   * Set a cache to store entities.
   *
   * @param \Radar\Connect\Cache $cache
   */
  public function setCache(Cache $cache) {
    $this->cache = $cache;
  }

  /**
   * Set, default, language for queries.
   */
  public function setLanguage($langcode) {
    $this->language = $langcode;
  }

  /**
   * Retrieve language code.
   */
  public function getLanguage() {
    if (!empty($this->language)) {
      return $this->language;
    }
    else {
      return 'und';
    }
  }

  /**
   * Compute url for cache storage.
   *
   * Language is for the language requested, not necessarily the language of
   * the entity, as different language requests can return different
   * langage entities (not necessarity corresponding) based on fallback.
   */
  public function cacheUri($entity) {
    return $entity->apiUri() . '?language=' . $this->getLanguage();
  }

  /**
   * Retrieve all fields for single entity.
   *
   * Entities can be partly loaded. Especially when just a reference on
   * an event or group. Use this to retrieve the full entity.
   * If there is a cache set, and the entity is still validly cached
   * this will be returned rather than making a new query.
   *
   * @param Entity $entity
   *   The partly loaded entity.
   *
   * @return Entity
   *   The loaded entity.
   */
  public function retrieveEntity(Entity $entity) {
    $cacheUri = $this->cacheUri($entity);
    if (!empty($this->cache) && $this->cache->contains($cacheUri)) {
      return $this->cache->fetch($cacheUri);
    }
    $request = $this->client->get($entity->apiUri());
    if ($this->getLanguage() != 'und') {
      $query = $request->getQuery();
      $query->set('language', $this->getLanguage());
    }
    $response = $this->retrieve($request);
    $entity = $this->parseResponse($response);
    if (!empty($this->cache)) {
      $this->cache->save($cacheUri, $entity);
    }
    return $entity;
  }

  /**
   * Retrieve all fields for multiple entities.
   *
   * As retrieveEntity(), but making multiple concurrent requests.
   *
   * @param Entity[] $entities
   *   Array of partly loaded entities.
   *
   * @return Entity[]
   *   Array of loaded entities.
   */
  public function retrieveEntityMultiple(&$entities) {
    $cached = array();
    if (!empty($this->cache)) {
      foreach($entities as $key => $entity) {
        if ($this->cache->contains($this->cacheUri($entity))) {
          $cached[] = $this->cache->fetch($this->cacheUri($entity));
          unset($entities[$key]);
        }
      }
    }

    $requests = array();
    foreach ($entities as $entity) {
      $request = $this->client->get($entity->apiUri());
      if ($this->getLanguage() != 'und') {
        $query = $request->getQuery();
        $query->set('language', $this->getLanguage());
      }
      $requests[] = $request;
    }
    $retrieved = $this->retrieveMultiple($requests);

    if (!empty($this->cache)) {
      foreach ($retrieved as $entity) {
        $this->cache->save($this->cacheUri($entity), $entity);
      }
    }

    $entities = array_merge($cached, $retrieved);
    return $entities;
  }

  /**
   * TODO Insert or update an existing Entity.
   */
  public function putEntity(Entity $entity) {

  }

  /**
   * Prepare a request to retrieve events.
   *
   * @see self::retrieve()
   *
   * @param Filter $filter
   * @param array $fields
   *   A list of fields to load. Optional, default is most available fields.
   * @param int $limit
   *   How many events to return.
   * @param array $sort
   *   Optional array ['field_name' => 'order'], where order is ASC or DESC. 
   * @param array $keys
   *   Values for full text search ['search', 'words'] for OR ['search words'] for AND. 
   *
   * @return \Guzzle\Http\Message\Request
   *   Request object to retrieve.
   */
  public function prepareEventsRequest(Filter $filter, $fields = array(), $limit = 500, $sort = array(), $keys = array()) {
    $request = $this->client->get($this->apiUrl . 'search/events.json');
    $query = $request->getQuery();
    $query->set('facets', $filter->getQuery());
    if ($this->getLanguage() != 'und') {
      $query->set('language', $this->getLanguage());
    }
    if (!empty($sort)) {
      $query->set('sort', $sort);
    }
    if (!empty($keys)) {
      $query->set('keys', $keys);
    }
    if (! empty($fields)) {
      // Always retrieve type.
      $fields = array_merge($fields, array('type'));
    }
    else {
      $fields = array(
        'title',
        'type',
        'uuid',
        'nid',
        'og_group_ref',
        'date_time',
        'offline',
        'category',
        'topic',
        'price_category',
        'price',
        'link',
        'phone',
        'body',
        'image',
        'language',
        'created',
        'updated',
        'view_url',
      );
    }
    $query->set('fields', $fields);
    $query->set('limit', $limit);
    return $request;
  }

  /**
   * Prepare a request to retrieve groups.
   *
   * @see self::retrieve()
   *
   * @param Filter $filter
   * @param array $fields
   *   A list of fields to load. Optional, default is most available fields.
   * @param int $limit
   *   How many groups to return.
   * @param array $sort
   *   Optional array ['field_name' => 'order'], where order is ASC or DESC. 
   * @param array $keys
   *   Values for full text search ['search', 'words'] for OR ['search words'] for AND. 
   *
   * @return \Guzzle\Http\Message\Request
   *   Request object to retrieve.
   */
  public function prepareGroupsRequest(Filter $filter, $fields = array(), $limit = 500, $sort = array(), $keys = array()) {
    $request = $this->client->get($this->apiUrl . 'search/groups.json');
    $query = $request->getQuery();
    if ($this->getLanguage() != 'und') {
      $query->set('language', $this->getLanguage());
    }
    $query->set('facets', $filter->getQuery());
    if (!empty($sort)) {
      $query->set('sort', $sort);
    }
    if (!empty($keys)) {
      $query->set('keys', $keys);
    }
    if (! empty($fields)) {
      $fields += array('type');
    }
    else {
      $fields = array(
        'uuid',
        'title',
        'type',
        'nid',
        'category',
        'offline',
        'topic',
        'body',
        'email',
        'weblink',
        'offline',
        'opening_times',
        'phone',
        'view_url',
      );
    }
    $query->set('fields', $fields);
    $query->set('limit', $limit);
    return $request;
  }

  /**
   * Retrieve entities from a prepared request.
   *
   * @param \Guzzle\Http\Message\RequestInterface $request
   *
   * @return Entity[]
   */
  public function retrieve(RequestInterface $request) {
    $response = $this->client->send($request);
    if ($this->debug) {
      var_export($response->getHeaders());
      var_export($response->getBody());
    }
    return $this->parseResponse($response);
  }

  /**
   * Retrieve entities from multiple prepared requests.
   *
   * Results are merged into one entity array.
   *
   * @param \Guzzle\Http\Message\RequestInterface[] $requests
   *
   * @return Entity[]
   */
  public function retrieveMultiple($requests) {
    try {
      $responses = $this->client->send($requests);
    }
    catch (MultiTransferException $e) {
      foreach ($e->getFailedRequests() as $request) {
      }

      foreach ($e->getSuccessfulRequests() as $request) {
      }
    }

    $items = array();
    foreach ($responses as $response) {
      $items = array_merge($items, $this->parseResponse($response));
    }
    return $items;
  }

  /**
   * Parse a response from the client.
   *
   * TODO this doesn't need to be in here.
   */
  public function parseResponse(Response $response) {
    $items = array();

    $content = $response->json();

    if (isset($content['type'])) {
      // Single item response.
      $class = __NAMESPACE__ . '\\Entity\\' . Entity::className($content['type']);
      $content['apiBase'] = $this->apiUrl;
      $items[] = new $class($content);
    }
    else {
      $result = empty($content['result']) ? array() : $content['result'];
      $first_content_item = current($result);
      if (!empty($first_content_item)) {
        // List response, that is non-empty.
        foreach ($result as $key => $item) {
          $class = __NAMESPACE__ . '\\Entity\\' . Entity::className($item['type']);
          $item['apiBase'] = $this->apiUrl;
          $items[] = new $class($item);
        }
      }
      else {
        // Empty response.
        $items = array();
      }
    }

    return $items;
  }

  /**
   * Parse response metadata.
   */
  public function parseResponseMeta(Response $response) {
    $output = [];
    $content = $response->json();

    if (isset($content['count'])) {
      $output['count'] = $content['count'];
    }
    if (isset($content['facets'])) {
      $output['facets'] = $content['facets'];
    }

    return $output;
  }

}
