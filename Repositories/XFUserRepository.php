<?php declare(strict_types=1);

namespace olml89\Subscriptions\Repositories;

use olml89\Subscriptions\ValueObjects\UserId\UserId;
use XF\Entity\User;
use XF\Mvc\Entity\Manager;

final class XFUserRepository
{
    public function __construct(
        private readonly Manager $entityManager,
    ) {}

    public function get(UserId $userId) : ?User
    {
        return $this->entityManager->findOne(
            shortName: 'XF:User',
            where: ['user_id' => $userId->value]
        );
    }
}
