<?php

namespace Alcaeus\BsonPerformanceTests\Codec;

use MongoDB\BSON\ArrayList;
use MongoDB\BSON\Document;
use MongoDB\BSON\DocumentWriter;

/**
 * @template T
 */
interface Codec
{
    /** @return T */
    public function decode(null $source): mixed;

    /** @param T $value */
    public function encode(DocumentWriter $writer, null $value): void;
}
