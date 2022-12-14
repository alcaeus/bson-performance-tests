<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark;

use Alcaeus\BsonPerformanceTests\Document\LazyBSONArray;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Generator;
use MongoDB\BSON\ArrayList;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\Warmup;
use function range;

#[BeforeMethods("init")]
#[Warmup(1)]
final class LazyBSONArrayBench
{
    private ArrayList $bsonArray;
    private LazyBSONArray $lazyIntArray;

    public function init(): void
    {
        $this->bsonArray = ArrayList::fromPHP(range(0, 9999));

        $this->lazyIntArray = new LazyBSONArray($this->bsonArray);
    }

    public function provideLists(): Generator
    {
        yield 'ArrayCollection' => ['property' => 'bsonArray', 'initializer' => 'bsonToArrayCollection'];
        yield 'BSON to array' => ['property' => 'bsonArray', 'initializer' => 'bsonToArray'];
        yield 'BSON to lazy array' => ['property' => 'bsonArray', 'initializer' => 'bsonToLazyArray'];
    }

    #[ParamProviders('provideLists')]
    public function benchAccessSingleElement(array $params)
    {
        $list = $this->getList($params);

        $list[829];
    }

    #[ParamProviders('provideLists')]
    public function benchIterateList(array $params)
    {
        $list = $this->getList($params);

        foreach ($list as $value);
    }

    #[ParamProviders('provideLists')]
    public function benchCount(array $params)
    {
        $list = $this->getList($params);

        count($list);
    }

    private function getList(array $params): array|Collection|LazyBSONArray
    {
        $property = $params['property'];
        $initializer = $params['initializer'] ?? null;

        $list = $this->$property;
        return $initializer ? $this->$initializer($list) : $list;
    }

    private function lazyArrayToArray(LazyBSONArray $lazyArray): array
    {
        return $lazyArray->toArray();
    }

    private function bsonToArray(ArrayList $bsonArray): array
    {
        return $bsonArray->toPHP();
    }

    private function bsonToLazyArray(ArrayList $bsonArray): LazyBSONArray
    {
        return new LazyBSONArray($bsonArray);
    }

    private function bsonToArrayCollection(ArrayList $bsonArray): ArrayCollection
    {
        return new ArrayCollection($bsonArray->toPHP());
    }

    private function initialize(int $value): int
    {
        return $value ** 2;
    }
}
