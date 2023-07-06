<?php

declare(strict_types=1);

namespace Lo\Index;

use Countable;
use OutOfBoundsException;

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
        return $this->count() === 0;
    }

    public function get(int $index): ItemList
    {
        if (isset($this->items[$index]) === false) {
            throw new OutOfBoundsException(
                sprintf('Index %d does not exist for this section', $index)
            );
        }

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
     * @return ItemList
     */
    public function getNestedItems(array $query): ItemList
    {
        $firstElement = $this->get($query[0]);

        if ($firstElement->hasChildren() === false) {
            return $firstElement;
        }

        $next = array_slice($query, 1);

        if (empty($next) || is_null($firstElement->children)) {
            return $firstElement;
        }

        return $firstElement->children->getNestedItems(array_slice($query, 1));
    }
}
