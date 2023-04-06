<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCases;

use JsonSerializable;

abstract class DataTransferObject implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return (array)$this;
    }
}
