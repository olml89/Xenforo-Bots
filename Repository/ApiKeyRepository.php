<?php declare(strict_types=1);

namespace olml89\XenforoBots\Repository;

use olml89\XenforoBots\Exception\ApiKeyStorageException;
use olml89\XenforoBots\Service\ErrorHandler;
use olml89\XenforoBots\XF\Entity\ApiKey;
use Throwable;

final class ApiKeyRepository
{
    public function __construct(
        private readonly ErrorHandler $errorHandler,
    ) {}

    /**
     * @throws ApiKeyStorageException
     */
    public function save(ApiKey $apiKey): void
    {
        try {
            $apiKey->save();
        }
        catch (Throwable $e) {
            throw ApiKeyStorageException::entity(
                entity: $apiKey,
                context: $this->errorHandler->handle($e),
            );
        }
    }
}
