<?php declare(strict_types=1);

namespace olml89\Subscriptions\XF\Api\Result;

use JsonSerializable;
use XF\Api\Result\ResultInterface;

final class UseCaseResponse implements ResultInterface
{
    public function __construct(
        private readonly JsonSerializable $result,
    ) {}

    public function render(): array
    {
        return $this->result->jsonSerialize();
    }
}
