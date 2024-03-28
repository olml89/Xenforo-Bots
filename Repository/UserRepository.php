<?php declare(strict_types=1);

namespace olml89\XenforoBots\Repository;

use olml89\XenforoBots\Exception\UserStorageException;
use Throwable;
use XF\Entity\User;

final class UserRepository
{
    /**
     * @throws UserStorageException
     */
    public function save(User $user): void
    {
        try {
            $user->save();
        }
        catch (Throwable $e) {
            throw UserStorageException::entity($user, $e);
        }
    }
}
