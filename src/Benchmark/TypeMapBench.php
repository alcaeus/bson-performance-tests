<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use Alcaeus\BsonPerformanceTests\Document\RootDocument;
use MongoDB\BSON\BSON;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

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

    public function benchUseLibraryDefaultTypeMap(): void
    {
        $this->bson->toPHP(self::TYPEMAP_LIBRARY_DEFAULT);
    }
}
