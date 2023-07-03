<?php

namespace Lo\Index;

readonly class ItemList
{
    public function __construct(
        public string $title,
        public string $anchor,
        public ?IndexList $children = null
    ) {
        //
    }

    public function hasChildren(): bool
    {
        return $this->children?->count() > 0;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'anchor' => $this->anchor,
            'child' => $this->children?->toArray()
        ];
    }
}
