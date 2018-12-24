<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TutuRu\ErrorTracker\Sentry\SentryClient;
use TutuRu\ErrorTracker\Sentry\SentryClientFactoryInterface;
use TutuRu\Metrics\MetricAwareInterface;
use TutuRu\Metrics\MetricAwareTrait;

class SentryErrorTracker implements ErrorTrackerInterface, LoggerAwareInterface, MetricAwareInterface
{
    use LoggerAwareTrait;
    use MetricAwareTrait;

    const DEFAULT_TEAM_ID = 'default';

    /** @var SentryClient[] */
    private $clients;

    /** @var TeamConfigInterface[] */
    private $teamConfigs;

    /** @var TagsPreparerInterface */
    private $tagsPreparer = null;

    /** @var SentryClientFactoryInterface */
    protected $clientFactory;


    public function __construct(SentryClientFactoryInterface $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }


    public function send(\Throwable $exception, array $context = [], array $tags = [], string $teamId = null): void
    {
        $teamId = $teamId ?? self::DEFAULT_TEAM_ID;
        $projectSlug = $this->getConfig($teamId)->getProjectSlug();
        $statsCollector = new TrackingMetricsCollector($exception, $projectSlug);
        $statsCollector->setSeverityLabel(SentryClient::ERROR);
        $statsCollector->startTiming();
        $client = null;
        try {
            if ($client = $this->getClient($teamId)) {
                if ($exception instanceof \ErrorException) {
                    $statsCollector->setSeverityLabel($client->translateSeverity($exception->getSeverity()));
                }
                $tags = is_null($this->tagsPreparer) ? $tags : $this->tagsPreparer->getPreparedTags($tags);
                $client->extra_context($context);
                $client->captureException($exception, $tags ? ['tags' => $tags] : null);
                if ($error = $client->getLastError()) {
                    $statsCollector->registerProcessingFailure();
                    if (!is_null($this->logger)) {
                        $this->logger->error($error, $this->getLogContext());
                    }
                }
            }
        } catch (\Throwable $e) {
            $statsCollector->registerProcessingFailure();
            if (!is_null($this->logger)) {
                $this->logger->error($e, $this->getLogContext());
            }
        }
        if ($client) {
            $client->context->clear();
        }
        $statsCollector->endTiming();

        if (!is_null($this->statsdExporterClient)) {
            $statsCollector->sendToStatsdExporter($this->statsdExporterClient);
        }
    }


    public function sendUnsentErrors(): void
    {
        try {
            if (!($client = $this->getClient(self::DEFAULT_TEAM_ID))) {
                return;
            }
            $client->sendUnsentErrors();
            $lastError = $client->getLastError();
            if ($lastError && !is_null($this->logger)) {
                $this->logger->error($lastError, $this->getLogContext());
            }
        } catch (\Exception $e) {
            if (!is_null($this->logger)) {
                $this->logger->error($e, $this->getLogContext());
            }
        }
    }


    public function registerConnectionConfig(string $teamId, TeamConfigInterface $teamConfig): void
    {
        $this->teamConfigs[$teamId] = $teamConfig;
    }


    public function setTagsPreparer(TagsPreparerInterface $tagsPreparer): void
    {
        $this->tagsPreparer = $tagsPreparer;
    }


    private function getConfig(string $teamId): TeamConfigInterface
    {
        return $this->teamConfigs[$teamId] ?? $this->teamConfigs[self::DEFAULT_TEAM_ID];
    }


    private function getClient(string $teamId): ?SentryClient
    {
        if (empty($this->clients[$teamId])) {
            $config = $this->getConfig($teamId);
            if (!$config->isValid()) {
                return null;
            }
            $this->clients[$teamId] = $this->clientFactory->create($config);
        }
        return $this->clients[$teamId];
    }


    private function getLogContext($operation = 'send'): array
    {
        return ['lib' => 'errortracker', 'operation' => $operation];
    }
}
