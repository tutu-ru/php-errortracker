<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

use TutuRu\ErrorTracker\Sentry\SentryClient;
use TutuRu\Metrics\MetricsCollector;
use TutuRu\Metrics\MetricType;
use TutuRu\Metrics\SessionRegistryInterface;

class TrackingMetricsCollector extends MetricsCollector
{
    /** @var \Exception */
    private $exception;

    /** @var string */
    private $projectSlug;

    /** @var string */
    private $severityLabel = SentryClient::ERROR;

    /** @var bool */
    private $processingFailed = false;


    public function __construct(\Throwable $exception, ?string $projectSlug, SessionRegistryInterface $metrics)
    {
        $this->exception = $exception;
        $this->projectSlug = $projectSlug ?? 'undefined_project';

        $this->setMetricsSessionRegistry($metrics);
        $this->setStatsdExporterTimersMetricName('error_tracker_processing');
        $this->setStatsdExporterTimersTags(
            [
                'project_slug' => $this->projectSlug,
                'status'       => 'success',
                'severity'     => $this->severityLabel
            ]
        );
    }


    public function registerProcessingFailure()
    {
        $this->processingFailed = true;
        $this->addStatsdExporterTimersTags(['status' => 'failure']);
    }


    public function setSeverityLabel($severityLabel)
    {
        $this->severityLabel = $severityLabel;
        $this->addStatsdExporterTimersTags(['severity' => $severityLabel]);
    }


    protected function saveCustomMetrics(): void
    {
    }


    protected function getTimingKey(): string
    {
        return $this->generatePrefix();
    }


    private function generatePrefix()
    {
        $keyParts = [
            MetricType::TYPE_LOW_LEVEL,
            'error_tracker',
            $this->projectSlug,
            $this->severityLabel,
            'processing',
            $this->processingFailed ? 'failure' : 'success'
        ];

        return $this->glueNamespaces($keyParts);
    }
}
