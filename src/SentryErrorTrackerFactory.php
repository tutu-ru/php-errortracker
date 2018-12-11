<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

use TutuRu\Config\ConfigContainer;
use TutuRu\ErrorTracker\Sentry\SentryDefaultTeamConfig;
use TutuRu\ErrorTracker\Sentry\SentryClientFactory;

class SentryErrorTrackerFactory
{
    public static function create(ConfigContainer $config, ?string $release = null): SentryErrorTracker
    {
        $errorTracker = new SentryErrorTracker(new SentryClientFactory($config, $release));
        $errorTracker->registerConnectionConfig(
            SentryErrorTracker::DEFAULT_TEAM_ID,
            new SentryDefaultTeamConfig($config)
        );
        return $errorTracker;
    }
}
