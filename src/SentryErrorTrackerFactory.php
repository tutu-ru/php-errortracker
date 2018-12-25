<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

use TutuRu\Config\ConfigContainer;
use TutuRu\ErrorTracker\Sentry\SentryDefaultTeamConfig;
use TutuRu\ErrorTracker\Sentry\SentryClientFactory;
use TutuRu\Metrics\StatsdExporterClientInterface;

class SentryErrorTrackerFactory
{
    public static function create(
        ConfigContainer $config,
        ?string $release = null,
        ?StatsdExporterClientInterface $statsdExporterClient = null
    ): SentryErrorTracker {
        $errorTracker = new SentryErrorTracker(new SentryClientFactory($config, $release));
        $errorTracker->registerConnectionConfig(
            SentryErrorTracker::DEFAULT_TEAM_ID,
            new SentryDefaultTeamConfig($config)
        );
        if (!is_null($statsdExporterClient)) {
            $errorTracker->setStatsdExporterClient($statsdExporterClient);
        }
        return $errorTracker;
    }
}
