<?php
declare(strict_types=1);

namespace TutuRu\Tests\ErrorTracker;

use TutuRu\ErrorTracker\TagsPreparerInterface;

class TagsPreparer implements TagsPreparerInterface
{
    public function getPreparedTags(array $tags): array
    {
        return array_merge($tags, ['app' => 'phpunit']);
    }
}
