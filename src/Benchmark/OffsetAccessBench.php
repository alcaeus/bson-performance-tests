<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use Alcaeus\BsonPerformanceTests\Document\RootDocument;
use Generator;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PhpBench\Attributes\ParamProviders;

final class OffsetAccessBench extends BaseBench
{
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
        yield 'BSON for embedded documents' => ['typeMap' => self::TYPEMAP_BSON_EMBEDDED];
        yield 'Persistable objects' => ['typeMap' => self::TYPEMAP_DOCUMENT_CLASS];
        yield 'Persistable objects (field paths)' => ['typeMap' => self::TYPEMAP_DOCUMENT_CLASS_FIELD_PATHS];
        yield 'Library default' => ['typeMap' => self::TYPEMAP_LIBRARY_DEFAULT];
    }

    #[ParamProviders('provideTypemap')]
    public function benchOffset(array $params): void
    {
        $data = $this->bson->toPHP(...$params);
        $data->documentArray;
    }

    public function benchOffsetArray(): void
    {
        $data = $this->bson->toPHP(self::TYPEMAP_ARRAY);
        $data['documentArray'];
    }

    public function benchOffsetBSON(): void
    {
        $this->bson->get('documentArray');
    }

    public function benchOffsetBSONIterator(): void
    {
        foreach ($this->bson as $key => $value) {
            if ($key == 'documentArray') {
                $value;
                return;
            }
        }
    }
}
