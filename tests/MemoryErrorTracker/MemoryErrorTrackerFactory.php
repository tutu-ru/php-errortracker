<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker\MemoryErrorTracker;

use TutuRu\Config\ConfigContainer;
use TutuRu\ErrorTracker\SentryErrorTracker;
use TutuRu\Metrics\MetricsExporterInterface;

class MemoryErrorTrackerFactory
{
    public static function create(
        ConfigContainer $config,
        ?string $release = null,
        ?MetricsExporterInterface $metricsExporter = null
    ): MemoryErrorTracker {
        $errorTracker = new MemoryErrorTracker(new MemorySentryClientFactory($config, $release));
        $errorTracker->registerConnectionConfig(
            SentryErrorTracker::DEFAULT_TEAM_ID,
            new MemoryTeamConfig('test', 31415, 27182)
        );
        if (!is_null($metricsExporter)) {
            $errorTracker->setMetricsExporter($metricsExporter);
        }
        return $errorTracker;
    }
}
