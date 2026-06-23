<?php

namespace Alcaeus\BsonPerformanceTests\Codec;

use MongoDB\BSON\ArrayList;
use MongoDB\BSON\Document;
use MongoDB\BSON\DocumentWriter;
use stdClass;
use UnexpectedValueException;
use function get_debug_type;

/**
 * @template-extends Codec<object>
 */
class DocumentCodec implements Codec
{
    public function __construct(private readonly CodecProvider $provider)
    {
    }

    public function decode(?Document $source): ?object
    {
        if ($source === null) {
            return null;
        }

        $object = new stdClass();

        foreach ($source as $key => $value) {
            $object->$key = $value;
        }

        return $object;
    }

    public function encode(DocumentWriter $writer, ?object $value): void
    {
        if ($value === null) {
            return;
        }

        foreach ($value as $key => $fieldValue) {
            $writer->writeKey($key);

            $codec = $this->provider->get(get_debug_type($fieldValue));
            if (!$codec) {
                throw new UnexpectedValueException(sprintf('Could not find a codec for type "%s" in key "%s"', get_debug_type($fieldValue), $key));
            }

            $codec->encode($writer, $fieldValue);
        }
    }
}
