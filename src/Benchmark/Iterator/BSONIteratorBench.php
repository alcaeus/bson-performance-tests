<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark\Iterator;

use MongoDB\BSON\BSON;
use MongoDB\BSON\BSONIterator;
use PhpBench\Attributes\BeforeMethods;
use function iterator_to_array;

#[BeforeMethods("init")]
final class BSONIteratorBench
{
    private array $array;
    private BSON $bson;

    public function init(): void
    {
        $bson = BSON::fromPHP([
            'asBson' => range(0, 10000),
            'asArray' => range(0, 10000),
        ]);

        $converted = $bson->toPHP(['array' => 'array', 'fieldPaths' => ['asBson' => 'bson']]);
        $this->array = $converted->asArray;
        $this->bson = $converted->asBson;
    }

    public function benchIterateOverArray(): void
    {
        foreach ($this->array as $key => $value) {}
    }

    public function benchIterateOverBson(): void
    {
        foreach ($this->bson as $key => $value) {}
    }

    public function benchIterateOverConvertedIterator(): void
    {
        foreach (iterator_to_array($this->bson) as $key => $value) {}
    }
}
