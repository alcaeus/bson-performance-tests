<?php

namespace Alcaeus\BsonPerformanceTests\Codec;

use MongoDB\BSON\ObjectId;
use function class_exists;

class BSONCodecProvider implements CodecProvider
{
    private array $codecs;

    public function __construct()
    {
        $this->codecs = [
            'object' => new DocumentCodec($this),
            ObjectId::class => new ObjectIdCodec(),
            'null' => new NullCodec(),
            'string' => new StringCodec(),
        ];
    }

    public function get(string $type): ?Codec
    {
        return $this->codecs[$type] ?? null;
    }
}
