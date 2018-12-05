<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker\Sentry;

use TutuRu\Config\ConfigContainer;
use TutuRu\ErrorTracker\ConnectionConfigInterface;

class SentryClientFactory implements SentryClientFactoryInterface
{
    /** @var ConfigContainer */
    private $config;

    /** @var string */
    private $release;


    public function __construct(ConfigContainer $config, ?string $release)
    {
        $this->config = $config;
        $this->release = $release;
    }


    public function create(ConnectionConfigInterface $config): SentryClient
    {
        $options = [
            'timeout'     => $config->getConnectTimeoutSec(),
            'curl_method' => 'sync',
            'name'        => $this->config->getServerHostname(),
        ];
        if ($this->release) {
            $options['release'] = $this->release;
        }

        if (!$config->isVerifySSL()) {
            $options['verify_ssl'] = 0;
        }

        $url = sprintf(
            '%s://%s:%s@%s:%s%s%s',
            $config->getProtocol(),
            $config->getPublicKey(),
            $config->getPrivateKey(),
            $config->getHost(),
            $config->getPort(),
            $config->getPath(),
            $config->getProjectId()
        );

        $client = $this->createNativeClient($url, $options);
        $client->setTimeouts(
            round($config->getConnectTimeoutSec() * 1000),
            round($config->getSelectTimeoutSec() * 1000)
        );

        if (!$config->isVerifySSL()) {
            $client->disableVerifySSL();
        }
        if ($config->useBulkSend()) {
            $client->store_errors_for_bulk_send = true;
        }

        return $client;
    }


    protected function createNativeClient($url, $options): SentryClient
    {
        return new SentryClient($url, $options);
    }
}
