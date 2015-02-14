<?php

namespace Radar\Connect;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

class Connect {

  /**
   * @var ClientInterface Guzzle HTTP Client
   */
  protected $client;

  /**
   * @var string URL of API endpoint.
   */
  public $apiUrl;

  /**
   * @var bool Debug switch true for verbose.
   */
  public $debug;

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
      $this->apiUrl = 'https://new-radar.squat.net/api/1.0/';
    }
    $this->debug = !empty($configuration['debug']);
  }

  /**
   * For now also just allow direct access to guzzle itself.
   */
  public function __call($name, $arguments) {
    return call_user_func_array(array($this->client, $name), $arguments);
  }

  public function retrieveEntity(Entity $entity) {
    $request = $this->client->get($entity->apiUri());
    $entity = $this->parseResponse($response);
    return $entity;
  }

  public function retrieveEntityMultiple(&$entities) {
    $requests = array();
    foreach ($entities as $entity) {
      $requests[] = $this->client->get($entity->apiUri());
    }
    $entities = $this->retrieveMultiple($requests);
    return $entities;
  }

  public function putEntity(Entity $entity) {

  }

  public function prepareEventsRequest(Filter $filter) {
    $request = $this->client->get($this->apiUrl . 'search/events.json');
    $query = $request->getQuery();
    $query->set('facets', $filter->getQuery());
    $query->set('fields', array(
      'title',
      'type',
      'uuid',
      'og_group_ref',
      'date_time',
      'offline',
      'category',
      'topic',
      'price',
      'link',
      'phone',
      'body',
      'image',
      'language',
      'created',
      'updated',
    ));
    return $request;
  }

  public function prepareGroupsRequest(Filter $filter) {
    $request = $this->client->get($this->apiUrl . 'search/groups.json');
    $query = $request->getQuery();
    $query->set('facets', $filter->getQuery());
    $query->set('fields', array('type', 'title', 'uuid'));
    return $request;
  }

  /**
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

  protected function parseResponse(Response $response) {
    $items = array();

    $content = $response->json();
    if (isset($content['type'])) {
      $class = __NAMESPACE__ . '\\' . Entity::className($content['type']);
      $items[] = new $class($content);
    }
    else {
      foreach ($content as $key => $item) {
        $class = __NAMESPACE__ . '\\' . Entity::className($item['type']);
        $items[] = new $class($item);
      }
    }

    return $items;
  }

}
