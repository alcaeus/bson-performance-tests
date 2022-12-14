<?php

namespace Alcaeus\BsonPerformanceTests\Marshaller;

use Alcaeus\BsonPerformanceTests\Document\LazyBSONArray;
use Alcaeus\BsonPerformanceTests\Document\LazyEmbeddedDocument;
use Alcaeus\BsonPerformanceTests\Document\LazyRootDocument;
use Closure;
use MongoDB\BSON\Document;
use function array_combine;
use function array_fill_keys;
use function array_map;

final class LazyRootDocumentMarshaller
{
    private const PROPERTIES = ['id', 'embedded', 'intArray', 'stringArray', 'dateTimeArray', 'documentArray'];

    public function __construct(
        private LazyEmbeddedDocumentMarshaller $lazyEmbeddedDocumentMarshaller,
    ) {}

    public function marshal(Document $data): LazyRootDocument
    {
        $initializer = $this->createPropertyInitializerClosure($data);

        $initializers = array_fill_keys(self::PROPERTIES, $initializer);
        $initializers["\0"] = $this->createObjectInitializerClosure(self::PROPERTIES, $data);

        return LazyRootDocument::createLazyGhost($initializers);
    }

    private function createPropertyInitializerClosure(Document $data): Closure
    {
        return fn (LazyRootDocument $instance, string $property, ?string $writeScope = null, mixed $default = null): mixed
            => $this->marshalProperty($data, $instance, $property, $writeScope, $default);
    }

    private function createObjectInitializerClosure(array $properties, Document $data): Closure
    {
        return fn (LazyRootDocument $instance, array $defaults): mixed
            => array_combine(
                $properties,
                array_map(fn (string $property): mixed => $this->marshalProperty($data, $instance, $property), $properties),
            ) + $defaults
        ;
    }

    protected function marshalProperty(Document $data, LazyRootDocument $instance, string $property, ?string $writeScope = null, mixed $default = null): mixed
    {
        // Todo: hack for _id => id mapping
        $field = ($property === 'id') ? '_id' : $property;

        if (! $data->has($field)) {
            return $default;
        }

        switch ($property) {
            case 'id':
                return $data->get($field);
                break;

            case 'embedded':
                return $this->lazyEmbeddedDocumentMarshaller->marshal($data->get($field));
                break;

            case 'intArray':
            case 'stringArray':
            case 'dateTimeArray':
                return new LazyBSONArray($data->get($field));
                break;

            case 'documentArray':
                return new LazyBSONArray(
                    $data->get($field),
                    fn (Document $embeddedData): LazyEmbeddedDocument =>
                        $this->lazyEmbeddedDocumentMarshaller->marshal($embeddedData),
                );

            default:
                return $default;
        }
    }
}
