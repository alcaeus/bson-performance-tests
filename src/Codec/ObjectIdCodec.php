<?php

namespace Alcaeus\BsonPerformanceTests\Codec;

use MongoDB\BSON\DocumentWriter;
use MongoDB\BSON\ObjectId;

/** @template-extends Codec<ObjectId> */
final class ObjectIdCodec implements Codec
{
    public function decode(?ObjectId $source): ?ObjectId
    {
        return $source;
    }

    public function encode(DocumentWriter $writer, ?ObjectId $value): void
    {
        if ($value === null) {
            return;
        }

        $writer->writeObjectId($value);
    }
}
