<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

interface TeamConfigInterface
{
    public function getPublicKey(): string;

    public function getPrivateKey(): string;

    public function getProjectSlug(): ?string;

    public function getHost(): string;

    public function isValid(): bool;

    public function getPort(): int;

    public function getProjectId(): int;

    public function getPath(): string;

    public function getProtocol(): string;

    public function isVerifySSL(): bool;

    public function getSelectTimeoutSec(): float;

    public function getConnectTimeoutSec(): float;

    public function useBulkSend(): bool;
}
