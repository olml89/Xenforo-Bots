<?php declare(strict_types=1);

namespace olml89\XenforoBots\Repository;

use olml89\XenforoBots\Exception\ApiKeyStorageException;
use Throwable;
use XF\Entity\ApiKey;

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
