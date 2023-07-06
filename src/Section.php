<?php

declare(strict_types=1);

namespace Lo;

use Lo\Index\IndexList;

readonly class Section
{
    public function __construct(
        public string $name,
        public IndexList $indexList,
        public array $articles
    ) {
        //
    }

}
