<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use TutuRu\Config\ConfigContainer;
use TutuRu\Tests\Config\JsonConfig\JsonConfigFactory;
use TutuRu\Tests\Metrics\MemoryMetricExporter\MemoryMetricExporter;
use TutuRu\Tests\Metrics\MemoryMetricExporter\MemoryMetricExporterFactory;

abstract class BaseTest extends TestCase
{
    /** @var ConfigContainer */
    protected $config;

    /** @var MemoryMetricExporter */
    protected $metricsExporter;

    /** @var LoggerInterface */
    protected $logger;

    public function setUp()
    {
        parent::setUp();
        $this->config = JsonConfigFactory::createConfig(__DIR__ . '/configs/app.json');
        $this->logger = new TestLogger();
        $this->metricsExporter = MemoryMetricExporterFactory::create($this->config);
    }
}
