<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use TutuRu\Config\ConfigContainer;
use TutuRu\Metrics\SessionRegistryInterface;
use TutuRu\Tests\Config\JsonConfig\JsonConfigFactory;
use TutuRu\Tests\Metrics\MemoryMetrics\MemoryMetrics;

abstract class BaseTest extends TestCase
{
    /** @var ConfigContainer */
    protected $config;

    /** @var SessionRegistryInterface */
    protected $metricsSessionRegistry;

    /** @var LoggerInterface */
    protected $logger;

    public function setUp()
    {
        parent::setUp();
        $this->config = JsonConfigFactory::createConfig(__DIR__ . '/configs/app.json');
        $this->metricsSessionRegistry = MemoryMetrics::createSessionRegistry($this->config, $this->logger);
        $this->logger = new NullLogger();
    }
}
