<?php

namespace Alcaeus\BsonPerformanceTests\Document;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use MongoDB\BSON\ArrayList;
use OutOfBoundsException;
use Traversable;
use function array_key_exists;
use function sprintf;

class LazyBSONArray implements ArrayAccess, Countable, IteratorAggregate
{
    private array $items = [];
    private bool $initialized = false;

    public function __construct(
        private readonly ArrayList $arrayList,
        private readonly ?Closure  $initializer = null,
    ) {
    }

    public function getIterator(): Traversable
    {
        $this->initialize();

        return new ArrayIterator($this->items);
    }

    public function toArray(): array
    {
        $this->initialize();

        // Todo: return copy?
        return $this->items;
    }

    public function offsetExists(mixed $offset): bool
    {
        return
            // Array key may exist
            array_key_exists($offset, $this->items) ||
            // If collection was initialised, no need to check underlying BSON
            (!$this->initialized && $this->arrayList->has($offset));
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (array_key_exists($offset, $this->items)) {
            return $this->items[$offset];
        }

        if ($this->initialized || ! $this->arrayList->has($offset)) {
            throw new OutOfBoundsException(sprintf('Did not find array key %d.', $offset));
        }

        $initializer = $this->initializer;
        $value = $this->arrayList->get($offset);

        $this->items[$offset] = $initializer ? $initializer($value) : $value;

        return $this->items[$offset];
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException('LazyBSONArray is immutable.');
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('LazyBSONArray is immutable.');
    }

    public function count(): int
    {
        $this->initialize();

        return count($this->items);
    }

    private function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        $items = $this->arrayList->toPHP(['array' => 'bson', 'document' => 'bson']);
        $this->items = $this->initializer ? array_map($this->initializer, $items) : $items;
    }
}
