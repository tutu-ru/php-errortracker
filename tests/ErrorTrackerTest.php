<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker;

use TutuRu\Tests\ErrorTracker\MemoryErrorTracker\MemoryTeamConfig;
use TutuRu\Tests\ErrorTracker\MemoryErrorTracker\MemoryErrorTrackerFactory;
use TutuRu\Tests\Metrics\MemoryMetricExporter\MemoryMetric;

class ErrorTrackerTest extends BaseTest
{
    public function testSendError()
    {
        $tracker = MemoryErrorTrackerFactory::create($this->config, 'test', $this->metricsExporter);
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

        $this->metricsExporter->save();
        $metrics = $this->metricsExporter->getExportedMetrics();
        $this->assertCount(3, $metrics);
        foreach ($metrics as $metric) {
            $expectedTags = [
                'project_slug' => 'undefined_project',
                'severity'     => 'error',
                'status'       => 'success',
                'app'          => 'unknown',
            ];
            $this->assertMetric($metric, 'error_tracker_processing', $expectedTags);
        }
    }


    public function testSendWithTwoConnections()
    {
        $tracker = MemoryErrorTrackerFactory::create($this->config, 'test', $this->metricsExporter);
        $tracker->setLogger($this->logger);
        $tracker->registerTeamConfig('second', new MemoryTeamConfig("second", 31416, 27183));

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

        $this->metricsExporter->save();
        $metrics = $this->metricsExporter->getExportedMetrics();
        $this->assertCount(2, $metrics);
        foreach ($metrics as $metric) {
            $this->assertMetric($metric, 'error_tracker_processing', $this->defaultTags());
        }
    }


    public function testSendWithNotValidConnections()
    {
        $tracker = MemoryErrorTrackerFactory::create($this->config, 'test', $this->metricsExporter);
        $tracker->setLogger($this->logger);
        $tracker->registerTeamConfig('second', new MemoryTeamConfig("", 0, 27183));

        $tracker->send(new \Exception('1'));
        $tracker->send(new \Exception('2'), [], [], 'second');

        $this->assertCount(1, $tracker->getClients());
        $defaultClient = $tracker->getClients()['http://public:private@test:31415/27182'];
        $this->assertEquals(
            [(object)['exception' => new \Exception("1"), 'tags' => []]],
            $defaultClient->getExceptions()
        );

        $this->metricsExporter->save();
        $metrics = $this->metricsExporter->getExportedMetrics();
        $this->assertCount(2, $metrics);
        foreach ($metrics as $metric) {
            $this->assertMetric($metric, 'error_tracker_processing', $this->defaultTags());
        }
    }


    public function testSendWithBrokenConnections()
    {
        $tracker = MemoryErrorTrackerFactory::create($this->config, 'test', $this->metricsExporter);
        $tracker->setLogger($this->logger);
        $tracker->registerTeamConfig('second', new MemoryTeamConfig("", 31416, 27183));

        $tracker->send(new \Exception('1'));
        $tracker->send(new \Exception('2'), [], [], 'second');

        $this->assertCount(1, $tracker->getClients());
        $defaultClient = $tracker->getClients()['http://public:private@test:31415/27182'];
        $this->assertEquals(
            [(object)['exception' => new \Exception("1"), 'tags' => []]],
            $defaultClient->getExceptions()
        );

        $this->metricsExporter->save();
        $metrics = $this->metricsExporter->getExportedMetrics();
        $this->assertCount(2, $metrics);
        $this->assertMetric($metrics[0], 'error_tracker_processing', $this->defaultTags('success'));
        $this->assertMetric($metrics[1], 'error_tracker_processing', $this->defaultTags('failure'));
    }


    public function testTagsPreparer()
    {
        $tracker = MemoryErrorTrackerFactory::create($this->config, 'test', $this->metricsExporter);
        $tracker->setLogger($this->logger);
        $tracker->setTagsPreparer(new TagsPreparer());

        $tracker->send(new \Exception('1'));
        $this->assertEquals(
            [(object)['exception' => new \Exception("1"), 'tags' => ['app' => 'phpunit']]],
            $tracker->getExceptions()
        );

        $this->metricsExporter->save();
        $metrics = $this->metricsExporter->getExportedMetrics();
        $this->assertCount(1, $metrics);
        foreach ($metrics as $metric) {
            $this->assertMetric($metric, 'error_tracker_processing', $this->defaultTags());
        }
    }


    private function defaultTags($status = 'success')
    {
        return [
            'project_slug' => 'undefined_project',
            'severity'     => 'error',
            'status'       => $status,
            'app'          => 'unknown',
        ];
    }


    private function assertMetric(MemoryMetric $metric, string $expectedName, array $expectedTags)
    {
        $this->assertEquals($expectedName, $metric->getName());
        $this->assertEquals($expectedTags, $metric->getTags());
    }
}
