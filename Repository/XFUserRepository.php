<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Repository;

use olml89\XenforoSubscriptions\XF\Entity\User as XFUser;
use XF\Mvc\Entity\Finder;

final class XFUserRepository
{
    public function __construct(
        private readonly Finder $userFinder,
    ) {}

    public function get(int $user_id): ?XFUser
    {
        return $this->userFinder
            ->where(
                condition: 'user_id',
                operator: '=',
                value: $user_id,
            )
            ->fetchOne();
    }
}
