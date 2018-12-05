<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker\Stub;

use TutuRu\ErrorTracker\ConnectionConfigInterface;

class MemoryConnectionConfig implements ConnectionConfigInterface
{
    private $host;
    private $port;
    private $projectId;

    public function __construct(string $host, int $port, int $projectId)
    {
        $this->host = $host;
        $this->port = $port;
        $this->projectId = $projectId;
    }

    public function getPublicKey()
    {
        return 'public';
    }

    public function getPrivateKey()
    {
        return 'private';
    }

    public function getProjectSlug(): ?string
    {
        return null;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function isValid()
    {
        return $this->host !== '' || $this->port !== 0;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getProjectId()
    {
        return $this->projectId;
    }

    public function getPath()
    {
        return '/';
    }

    public function getProtocol()
    {
        return 'http';
    }

    public function isVerifySSL()
    {
        return false;
    }

    public function getSelectTimeoutSec()
    {
        return 0;
    }

    public function getConnectTimeoutSec()
    {
        return 0;
    }

    public function useBulkSend()
    {
        return false;
    }
}
