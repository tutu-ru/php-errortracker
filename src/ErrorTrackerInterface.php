<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

interface ErrorTrackerInterface
{
    public function send(\Throwable $exception, array $context = [], array $tags = [], string $teamId = null): void;

    public function sendUnsentErrors(): void;

    public function registerConnectionConfig(string $teamId, TeamConfigInterface $teamConfig): void;

    public function setTagsPreparer(TagsPreparerInterface $tagsPreparer): void;
}
