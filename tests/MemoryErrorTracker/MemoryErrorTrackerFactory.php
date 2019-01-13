<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker\MemoryErrorTracker;

use TutuRu\Config\ConfigInterface;
use TutuRu\ErrorTracker\SentryErrorTracker;
use TutuRu\Metrics\StatsdExporterClientInterface;

class MemoryErrorTrackerFactory
{
    public static function create(
        ConfigInterface $config,
        ?string $release = null,
        ?StatsdExporterClientInterface $statsdExporterClient = null
    ): MemoryErrorTracker {
        $errorTracker = new MemoryErrorTracker(new MemorySentryClientFactory($release));
        $errorTracker->registerConnectionConfig(
            SentryErrorTracker::DEFAULT_TEAM_ID,
            new MemoryTeamConfig('test', 31415, 27182)
        );
        if (!is_null($statsdExporterClient)) {
            $errorTracker->setStatsdExporterClient($statsdExporterClient);
        }
        return $errorTracker;
    }
}
