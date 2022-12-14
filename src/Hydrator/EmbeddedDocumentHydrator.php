<?php

namespace Alcaeus\BsonPerformanceTests\Hydrator;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use MongoDB\BSON\Document;

final class EmbeddedDocumentHydrator
{
    public function hydrate(array $data): EmbeddedDocument
    {
        $embeddedDocument = new EmbeddedDocument();

        $embeddedDocument->foo = $data['foo'];
        $embeddedDocument->baz = $data['baz'];

        return $embeddedDocument;
    }

    public function hydrateFromBSON(Document $data): EmbeddedDocument
    {
        $embeddedDocument = new EmbeddedDocument();

        $embeddedDocument->foo = $data->get('foo');
        $embeddedDocument->baz = $data->get('baz');

        return $embeddedDocument;
    }
}
