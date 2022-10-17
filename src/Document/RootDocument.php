<?php

namespace Alcaeus\BsonPerformanceTests\Document;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Unserializable;
use MongoDB\BSON\UTCDateTime;

final class RootDocument implements Unserializable
{
    public ObjectId $id;
    public EmbeddedDocument $embedded;

    /** @var array<int> */
    public array $intArray;

    /** @var array<string> */
    public array $stringArray;

    /** @var array<UTCDateTime> */
    public array $dateTimeArray;

    /** @var array<EmbeddedDocument> */
    public array $documentArray;

    public function bsonUnserialize(array $data): void
    {
        $this->id = $data['_id'];
        $this->embedded = $data['embedded'];
        $this->intArray = $data['intArray'];
        $this->stringArray = $data['stringArray'];
        $this->dateTimeArray = $data['dateTimeArray'];
        $this->documentArray = $data['documentArray'];
    }
}
