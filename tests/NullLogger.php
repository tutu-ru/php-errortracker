<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker;

use Psr\Log\AbstractLogger;

class NullLogger extends AbstractLogger
{
    public function log($level, $message, array $context = [])
    {
    }
}
