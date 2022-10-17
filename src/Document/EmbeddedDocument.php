<?php

namespace Alcaeus\BsonPerformanceTests\Document;

use MongoDB\BSON\Unserializable;

final class EmbeddedDocument implements Unserializable
{
    public string $foo;
    public string $baz;

    public function bsonUnserialize(array $data): void
    {
        $this->foo = $data['foo'];
        $this->baz = $data['baz'];
    }
}
