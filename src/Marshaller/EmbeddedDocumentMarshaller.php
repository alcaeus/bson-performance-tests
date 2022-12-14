<?php

namespace Alcaeus\BsonPerformanceTests\Marshaller;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use MongoDB\BSON\Document;

final class EmbeddedDocumentMarshaller
{
    public function marshalUsingIterator(Document $data): EmbeddedDocument
    {
        $embeddedDocument = new EmbeddedDocument();

        foreach ($data as $key => $value) {
            switch ($key) {
                case 'foo':
                case 'baz':
                    $embeddedDocument->$key = $value;
                    break;
            }
        }

        return $embeddedDocument;
    }

    public function marshalUsingArray(Document $data): EmbeddedDocument
    {
        $embeddedDocument = new EmbeddedDocument();

        $arrayData = $data->toPHP(['root' => 'array']);
        $embeddedDocument->bsonUnserialize($arrayData);

        return $embeddedDocument;
    }

    public function marshalUsingGet(Document $data): EmbeddedDocument
    {
        $embeddedDocument = new EmbeddedDocument();

        $embeddedDocument->foo = $data->get('foo');
        $embeddedDocument->baz = $data->get('baz');

        return $embeddedDocument;
    }
}
