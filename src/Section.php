<?php

declare(strict_types=1);

namespace Ladoc;

use Ladoc\Index\IndexList;

readonly class Section
{
    /**
     * @param string $name
     * @param IndexList $indexList
     * @param array<string, string> $articles
     */
    public function __construct(
        public string $name,
        public IndexList $indexList,
        public array $articles
    ) {
        //
    }

}
