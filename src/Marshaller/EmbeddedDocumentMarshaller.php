<?php

namespace Alcaeus\BsonPerformanceTests\Marshaller;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use MongoDB\BSON\BSON;

final class EmbeddedDocumentMarshaller
{
    public function marshalUsingIterator(BSON $data): EmbeddedDocument
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
    public function marshalUsingArray(BSON $data): EmbeddedDocument
    {
        $embeddedDocument = new EmbeddedDocument();

        $arrayData = $data->toPHP(['root' => 'array']);
        $embeddedDocument->bsonUnserialize($arrayData);

        return $embeddedDocument;
    }
}
