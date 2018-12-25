<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

use TutuRu\Metrics\MetricCollector;

class TrackingMetricsCollector extends MetricCollector
{
    /** @var \Exception */
    private $exception;

    /** @var string */
    private $projectSlug;

    /** @var string */
    private $severityLabel;

    /** @var bool */
    private $processingFailed = false;


    public function __construct(\Throwable $exception, ?string $projectSlug)
    {
        $this->exception = $exception;
        $this->projectSlug = $projectSlug ?? 'undefined_project';
    }


    public function registerProcessingFailure()
    {
        $this->processingFailed = true;
    }


    public function setSeverityLabel($severityLabel)
    {
        $this->severityLabel = $severityLabel;
    }


    protected function getTimersMetricName(): string
    {
        return 'error_tracker_processing';
    }


    protected function getTimersMetricTags(): array
    {
        return [
            'project_slug' => $this->projectSlug,
            'severity'     => $this->severityLabel,
            'status'       => $this->processingFailed ? 'failure' : 'success'
        ];
    }
}
