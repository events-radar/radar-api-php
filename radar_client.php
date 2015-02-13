<?php

require 'vendor/autoload.php';

use Radar\Connect\Connect;
use Radar\Connect\Filter;
use Guzzle\Http\Client;
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\DefaultCacheStorage;

function radar_client() {
  $guzzle = new Client();

  $cachePlugin = new CachePlugin(array(
    'storage' => new DefaultCacheStorage(
      new DoctrineCacheAdapter(
        new FilesystemCache(CACHE_PATH)
      )
    )
  ));

  // Add the cache plugin to the client object
  $guzzle->addSubscriber($cachePlugin);

  return new Connect($guzzle);
}