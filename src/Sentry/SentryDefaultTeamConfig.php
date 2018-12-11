<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker\Sentry;

use TutuRu\Config\ConfigContainer;
use TutuRu\ErrorTracker\TeamConfigInterface;

class SentryDefaultTeamConfig implements TeamConfigInterface
{
    /** @var ConfigContainer */
    private $config;


    public function __construct(ConfigContainer $config)
    {
        $this->config = $config;
    }


    public function getPublicKey(): string
    {
        return (string)$this->config->getValue('errortracker.public_key', null, true);
    }


    public function getPrivateKey(): string
    {
        return (string)$this->config->getValue('errortracker.private_key', null, true);
    }


    public function getProjectSlug(): ?string
    {
        return $this->config->getValue('errortracker.project_slug');
    }


    public function getHost(): string
    {
        return (string)$this->config->getValue('errortracker.host', null, true);
    }


    public function isValid(): bool
    {
        return (bool)$this->config->getValue('errortracker.host');
    }


    public function getPort(): int
    {
        return (int)$this->config->getValue('errortracker.port', null, true);
    }


    public function getProjectId(): int
    {
        return (int)$this->config->getValue('errortracker.project_id', null, true);
    }


    public function getPath(): string
    {
        return (string)$this->config->getValue('errortracker.path', '/');
    }


    public function getProtocol(): string
    {
        return (string)$this->config->getValue('errortracker.protocol', 'http');
    }


    public function isVerifySSL(): bool
    {
        return (bool)$this->config->getValue('errortracker.verify_ssl', 1);
    }


    public function getSelectTimeoutSec(): float
    {
        return (float)$this->config->getValue('errortracker.select_timeout_sec', null, true);
    }


    public function getConnectTimeoutSec(): float
    {
        return (float)$this->config->getValue('errortracker.connect_timeout_sec', null, true);
    }


    public function useBulkSend(): bool
    {
        return (bool)$this->config->getValue('errortracker.bulk_send', false);
    }
}
