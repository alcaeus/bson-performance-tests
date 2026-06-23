<?php

namespace Alcaeus\BsonPerformanceTests\Codec;

use MongoDB\BSON\DocumentWriter;

/** @template-extends Codec<null> */
final class NullCodec implements Codec
{
    public function decode(null $source): null
    {
        return $source;
    }

    public function encode(DocumentWriter $writer, null $value): void
    {
        $writer->writeNull();
    }
}
