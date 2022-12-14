<?php

namespace Alcaeus\BsonPerformanceTests\Document;

use Symfony\Component\VarExporter\LazyGhostTrait;
use Symfony\Component\VarExporter\LazyObjectInterface;

final class LazyEmbeddedDocument implements LazyObjectInterface
{
    use LazyGhostTrait;

    public string $foo;
    public string $baz;
}
