<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use TutuRu\Config\ConfigContainer;
use TutuRu\Tests\Config\JsonConfig\JsonConfigFactory;
use TutuRu\Tests\Metrics\MemoryMetricsExporter\MemoryMetricsExporter;
use TutuRu\Tests\Metrics\MemoryMetricsExporter\MemoryMetricsExporterFactory;

abstract class BaseTest extends TestCase
{
    /** @var ConfigContainer */
    protected $config;

    /** @var MemoryMetricsExporter */
    protected $metricsExporter;

    /** @var LoggerInterface */
    protected $logger;

    public function setUp()
    {
        parent::setUp();
        $this->config = JsonConfigFactory::createConfig(__DIR__ . '/configs/app.json');
        $this->logger = new NullLogger();
        $this->metricsExporter = MemoryMetricsExporterFactory::create($this->config, $this->logger);
    }
}
