<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark;

use Alcaeus\BsonPerformanceTests\Document\LazyBSONArray;
use Alcaeus\BsonPerformanceTests\Document\LazyEmbeddedDocument;
use Alcaeus\BsonPerformanceTests\Document\LazyRootDocument;
use Alcaeus\BsonPerformanceTests\Document\RootDocument;
use Alcaeus\BsonPerformanceTests\Hydrator\EmbeddedDocumentHydrator;
use Alcaeus\BsonPerformanceTests\Hydrator\RootDocumentHydrator;
use Alcaeus\BsonPerformanceTests\Marshaller\EmbeddedDocumentMarshaller;
use Alcaeus\BsonPerformanceTests\Marshaller\LazyEmbeddedDocumentMarshaller;
use Alcaeus\BsonPerformanceTests\Marshaller\LazyRootDocumentMarshaller;
use Alcaeus\BsonPerformanceTests\Marshaller\RootDocumentMarshaller;
use Closure;
use Generator;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\Warmup;
use Symfony\Component\VarExporter\LazyObjectInterface;
use function assert;
use function is_int;
use function is_string;

/**
 * This benchmark compares serialisation using the hydrator concept from Doctrine
 * ODM (where a hydrator receives the BSON structure using a full array type map)
 * and converts that, versus using a fictitious "marshaller" concept which works
 * with raw BSON values. There are two options for implementing this:
 * - The first (marshalUsingIterator) uses a BSONIterator for iterating over the
 *   BSON structure and handles each field as it encounters them.
 * - The second converts the BSON structure to an array, leaving embedded
 *   documents as BSON structures. This helps understand the performance impact
 *   of BSONIterator.
 *
 */
#[BeforeMethods(['initHydrators'])]
#[Warmup(1)]
class MarshalBench extends BaseBench
{
    private RootDocumentHydrator $rootDocumentHydrator;
    private EmbeddedDocumentHydrator $embeddedDocumentHydrator;

    private RootDocumentMarshaller $rootDocumentMarshaller;
    private EmbeddedDocumentMarshaller $embeddedDocumentMarshaller;

    private LazyRootDocumentMarshaller $lazyRootDocumentMarshaller;
    private LazyEmbeddedDocumentMarshaller $lazyEmbeddedDocumentMarshaller;

    public function initHydrators(): void
    {
        $this->embeddedDocumentHydrator = new EmbeddedDocumentHydrator();
        $this->rootDocumentHydrator = new RootDocumentHydrator($this->embeddedDocumentHydrator);

        $this->embeddedDocumentMarshaller = new EmbeddedDocumentMarshaller();
        $this->rootDocumentMarshaller = new RootDocumentMarshaller($this->embeddedDocumentMarshaller);

        $this->lazyEmbeddedDocumentMarshaller = new LazyEmbeddedDocumentMarshaller();
        $this->lazyRootDocumentMarshaller = new LazyRootDocumentMarshaller($this->lazyEmbeddedDocumentMarshaller);
    }

    public function getHydrators(): Generator
    {
        yield 'ODM Hydrator' => ['initializer' => 'hydrateODM'];
        yield 'ODM BSON Hydrator' => ['initializer' => 'hydrateODMFromBSON'];
        yield 'Iterator Marshaller' => ['initializer' => 'marshalBSONIterator'];
        yield 'Array Marshaller' => ['initializer' => 'marshalArray'];
        yield 'Get Marshaller' => ['initializer' => 'marshalGet'];
        yield 'Lazy Marshaller' => ['initializer' => 'marshalLazy'];
    }

    public function getHydratorsWithObjectInitializer(): Generator
    {
        yield from $this->getHydrators();

        yield 'Lazy Marshaller w/ object initializer' => ['initializer' => 'marshalLazy', 'initObject' => true];
    }

    #[ParamProviders('getHydrators')]
    public function benchCreateObject(array $params): void
    {
        $this->createObject($params);
    }

    #[ParamProviders('getHydratorsWithObjectInitializer')]
    public function benchAccessId(array $params): void
    {
        $object = $this->createObject($params);

        assert($object->id instanceof ObjectId);
    }

    #[ParamProviders('getHydratorsWithObjectInitializer')]
    public function benchAccessEmbeddedProperty(array $params): void
    {
        $object = $this->createObject($params);

        assert(is_string($object->embedded->foo));
    }

    #[ParamProviders('getHydratorsWithObjectInitializer')]
    public function benchAccessSingleListItem(array $params): void
    {
        $object = $this->createObject($params);

        $this->accessSingleListItem($object);
    }

    #[ParamProviders('getHydratorsWithObjectInitializer')]
    public function benchAccessAllListItems(array $params): void
    {
        $object = $this->createObject($params);

        $this->accessAllListItems($object, $params['initObject'] ?? false);
    }

    private function createObject(array $params)
    {
        $initializer = $params['initializer'];

        return $this->$initializer();
    }

    private function accessItems(array|LazyBSONArray $list, Closure $assert, bool $initObject = false): void
    {
        $arrayList = $list instanceof LazyBSONArray ? $list->toArray() : $list;
        foreach ($arrayList as $item) {
            if ($initObject && $item instanceof LazyObjectInterface) {
                $item->initializeLazyObject();
            }

            $assert($item);
        }
    }

    private function accessSingleListItem(RootDocument|LazyRootDocument $object): void
    {
        assert(is_int($object->intArray[6753]));
        assert(is_string($object->stringArray[6753]));
        assert($object->dateTimeArray[6753] instanceof UTCDateTime);
        assert(is_string($object->documentArray[6753]->foo));
    }

    private function accessAllListItems(LazyRootDocument|RootDocument $object, bool $initObject = false): void
    {
        $this->accessItems($object->intArray, fn($value) => assert(is_int($value)));
        $this->accessItems($object->stringArray, fn($value) => assert(is_string($value)));
        $this->accessItems($object->dateTimeArray, fn($value) => assert($value instanceof UTCDateTime));
        $this->accessItems($object->documentArray, fn($value) => assert(is_string($value->foo)), $initObject);

        assert(count($object->intArray) === 10000);
        assert(count($object->stringArray) === 10000);
        assert(count($object->dateTimeArray) === 10000);
        assert(count($object->documentArray) === 10000);
    }

    private function hydrateODM(): RootDocument
    {
        return $this->rootDocumentHydrator->hydrate($this->bson->toPHP(self::TYPEMAP_ARRAY));
    }

    private function hydrateODMFromBSON(): RootDocument
    {
        return $this->rootDocumentHydrator->hydrateFromBSON($this->bson);
    }

    private function marshalBSONIterator(): RootDocument
    {
        return $this->rootDocumentMarshaller->marshalUsingIterator($this->bson);
    }

    private function marshalArray(): RootDocument
    {
        return $this->rootDocumentMarshaller->marshalUsingArray($this->bson);
    }

    private function marshalGet(): RootDocument
    {
        return $this->rootDocumentMarshaller->marshalUsingGet($this->bson);
    }

    private function marshalLazy(): LazyRootDocument
    {
        return $this->lazyRootDocumentMarshaller->marshal($this->bson);
    }
}
