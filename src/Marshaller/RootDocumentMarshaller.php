<?php

namespace Alcaeus\BsonPerformanceTests\Marshaller;

use Alcaeus\BsonPerformanceTests\Document\EmbeddedDocument;
use Alcaeus\BsonPerformanceTests\Document\RootDocument;
use MongoDB\BSON\Document;

final class RootDocumentMarshaller
{
    public function __construct(
        private EmbeddedDocumentMarshaller $embeddedDocumentMarshaller,
    ) {}

    public function marshalUsingIterator(Document $data): RootDocument
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
                        fn (Document $embeddedData): EmbeddedDocument => $this->embeddedDocumentMarshaller->marshalUsingIterator($embeddedData),
                        $value->toPHP(['document' => 'bson']),
                    );
            }
        }

        return $rootDocument;
    }

    public function marshalUsingArray(Document $data): RootDocument
    {
        $rootDocument = new RootDocument();

        $dataArray = $data->toPHP(['root' => 'array', 'array' => 'array', 'document' => 'bson']);

        $rootDocument->id = $dataArray['_id'];

        $rootDocument->embedded = $this->embeddedDocumentMarshaller->marshalUsingIterator($dataArray['embedded']);
        $rootDocument->intArray = $dataArray['intArray'];
        $rootDocument->stringArray = $dataArray['stringArray'];
        $rootDocument->dateTimeArray = $dataArray['dateTimeArray'];

        $rootDocument->documentArray = array_map(
            fn (Document $embeddedData): EmbeddedDocument => $this->embeddedDocumentMarshaller->marshalUsingArray($embeddedData),
            $dataArray['documentArray'],
        );

        return $rootDocument;
    }

    public function marshalUsingGet(Document $data): RootDocument
    {
        $rootDocument = new RootDocument();

        $rootDocument->id = $data->get('_id');

        $rootDocument->embedded = $this->embeddedDocumentMarshaller->marshalUsingIterator($data->get('embedded'));
        $rootDocument->intArray = $data->get('intArray')->toPHP();
        $rootDocument->stringArray = $data->get('stringArray')->toPHP();
        $rootDocument->dateTimeArray = $data->get('dateTimeArray')->toPHP();

        $rootDocument->documentArray = array_map(
            fn (Document $embeddedData): EmbeddedDocument => $this->embeddedDocumentMarshaller->marshalUsingGet($embeddedData),
            $data->get('documentArray')->toPHP(['document' => 'bson']),
        );

        return $rootDocument;
    }
}
