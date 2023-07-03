<?php

namespace Lo\Index;

class IndexList
{
    private array $items = [];

    public function attach(ItemList $itemList): self
    {
        $this->items[] = $itemList;

        return $this;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function get(int $index): ItemList
    {
        return $this->items[$index];
    }

    public function all(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return array_map(fn ($item) => $item->toArray(), $this->items);
    }


    public function getNestedItems(array $query): ItemList
    {
        /** @var ItemList $firstElement */
        $firstElement = $this->items[$query[0]];

        if (!$firstElement->hasChildren()) {
            return $firstElement;
        }

        return $firstElement->children->getNestedItems(array_slice($query, 1));
    }
}
