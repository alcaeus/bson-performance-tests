<?php

namespace Alcaeus\BsonPerformanceTests\Codec;

use MongoDB\BSON\DocumentWriter;

/** @template-extends Codec<string> */
final class StringCodec implements Codec
{
    public function decode(?string $source): ?string
    {
        return $source;
    }

    public function encode(DocumentWriter $writer, ?string $value): void
    {
        if ($value === null) {
            return;
        }

        $writer->writeString($value);
    }
}
