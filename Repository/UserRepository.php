<?php declare(strict_types=1);

namespace olml89\XenforoBots\Repository;

use olml89\XenforoBots\Exception\UserStorageException;
use olml89\XenforoBots\XF\Entity\User;
use Throwable;

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
