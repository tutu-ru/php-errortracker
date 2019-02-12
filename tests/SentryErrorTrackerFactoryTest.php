<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker;

use TutuRu\ErrorTracker\SentryErrorTracker;
use TutuRu\ErrorTracker\SentryErrorTrackerFactory;

class SentryErrorTrackerFactoryTest extends BaseTest
{
    public function testCreate()
    {
        $tracker = SentryErrorTrackerFactory::create($this->config, 'test', $this->statsdExporterClient);
        $this->assertInstanceOf(SentryErrorTracker::class, $tracker);
    }
}
