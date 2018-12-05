<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker\Sentry;

use TutuRu\ErrorTracker\ConnectionConfigInterface;

interface SentryClientFactoryInterface
{
    public function create(ConnectionConfigInterface $connectionConfig): SentryClient;
}
