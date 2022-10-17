<?php

namespace Alcaeus\BsonPerformanceTests\Marshaller;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use Alcaeus\BsonPerformanceTests\Document\RootDocument;
use MongoDB\BSON\BSON;

final class RootDocumentMarshaller
{
    public function __construct(
        private EmbeddedDocumentMarshaller $embeddedDocumentMarshaller,
    ) {}

    public function marshalUsingIterator(BSON $data): RootDocument
    {
        $rootDocument = new RootDocument();

        foreach ($data as $key => $value) {
            switch ($key) {
                case '_id':
                    $rootDocument->id = $value;
                    break;

                case 'embedded':
                    $rootDocument->embedded = $this->embeddedDocumentMarshaller->marshalUsingIterator($value);
                    break;

                case 'intArray':
                case 'stringArray':
                case 'dateTimeArray':
                    $rootDocument->$key = $value->toPHP();
                    break;

                case 'documentArray':
                    $rootDocument->documentArray = array_map(
                        fn (BSON $embeddedData): EmbeddedDocument => $this->embeddedDocumentMarshaller->marshalUsingIterator($embeddedData),
                        $value->toPHP(['document' => 'bson']),
                    );
            }
        }

        return $rootDocument;
    }

    public function marshalUsingArray(BSON $data): RootDocument
    {
        $rootDocument = new RootDocument();

        $dataArray = $data->toPHP(['root' => 'array', 'array' => 'array', 'document' => 'bson']);

        $rootDocument->id = $dataArray['_id'];

        $rootDocument->embedded = $this->embeddedDocumentMarshaller->marshalUsingIterator($dataArray['embedded']);
        $rootDocument->intArray = $dataArray['intArray'];
        $rootDocument->stringArray = $dataArray['stringArray'];
        $rootDocument->dateTimeArray = $dataArray['dateTimeArray'];

        $rootDocument->documentArray = array_map(
            fn (BSON $embeddedData): EmbeddedDocument => $this->embeddedDocumentMarshaller->marshalUsingArray($embeddedData),
            $dataArray['documentArray'],
        );

        return $rootDocument;
    }
}
