<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Entity;

use XF\Api\Result\EntityResult;

/**
 * RELATIONS
 *
 * @property-read User $User
 */
final class ApiKey extends XFCP_ApiKey
{
    protected function setupApiResultData(
        EntityResult $result,
        $verbosity = self::VERBOSITY_NORMAL,
        array $options = [],
    ): void {
        $result->skipColumn('api_key_id');
        $result->includeColumn('api_key');
    }
}
