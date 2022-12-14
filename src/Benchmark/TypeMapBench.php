<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use Alcaeus\BsonPerformanceTests\Document\RootDocument;
use Generator;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PhpBench\Attributes\ParamProviders;

final class TypeMapBench extends BaseBench
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

    public function provideTypemap(): Generator
    {
        yield 'Default' => ['typeMap' => []];
        yield 'Array' => ['typeMap' => self::TYPEMAP_ARRAY];
        yield 'BSON' => ['typeMap' => self::TYPEMAP_BSON];
        yield 'BSON for embedded documents' => ['typeMap' => self::TYPEMAP_BSON_EMBEDDED];
        yield 'Persistable objects' => ['typeMap' => self::TYPEMAP_DOCUMENT_CLASS];
        yield 'Persistable objects (field paths)' => ['typeMap' => self::TYPEMAP_DOCUMENT_CLASS_FIELD_PATHS];
        yield 'Library default' => ['typeMap' => self::TYPEMAP_LIBRARY_DEFAULT];
    }

    #[ParamProviders('provideTypemap')]
    public function benchToPHP(array $params): void
    {
        $this->bson->toPHP(...$params);
    }
}
