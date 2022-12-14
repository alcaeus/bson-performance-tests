<?php

namespace Alcaeus\BsonPerformanceTests\Tests\Document;

use Alcaeus\BsonPerformanceTests\Document\LazyBSONArray;
use MongoDB\BSON\ArrayList;
use PHPUnit\Framework\TestCase;

class LazyBSONArrayTest extends TestCase
{
    public function testOffsetGet(): void
    {
        $bsonArray = ArrayList::fromPHP(range(0, 0));

        $lazyArray = new LazyBSONArray($bsonArray);

        self::assertTrue(isset($lazyArray[0]));
        self::assertSame(0, $lazyArray[0]);
    }
}
