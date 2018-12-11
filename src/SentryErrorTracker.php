<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TutuRu\ErrorTracker\Sentry\SentryClient;
use TutuRu\ErrorTracker\Sentry\SentryClientFactoryInterface;
use TutuRu\Metrics\MetricsAwareInterface;
use TutuRu\Metrics\MetricsAwareTrait;

class SentryErrorTracker implements ErrorTrackerInterface, LoggerAwareInterface, MetricsAwareInterface
{
    use LoggerAwareTrait;
    use MetricsAwareTrait;

    /** Дефолтная команда в Sentry в которую по-умолчанию попадают ошибки */
    const CONNECTION_CONFIG_SUPPORT_TEAM = 'support';

    /** @var SentryClient[] */
    private $clients;

    /** @var ConnectionConfigInterface[] */
    private $connectionConfigs;

    /** @var TagsPreparerInterface */
    private $tagsPreparer = null;

    /** @var SentryClientFactoryInterface */
    protected $clientFactory;


    public function __construct(SentryClientFactoryInterface $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }


    public function send(
        \Throwable $exception,
        array $context = [],
        array $tags = [],
        string $connectionId = null
    ): void {
        $connectionId = $connectionId ?? self::CONNECTION_CONFIG_SUPPORT_TEAM;
        $projectSlug = $this->getConfig($connectionId)->getProjectSlug();
        $statsCollector = new TrackingMetricsCollector($exception, $projectSlug, $this->getMetricsSessionRegistry());
        $statsCollector->startTiming();
        $client = null;
        try {
            if ($client = $this->getClient($connectionId)) {
                if ($exception instanceof \ErrorException) {
                    $statsCollector->setSeverityLabel($client->translateSeverity($exception->getSeverity()));
                }
                $tags = is_null($this->tagsPreparer) ? $tags : $this->tagsPreparer->getPreparedTags($tags);
                $client->extra_context($context);
                $client->captureException($exception, $tags ? ['tags' => $tags] : null);
                if ($error = $client->getLastError()) {
                    $statsCollector->registerProcessingFailure();
                    if (!is_null($this->logger)) {
                        $this->logger->error($error, ['lib' => 'errortracker', 'operation' => 'send']);
                    }
                }
            }
        } catch (\Throwable $e) {
            $statsCollector->registerProcessingFailure();
            if (!is_null($this->logger)) {
                $this->logger->error($e->__toString(), ['lib' => 'errortracker', 'operation' => 'send']);
            }
        }
        if ($client) {
            $client->context->clear();
        }
        $statsCollector->endTiming();
        $statsCollector->save();
    }


    public function sendUnsentErrors(): void
    {
        try {
            if (!($client = $this->getClient(self::CONNECTION_CONFIG_SUPPORT_TEAM))) {
                return;
            }
            $client->sendUnsentErrors();
            $lastError = $client->getLastError();
            if ($lastError && !is_null($this->logger)) {
                $this->logger->error($lastError, ['lib' => 'errortracker', 'operation' => 'send']);
            }
        } catch (\Exception $e) {
            if (!is_null($this->logger)) {
                $this->logger->error($e->__toString(), ['lib' => 'errortracker', 'operation' => 'send']);
            }
        }
    }


    public function registerConnectionConfig(string $connectionId, ConnectionConfigInterface $connectionConfig): void
    {
        $this->connectionConfigs[$connectionId] = $connectionConfig;
    }


    public function setTagsPreparer(TagsPreparerInterface $tagsPreparer): void
    {
        $this->tagsPreparer = $tagsPreparer;
    }


    private function getConfig(string $connectionId): ConnectionConfigInterface
    {
        return $this->connectionConfigs[$connectionId] ?? $this->connectionConfigs[self::CONNECTION_CONFIG_SUPPORT_TEAM];
    }


    private function getClient(string $connectionId): ?SentryClient
    {
        if (empty($this->clients[$connectionId])) {
            $config = $this->getConfig($connectionId);
            if (!$config->isValid()) {
                return null;
            }
            $this->clients[$connectionId] = $this->clientFactory->create($config);
        }
        return $this->clients[$connectionId];
    }
}
