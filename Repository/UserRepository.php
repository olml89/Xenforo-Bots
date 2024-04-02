<?php declare(strict_types=1);

namespace olml89\XenforoBots\Repository;

use olml89\XenforoBots\Exception\UserStorageException;
use olml89\XenforoBots\Service\ErrorHandler;
use olml89\XenforoBots\XF\Entity\User;
use Throwable;

final class UserRepository
{
    public function __construct(
        private readonly ErrorHandler $errorHandler,
    ) {}

    /**
     * @throws UserStorageException
     */
    public function save(User $user): void
    {
        try {
            $user->save();
        }
        catch (Throwable $e) {
            throw UserStorageException::entity(
                entity: $user,
                context: $this->errorHandler->handle($e),
            );
        }
    }
}
