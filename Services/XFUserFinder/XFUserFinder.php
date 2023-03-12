<?php declare(strict_types=1);

namespace olml89\Subscriptions\Services\XFUserFinder;

use olml89\Subscriptions\Repositories\XFUserRepository;
use olml89\Subscriptions\ValueObjects\UserId\UserId;
use XF\Entity\User;

final class XFUserFinder
{
    public function __construct(
        private readonly XFUserRepository $xFUserRepository,
    ) {}

    /**
     * @throws XFUserNotFoundException
     */
    public function find(UserId $userId): User
    {
        return $this->xFUserRepository->get($userId) ?? throw new XFUserNotFoundException($userId);
    }
}
