<?php

namespace Alcaeus\BsonPerformanceTests\Marshaller;

use Alcaeus\BsonPerformanceTests\Document\LazyEmbeddedDocument;
use Closure;
use MongoDB\BSON\Document;
use function array_combine;
use function array_fill_keys;
use function array_map;

final class LazyEmbeddedDocumentMarshaller
{
    private const PROPERTIES = ['foo', 'baz'];

    public function marshal(Document $data): LazyEmbeddedDocument
    {
        $initializer = $this->createPropertyInitializerClosure($data);

        $initializers = array_fill_keys(self::PROPERTIES, $initializer);
        $initializers["\0"] = $this->createObjectInitializerClosure(self::PROPERTIES, $data);

        return LazyEmbeddedDocument::createLazyGhost($initializers);
    }

    private function createObjectInitializerClosure(array $properties, Document $data): Closure
    {
        return fn (LazyEmbeddedDocument $instance, array $defaults): mixed
            => array_combine(
                $properties,
                array_map(fn (string $property): mixed => $this->marshalProperty($data, $instance, $property), $properties),
            ) + $defaults
            ;
    }

    private function createPropertyInitializerClosure(Document $data): Closure
    {
        return fn (LazyEmbeddedDocument $instance, string $property, ?string $writeScope = null, mixed $default = null): mixed
            => $this->marshalProperty($data, $instance, $property, $writeScope = null, $default = null);
    }

    protected function marshalProperty(Document $data, LazyEmbeddedDocument $instance, string $property, ?string $writeScope = null, mixed $default = null): mixed
    {
        return $data->get($property);
    }
}
