<?php
declare(strict_types=1);

namespace TutuRu\ErrorTracker;

interface TagsPreparerInterface
{
    public function getPreparedTags(array $tags): array;
}
