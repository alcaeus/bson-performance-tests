<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark;

use MongoDB\BSON\Document;
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

    protected Document $bson;

    public function init(): void
    {
        $embedded = [
            'foo' => 'bar',
            'baz' => 'foo',
        ];

        $document = [
            '_id' => new ObjectId(),
            'embedded' => $embedded,
            'intArray' => range(0, 9999),
            'stringArray' => array_fill(0, 10000, 'foo'),
            'dateTimeArray' => array_fill(0, 10000, new UTCDateTime(0)),
            'documentArray' => array_fill(0, 10000, $embedded),
            'unusedDocumentArray' => array_fill(0, 10000, $embedded),
        ];

        $this->bson = Document::fromPHP($document);
    }
}
