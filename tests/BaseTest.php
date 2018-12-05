<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use TutuRu\Config\ConfigContainer;
use TutuRu\Metrics\SessionRegistryInterface;
use TutuRu\Metrics\UdpMetricsFactory;

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
        $this->config = new ConfigContainer();
        // TODO: memory metrics
        $this->metricsSessionRegistry = UdpMetricsFactory::createSessionRegistry($this->config);
        $this->logger = new NullLogger();
    }
}
