<?php

declare(strict_types=1);

namespace Lo\Index;

use Countable;

class IndexList implements Countable
{
    /** @var ItemList[] */
    private array $items = [];

    public function __construct(
        private ?string $name = null
    ) {
        //
    }

    public function attach(ItemList $itemList): self
    {
        $this->items[] = $itemList;
        return $this;
    }

    public function add(int $index, ItemList $itemList): self
    {
        $this->items[$index] = $itemList;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
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

    /**
     * @return ItemList[]
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(fn ($item) => $item->toArray(), $this->items);
    }

    public function filterByLetter(string $letter): self
    {
        $indexList = new IndexList(
            sprintf('%s | filter: %s', $this->name, strtoupper($letter))
        );

        foreach ($this->items as $index => $item) {
            if (strtolower($item->title[0]) == $letter) {
                $indexList->add($index, $item);
            }
        }

        return $indexList;
    }


    /**
     * @param array<int> $query
     * @return IndexList|ItemList
     */
    public function getNestedItems(array $query): IndexList|ItemList
    {
        $count = count($query);

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
