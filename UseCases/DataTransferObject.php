<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases;

use JsonSerializable;

abstract class DataTransferObject implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return (array)$this;
    }
}
