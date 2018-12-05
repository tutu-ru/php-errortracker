<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker\Stub;

use TutuRu\Config\ConfigContainer;
use TutuRu\ErrorTracker\SentryErrorTracker;
use TutuRu\Metrics\SessionRegistryInterface;

class MemoryErrorTrackerFactory
{
    public static function create(
        ConfigContainer $config,
        ?string $release = null,
        ?SessionRegistryInterface $metricsSessionsRegistry = null
    ): MemoryErrorTracker {
        $errorTracker = new MemoryErrorTracker(new MemorySentryClientFactory($config, $release));
        $errorTracker->registerConnectionConfig(
            SentryErrorTracker::TEAM_CONFIG_SUPPORT,
            new MemoryConnectionConfig('test', 31415, 27182)
        );
        if (!is_null($metricsSessionsRegistry)) {
            $errorTracker->setMetricsSessionRegistry($metricsSessionsRegistry);
        }
        return $errorTracker;
    }
}
