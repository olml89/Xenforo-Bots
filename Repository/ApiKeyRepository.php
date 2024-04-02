<?php declare(strict_types=1);

namespace olml89\XenforoBots\Repository;

use olml89\XenforoBots\Exception\ApiKeyStorageException;
use olml89\XenforoBots\XF\Entity\ApiKey;
use Throwable;

final class ApiKeyRepository
{
    /**
     * @throws ApiKeyStorageException
     */
    public function save(ApiKey $apiKey): void
    {
        try {
            $apiKey->save();
        }
        catch (Throwable $e) {
            throw ApiKeyStorageException::entity($apiKey, $e);
        }
    }
}
