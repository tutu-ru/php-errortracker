<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use TutuRu\Config\JsonConfig\MutableJsonConfig;
use TutuRu\Tests\Metrics\MemoryStatsdExporter\MemoryStatsdExporterClient;

abstract class BaseTest extends TestCase
{
    /** @var MutableJsonConfig */
    protected $config;

    /** @var MemoryStatsdExporterClient */
    protected $statsdExporterClient;

    /** @var LoggerInterface */
    protected $logger;

    public function setUp()
    {
        parent::setUp();
        $this->config = new MutableJsonConfig(__DIR__ . '/configs/app.json');
        $this->logger = new TestLogger();
        $this->statsdExporterClient = new MemoryStatsdExporterClient("unittest");
    }
}
