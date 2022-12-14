<?php

namespace Alcaeus\BsonPerformanceTests\Document;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Symfony\Component\VarExporter\LazyGhostTrait;
use Symfony\Component\VarExporter\LazyObjectInterface;

final class LazyRootDocument implements LazyObjectInterface
{
    use LazyGhostTrait;

    public ObjectId $id;
    public LazyEmbeddedDocument $embedded;

    /** @var LazyBSONArray<int> */
    public LazyBSONArray $intArray;

    /** @var LazyBSONArray<string> */
    public LazyBSONArray $stringArray;

    /** @var LazyBSONArray<UTCDateTime> */
    public LazyBSONArray $dateTimeArray;

    /** @var LazyBSONArray<LazyEmbeddedDocument> */
    public LazyBSONArray $documentArray;
}
