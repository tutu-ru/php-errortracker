<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker;

use TutuRu\Tests\ErrorTracker\Stub\MemoryTeamConfig;
use TutuRu\Tests\ErrorTracker\Stub\MemoryErrorTrackerFactory;

class ErrorTrackerTest extends BaseTest
{
    public function testSendError()
    {
        $tracker = MemoryErrorTrackerFactory::create($this->config, 'test', $this->metricsSessionRegistry);
        $tracker->setLogger($this->logger);

        $tracker->send(new \Exception('1'));
        $tracker->send(new \Exception('2'), [], ['env' => 'test']);
        $tracker->send(new \ErrorException('3'), [], []);

        $this->assertEquals(
            [
                (object)['exception' => new \Exception("1"), 'tags' => []],
                (object)['exception' => new \Exception("2"), 'tags' => ['env' => 'test']],
                (object)['exception' => new \ErrorException("3"), 'tags' => []],
            ],
            $tracker->getExceptions()
        );
    }


    public function testSendWithTwoConnections()
    {
        $tracker = MemoryErrorTrackerFactory::create($this->config, 'test', $this->metricsSessionRegistry);
        $tracker->setLogger($this->logger);
        $tracker->registerConnectionConfig('second', new MemoryTeamConfig("second", 31416, 27183));

        $tracker->send(new \Exception('1'));
        $tracker->send(new \Exception('2'), [], [], 'second');

        $this->assertCount(2, $tracker->getClients());
        $defaultClient = $tracker->getClients()['http://public:private@test:31415/27182'];
        $secondClient = $tracker->getClients()['http://public:private@second:31416/27183'];
        $this->assertEquals(
            [(object)['exception' => new \Exception("1"), 'tags' => []]],
            $defaultClient->getExceptions()
        );
        $this->assertEquals(
            [(object)['exception' => new \Exception("2"), 'tags' => []]],
            $secondClient->getExceptions()
        );
    }


    public function testSendWithNotValidConnections()
    {
        $tracker = MemoryErrorTrackerFactory::create($this->config, 'test', $this->metricsSessionRegistry);
        $tracker->setLogger($this->logger);
        $tracker->registerConnectionConfig('second', new MemoryTeamConfig("", 0, 27183));

        $tracker->send(new \Exception('1'));
        $tracker->send(new \Exception('2'), [], [], 'second');

        $this->assertCount(1, $tracker->getClients());
        $defaultClient = $tracker->getClients()['http://public:private@test:31415/27182'];
        $this->assertEquals(
            [(object)['exception' => new \Exception("1"), 'tags' => []]],
            $defaultClient->getExceptions()
        );
    }


    public function testSendWithBrokenConnections()
    {
        $tracker = MemoryErrorTrackerFactory::create($this->config, 'test', $this->metricsSessionRegistry);
        $tracker->setLogger($this->logger);
        $tracker->registerConnectionConfig('second', new MemoryTeamConfig("", 31416, 27183));

        $tracker->send(new \Exception('1'));
        $tracker->send(new \Exception('2'), [], [], 'second');

        $this->assertCount(1, $tracker->getClients());
        $defaultClient = $tracker->getClients()['http://public:private@test:31415/27182'];
        $this->assertEquals(
            [(object)['exception' => new \Exception("1"), 'tags' => []]],
            $defaultClient->getExceptions()
        );
    }


    public function testTagsPreparer()
    {
        $tracker = MemoryErrorTrackerFactory::create($this->config, 'test', $this->metricsSessionRegistry);
        $tracker->setLogger($this->logger);
        $tracker->setTagsPreparer(new TagsPreparer());

        $tracker->send(new \Exception('1'));
        $this->assertEquals(
            [(object)['exception' => new \Exception("1"), 'tags' => ['app' => 'phpunit']]],
            $tracker->getExceptions()
        );
    }
}
