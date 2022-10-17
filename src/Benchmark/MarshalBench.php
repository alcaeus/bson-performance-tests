<?php

namespace Alcaeus\BsonPerformanceTests\Benchmark;

use Alcaeus\BsonPerformanceTests\Hydrator\EmbeddedDocumentHydrator;
use Alcaeus\BsonPerformanceTests\Hydrator\RootDocumentHydrator;
use Alcaeus\BsonPerformanceTests\Marshaller\EmbeddedDocumentMarshaller;
use Alcaeus\BsonPerformanceTests\Marshaller\RootDocumentMarshaller;
use PhpBench\Attributes\BeforeMethods;

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
class MarshalBench extends BaseBench
{
    private RootDocumentHydrator $rootDocumentHydrator;
    private EmbeddedDocumentHydrator $embeddedDocumentHydrator;

    private RootDocumentMarshaller $rootDocumentMarshaller;
    private EmbeddedDocumentMarshaller $embeddedDocumentMarshaller;

    public function initHydrators(): void
    {
        $this->embeddedDocumentHydrator = new EmbeddedDocumentHydrator();
        $this->rootDocumentHydrator = new RootDocumentHydrator($this->embeddedDocumentHydrator);

        $this->embeddedDocumentMarshaller = new EmbeddedDocumentMarshaller();
        $this->rootDocumentMarshaller = new RootDocumentMarshaller($this->embeddedDocumentMarshaller);
    }

    public function benchDoctrineOdm(): void
    {
        $this->rootDocumentHydrator->hydrate($this->bson->toPHP(self::TYPEMAP_ARRAY));
    }

    public function benchIteratorMarshalling(): void
    {
        $this->rootDocumentMarshaller->marshalUsingIterator($this->bson);
    }

    public function benchArrayMarshalling(): void
    {
        $this->rootDocumentMarshaller->marshalUsingArray($this->bson);
    }
}
