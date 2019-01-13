<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker\Sentry;

use TutuRu\Config\ConfigInterface;
use TutuRu\ErrorTracker\TeamConfigInterface;

class SentryDefaultTeamConfig implements TeamConfigInterface
{
    /** @var ConfigInterface */
    private $config;


    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }


    public function getPublicKey(): string
    {
        return (string)$this->config->getValue('errortracker.public_key', true);
    }


    public function getPrivateKey(): string
    {
        return (string)$this->config->getValue('errortracker.private_key', true);
    }


    public function getProjectSlug(): ?string
    {
        return $this->config->getValue('errortracker.project_slug');
    }


    public function getHost(): string
    {
        return (string)$this->config->getValue('errortracker.host', true);
    }


    public function isValid(): bool
    {
        return (bool)$this->config->getValue('errortracker.host');
    }


    public function getPort(): int
    {
        return (int)$this->config->getValue('errortracker.port', true);
    }


    public function getProjectId(): int
    {
        return (int)$this->config->getValue('errortracker.project_id', true);
    }


    public function getPath(): string
    {
        return (string)$this->config->getValue('errortracker.path', false, '/');
    }


    public function getProtocol(): string
    {
        return (string)$this->config->getValue('errortracker.protocol', false, 'http');
    }


    public function isVerifySSL(): bool
    {
        return (bool)$this->config->getValue('errortracker.verify_ssl', false, 1);
    }


    public function getSelectTimeoutSec(): float
    {
        return (float)$this->config->getValue('errortracker.select_timeout_sec', true);
    }


    public function getConnectTimeoutSec(): float
    {
        return (float)$this->config->getValue('errortracker.connect_timeout_sec', true);
    }


    public function useBulkSend(): bool
    {
        return (bool)$this->config->getValue('errortracker.bulk_send', false, false);
    }
}
