<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker\Sentry;

use TutuRu\ErrorTracker\TeamConfigInterface;

interface SentryClientFactoryInterface
{
    public function create(TeamConfigInterface $teamConfig): SentryClient;
}
