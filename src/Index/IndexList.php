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

    public function isEmpty(): bool
    {
        return empty($this->items);
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


    public function getNestedItems(array $query): IndexList|ItemList
    {
        $count = count($query);

        /** @var ItemList $firstElement */
        $firstElement = $this->items[$query[0]];

        if (!$firstElement->hasChildren()) {
            return $firstElement;
        }

        /** @var IndexList $children */
        $children = $firstElement->children;

        if ($children->count() == 1 && $count == 1) {
            return $children->get(0);
        }

        if($count == 1) {
            return $children;
        }


        return $children->getNestedItems(array_slice($query, 1));
    }
}
