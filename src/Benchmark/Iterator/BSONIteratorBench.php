<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark\Iterator;

use MongoDB\BSON\ArrayList;
use MongoDB\BSON\Iterator;
use PhpBench\Attributes\BeforeMethods;
use function iterator_to_array;

#[BeforeMethods("init")]
final class BSONIteratorBench
{
    private array $array;
    private ArrayList $arrayList;

    public function init(): void
    {
        $this->array = range(0, 10000);
        $this->arrayList = ArrayList::fromPHP($this->array);
    }

    public function benchIterateOverArray(): void
    {
        foreach ($this->array as $key => $value) {}
    }

    public function benchIterateOverBson(): void
    {
        foreach ($this->arrayList as $key => $value) {}
    }

    public function benchIterateOverConvertedIterator(): void
    {
        foreach (iterator_to_array($this->arrayList) as $key => $value) {}
    }
}
