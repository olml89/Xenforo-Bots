<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Entity;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\ApiKeyNotAuthorizedException;
use XF\Api\Result\EntityResult;

/**
 * @extends \XF\Entity\ApiKey
 *
 * RELATIONS
 *
 * @property-read User $User
 */
final class ApiKey extends XFCP_ApiKey
{
    public function same(ApiKey $apiKey): bool
    {
        return $this->api_key_id === $apiKey->api_key_id;
    }

    /**
     * @throws ApiKeyNotAuthorizedException
     */
    public function owns(Bot $bot): void
    {
        if (!$this->same($bot->Owner)) {
            throw ApiKeyNotAuthorizedException::doesNotOwn($this, $bot);
        }
    }

    protected function setupApiResultData(
        EntityResult $result,
        $verbosity = self::VERBOSITY_NORMAL,
        array $options = [],
    ): void {
        $result->skipColumn('api_key_id');
        $result->includeColumn('api_key');
    }
}
