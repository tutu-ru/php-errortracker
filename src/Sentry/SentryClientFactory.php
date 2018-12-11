<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker\Sentry;

use TutuRu\Config\ConfigContainer;
use TutuRu\Config\EnvironmentUtils;
use TutuRu\ErrorTracker\TeamConfigInterface;

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


    public function create(TeamConfigInterface $teamConfig): SentryClient
    {
        $options = [
            'timeout'     => $teamConfig->getConnectTimeoutSec(),
            'curl_method' => 'sync',
            'name'        => EnvironmentUtils::getServerHostname(),
        ];
        if ($this->release) {
            $options['release'] = $this->release;
        }

        if (!$teamConfig->isVerifySSL()) {
            $options['verify_ssl'] = 0;
        }

        $url = sprintf(
            '%s://%s:%s@%s:%s%s%s',
            $teamConfig->getProtocol(),
            $teamConfig->getPublicKey(),
            $teamConfig->getPrivateKey(),
            $teamConfig->getHost(),
            $teamConfig->getPort(),
            $teamConfig->getPath(),
            $teamConfig->getProjectId()
        );

        $client = $this->createNativeClient($url, $options);
        $client->setTimeouts(
            round($teamConfig->getConnectTimeoutSec() * 1000),
            round($teamConfig->getSelectTimeoutSec() * 1000)
        );

        if (!$teamConfig->isVerifySSL()) {
            $client->disableVerifySSL();
        }
        if ($teamConfig->useBulkSend()) {
            $client->store_errors_for_bulk_send = true;
        }

        return $client;
    }


    protected function createNativeClient($url, $options): SentryClient
    {
        return new SentryClient($url, $options);
    }
}
