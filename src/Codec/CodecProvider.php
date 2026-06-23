<?php

namespace Alcaeus\BsonPerformanceTests\Codec;

interface CodecProvider
{
    /**
     * @template T
     * @var class-string<T>
     * @return Codec<T>
     */
    public function get(string $type): ?Codec;
}
