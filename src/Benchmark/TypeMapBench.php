<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use Alcaeus\BsonPerformanceTests\Document\RootDocument;
use MongoDB\BSON\BSON;

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

    public function benchUseDefaultTypeMap(): void
    {
        $this->bson->toPHP();
    }

    public function benchUseArrayTypeMap(): void
    {
        $this->bson->toPHP(self::TYPEMAP_ARRAY);
    }

    public function benchUseBsonTypeMap(): void
    {
        $this->bson->toPHP(self::TYPEMAP_BSON);
    }

    public function benchUseBsonEmbeddedTypeMap(): void
    {
        $this->bson->toPHP(self::TYPEMAP_BSON_EMBEDDED);
    }

    public function benchUseDocumentTypeMap(): void
    {
        $this->bson->toPHP(self::TYPEMAP_DOCUMENT_CLASS);
    }

    public function benchUseDocumentFieldPathsTypeMap(): void
    {
        $this->bson->toPHP(self::TYPEMAP_DOCUMENT_CLASS_FIELD_PATHS);
    }
}
