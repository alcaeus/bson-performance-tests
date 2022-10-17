<?php

namespace Alcaeus\BsonPerformanceTests\Hydrator;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;

final class EmbeddedDocumentHydrator
{
    public function hydrate(array $data): EmbeddedDocument
    {
        $embeddedDocument = new EmbeddedDocument();

        $embeddedDocument->foo = $data['foo'];
        $embeddedDocument->baz = $data['baz'];

        return $embeddedDocument;
    }
}
