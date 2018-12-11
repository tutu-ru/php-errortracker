<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker\Stub;

use TutuRu\ErrorTracker\Sentry\SentryClient;
use TutuRu\ErrorTracker\Sentry\SentryClientFactory;
use TutuRu\ErrorTracker\Sentry\SentryClientFactoryInterface;

class MemorySentryClientFactory extends SentryClientFactory implements SentryClientFactoryInterface
{
    private $cachedClients = [];


    protected function createNativeClient($url, $options): SentryClient
    {
        $client = new MemorySentryClient($url, $options);
        $this->cachedClients[$url] = $client;
        return $client;
    }


    public function getCachedClients()
    {
        return $this->cachedClients;
    }
}
