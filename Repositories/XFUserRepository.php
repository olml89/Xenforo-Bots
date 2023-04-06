<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Repositories;

use olml89\XenforoSubscriptions\ValueObjects\AutoId\AutoId;
use XF\Entity\User;
use XF\Mvc\Entity\Manager;

final class XFUserRepository
{
    public function __construct(
        private readonly Manager $entityManager,
    ) {}

    public function get(AutoId $userId) : ?User
    {
        return $this->entityManager->findOne(
            shortName: 'XF:User',
            where: ['user_id' => $userId->toInt()]
        );
    }
}
