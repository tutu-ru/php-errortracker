<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

use TutuRu\Config\ConfigInterface;
use TutuRu\ErrorTracker\Sentry\SentryDefaultTeamConfig;
use TutuRu\ErrorTracker\Sentry\SentryClientFactory;
use TutuRu\Metrics\StatsdExporterClientInterface;

class SentryErrorTrackerFactory
{
    public static function create(
        ConfigInterface $config,
        ?string $release = null,
        ?StatsdExporterClientInterface $statsdExporterClient = null
    ): SentryErrorTracker {
        $errorTracker = new SentryErrorTracker(new SentryClientFactory($release));
        $errorTracker->registerTeamConfig(SentryErrorTracker::DEFAULT_TEAM_ID, new SentryDefaultTeamConfig($config));
        if (!is_null($statsdExporterClient)) {
            $errorTracker->setStatsdExporterClient($statsdExporterClient);
        }
        return $errorTracker;
    }
}
