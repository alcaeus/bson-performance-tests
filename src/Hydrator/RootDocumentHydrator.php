<?php

namespace Alcaeus\BsonPerformanceTests\Hydrator;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use Alcaeus\BsonPerformanceTests\Document\RootDocument;
use MongoDB\BSON\Document;

final class RootDocumentHydrator
{
    public function __construct(
        private EmbeddedDocumentHydrator $embeddedDocumentHydrator,
    ) {}

    public function hydrate(array $data): RootDocument
    {
        $rootDocument = new RootDocument();

        $rootDocument->id = $data['_id'];
        $rootDocument->embedded = $this->embeddedDocumentHydrator->hydrate($data['embedded']);
        $rootDocument->intArray = $data['intArray'];
        $rootDocument->stringArray = $data['stringArray'];
        $rootDocument->dateTimeArray = $data['dateTimeArray'];
        $rootDocument->documentArray = array_map(
            fn (array $embeddedData): EmbeddedDocument => $this->embeddedDocumentHydrator->hydrate($embeddedData),
            $data['documentArray']
        );

        return $rootDocument;
    }

    public function hydrateFromBSON(Document $data): RootDocument
    {
        $rootDocument = new RootDocument();

        $rootDocument->id = $data->get('_id');
        $rootDocument->embedded = $this->embeddedDocumentHydrator->hydrateFromBSON($data->get('embedded'));
        $rootDocument->intArray = $data->get('intArray')->toPHP();
        $rootDocument->stringArray = $data->get('stringArray')->toPHP();
        $rootDocument->dateTimeArray = $data->get('dateTimeArray')->toPHP();
        $rootDocument->documentArray = array_map(
            fn (Document $embeddedData): EmbeddedDocument => $this->embeddedDocumentHydrator->hydrateFromBSON($embeddedData),
            $data->get('documentArray')->toPHP(['document' => 'bson'])
        );

        return $rootDocument;
    }
}
