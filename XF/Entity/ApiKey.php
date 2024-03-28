<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\XF\Entity;

use XF\Api\Result\EntityResult;

final class ApiKey extends XFCP_ApiKey
{
    protected function setupApiResultData(
        EntityResult $result,
        $verbosity = self::VERBOSITY_NORMAL,
        array $options = [],
    ): void {
        $result->api_key = $this->api_key;
    }
}
