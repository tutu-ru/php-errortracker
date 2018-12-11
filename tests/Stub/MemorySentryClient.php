<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker\Stub;

use TutuRu\ErrorTracker\Sentry\SentryClient;

class MemorySentryClient extends SentryClient
{
    /** @var \Throwable[] */
    private $exceptions = [];


    public function __construct($optionsOrDsn = null, array $options = [])
    {
        parent::__construct($optionsOrDsn, $options);
    }


    public function captureException($exception, $tags = null, $logger = null, $vars = null)
    {
        $obj = new \stdClass();
        $obj->exception = $exception;
        $obj->tags = $tags['tags'] ?? [];
        $this->exceptions[] = $obj;
    }


    /**
     * @return array
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
