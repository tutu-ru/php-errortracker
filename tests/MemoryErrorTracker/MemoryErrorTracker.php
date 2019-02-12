<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker\MemoryErrorTracker;

use TutuRu\ErrorTracker\SentryErrorTracker;

class MemoryErrorTracker extends SentryErrorTracker
{
    public function __construct(?string $release = null)
    {
        parent::__construct(new MemorySentryClientFactory($release));
        $this->registerTeamConfig(
            SentryErrorTracker::DEFAULT_TEAM_ID,
            new MemoryTeamConfig('test', 31415, 27182)
        );
    }


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
