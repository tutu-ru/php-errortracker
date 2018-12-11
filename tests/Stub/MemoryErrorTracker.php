<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker\Stub;

use TutuRu\ErrorTracker\SentryErrorTracker;

class MemoryErrorTracker extends SentryErrorTracker
{
    public function getClientFactory(): MemorySentryClientFactory
    {
        return $this->clientFactory;
    }


    /**
     * @return MemorySentryClient[]
     */
    public function getClients(): array
    {
        return $this->getClientFactory()->getCachedClients();
    }


    public function getExceptions()
    {
        $result = [];
        foreach ($this->getClients() as $client) {
            foreach ($client->getExceptions() as $exceptionData) {
                $result[] = $exceptionData;
            }
        }
        return $result;
    }
}
