<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker\Sentry;

use TutuRu\Config\ConfigContainer;
use TutuRu\ErrorTracker\ConnectionConfigInterface;

class SentryDefaultConnectionConfig implements ConnectionConfigInterface
{
    /** @var ConfigContainer */
    private $config;


    public function __construct(ConfigContainer $config)
    {
        $this->config = $config;
    }


    public function getPublicKey()
    {
        return (string)$this->config->getValue('errortracker.public_key', null, true);
    }


    public function getPrivateKey()
    {
        return $this->config->getValue('errortracker.private_key', null, true);
    }


    public function getProjectSlug(): ?string
    {
        return $this->config->getValue('errortracker.project_slug');
    }


    public function getHost()
    {
        return $this->config->getValue('errortracker.host', null, true);
    }


    public function isValid(): bool
    {
        return (bool)$this->config->getValue('errortracker.host');
    }


    public function getPort()
    {
        return $this->config->getValue('errortracker.port', null, true);
    }


    public function getProjectId()
    {
        return $this->config->getValue('errortracker.project_id', null, true);
    }


    public function getPath()
    {
        return $this->config->getValue('errortracker.path', '/');
    }


    public function getProtocol()
    {
        return $this->config->getValue('errortracker.protocol', 'http');
    }


    public function isVerifySSL()
    {
        return $this->config->getValue('errortracker.verify_ssl', 1);
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
