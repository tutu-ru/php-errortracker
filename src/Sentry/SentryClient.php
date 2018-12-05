<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker\Sentry;

class SentryClient extends \Raven_Client
{
    /** @var float */
    private $connectTimeoutSec;

    /** @var float */
    private $receiveTimeoutSec;

    /** @var bool */
    private $verifySSLEnabled = true;


    /**
     * @param float $connectTimeoutSec
     * @param float $receiveTimeoutSec
     */
    public function setTimeouts(float $connectTimeoutSec, float $receiveTimeoutSec)
    {
        $this->connectTimeoutSec = $connectTimeoutSec;
        $this->receiveTimeoutSec = $receiveTimeoutSec;
    }


    public function disableVerifySSL()
    {
        $this->verifySSLEnabled = false;
    }


    // phpcs:disable
    protected function get_curl_options()
    {
        $options = parent::get_curl_options();
        $options[CURLOPT_TIMEOUT_MS] = $this->receiveTimeoutSec;
        $options[CURLOPT_CONNECTTIMEOUT_MS] = $this->connectTimeoutSec;
        /**
         * На некоторых средах два параметра выше не работают без CURLOPT_NOSIGNAL:
         *
         * If onoff is 1, libcurl will not use any functions that install signal handlers or any functions that cause
         * signals to be sent to the process. This option is here to allow multi-threaded unix applications to still
         * set/use all timeout options etc, without risking getting signals.
         *
         * If this option is set and libcurl has been built with the standard name resolver, timeouts will not occur
         * while the name resolve takes place. Consider building libcurl with the c-ares or threaded resolver backends
         * to enable asynchronous DNS lookups, to enable timeouts for name resolves without the use of signals.
         *
         * Setting CURLOPT_NOSIGNAL to 1 makes libcurl NOT ask the system to ignore SIGPIPE signals, which otherwise
         * are sent by the system when trying to send data to a socket which is closed in the other end. libcurl makes
         * an effort to never cause such SIGPIPEs to trigger, but some operating systems have no way to avoid them and
         * even on those that have there are some corner cases when they may still happen, contrary to our desire.
         * In addition, using CURLAUTH_NTLM_WB authentication could cause a SIGCHLD signal to be raised.
         *
         * (c) https://curl.haxx.se/libcurl/c/CURLOPT_NOSIGNAL.html
         */
        $options[CURLOPT_NOSIGNAL] = 1;

        if (!$this->verifySSLEnabled) {
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
        }

        return $options;
    }
    // phpcs:enable
}
