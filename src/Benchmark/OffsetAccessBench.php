<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use Alcaeus\BsonPerformanceTests\Document\RootDocument;
use MongoDB\BSON\BSON;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

final class OffsetAccessBench extends BaseBench
{
    private const TYPEMAP_BSON = ['root' => 'bson'];

    private const TYPEMAP_BSON_EMBEDDED = ['document' => 'bson'];

    private const TYPEMAP_DOCUMENT_CLASS = [
        'root' => RootDocument::class,
        'document' => EmbeddedDocument::class,
    ];

    private const TYPEMAP_DOCUMENT_CLASS_FIELD_PATHS = [
        'root' => RootDocument::class,
        'fieldPaths' => [
            'embedded' => EmbeddedDocument::class,
            'documentArray.$' => EmbeddedDocument::class,
        ],
    ];

    private const TYPEMAP_LIBRARY_DEFAULT = [
        'array' => BSONArray::class,
        'document' => BSONDocument::class,
        'root' => BSONDocument::class,
    ];

    public function benchOffsetWithDefaultTypemap(): void
    {
        $data = $this->bson->toPHP();
        $value = $data->documentArray;
    }

    public function benchOffsetWithArraysAsBSON(): void
    {
        $data = $this->bson->toPHP(['array' => 'bson', 'document' => 'bson']);
        $value = $data->documentArray;
    }

    public function benchOffsetWithIterator(): void
    {
        foreach ($this->bson as $key => $value) {
            if ($key == 'documentArray') {
                $foundValue = $value;
                return;
            }
        }
    }

    public function benchOffsetDirectlyFromBSON(): void
    {
        $value = $this->bson->get('documentArray');
    }
}
