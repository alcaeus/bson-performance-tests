<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark;

use MongoDB\BSON\BSON;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

use PhpBench\Attributes\BeforeMethods;
use function array_fill;
use function range;

#[BeforeMethods(['init'])]
abstract class BaseBench
{
    protected const TYPEMAP_ARRAY = [
        'root' => 'array',
        'document' => 'array',
        'array' => 'array',
    ];

    protected BSON $bson;

    public function init(): void
    {
        $embedded = [
            'foo' => 'bar',
            'baz' => 'foo',
        ];

        $document = [
            '_id' => new ObjectId(),
            'embedded' => $embedded,
            'intArray' => range(0, 10000),
            'stringArray' => array_fill(0, 10000, 'foo'),
            'dateTimeArray' => array_fill(0, 10000, new UTCDateTime(0)),
            'documentArray' => array_fill(0, 10000, $embedded),
        ];

        $this->bson = BSON::fromPHP($document);
    }
}
