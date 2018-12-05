<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

interface ConnectionConfigInterface
{
    /**
     * @return string
     */
    public function getPublicKey();

    /**
     * @return string
     */
    public function getPrivateKey();

    public function getProjectSlug(): ?string;

    /**
     * @return string
     */
    public function getHost();

    /**
     * @return boolean
     */
    public function isValid();

    /**
     * @return integer
     */
    public function getPort();

    /**
     * @return integer
     */
    public function getProjectId();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getProtocol();

    /**
     * @return int
     */
    public function isVerifySSL();

    /**
     * @return float
     */
    public function getSelectTimeoutSec();

    /**
     * @return float
     */
    public function getConnectTimeoutSec();

    /**
     * @return bool
     */
    public function useBulkSend();
}
