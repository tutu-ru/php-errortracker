<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker\Stub;

use TutuRu\ErrorTracker\TeamConfigInterface;

class MemoryTeamConfig implements TeamConfigInterface
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

    public function getPublicKey(): string
    {
        return 'public';
    }

    public function getPrivateKey(): string
    {
        return 'private';
    }

    public function getProjectSlug(): ?string
    {
        return null;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function isValid(): bool
    {
        return $this->host !== '' || $this->port !== 0;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getPath(): string
    {
        return '/';
    }

    public function getProtocol(): string
    {
        return 'http';
    }

    public function isVerifySSL(): bool
    {
        return false;
    }

    public function getSelectTimeoutSec(): float
    {
        return 0;
    }

    public function getConnectTimeoutSec(): float
    {
        return 0;
    }

    public function useBulkSend(): bool
    {
        return false;
    }
}
