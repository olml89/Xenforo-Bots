<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase;

use JsonSerializable;

abstract class JsonSerializableObject implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return (array)$this;
    }
}
