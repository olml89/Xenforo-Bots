<?php declare(strict_types=1);

namespace olml89\Subscriptions\Services\XFUserFinder;

use olml89\Subscriptions\Repositories\XFUserRepository;
use olml89\Subscriptions\ValueObjects\AutoId\AutoId;
use XF\Entity\User as XFUser;

final class XFUserFinder
{
    public function __construct(
        private readonly XFUserRepository $xFUserRepository,
    ) {}

    /**
     * @throws XFUserNotFoundException
     */
    public function find(AutoId $userId, string $password): XFUser
    {
        $xFUser = $this->xFUserRepository->get($userId) ?? throw XFUserNotFoundException::unexisting($userId);

        if (!$xFUser->Auth->authenticate($password)) {
            throw XFUserNotFoundException::invalidPassword();
        }

        return $xFUser;
    }
}
