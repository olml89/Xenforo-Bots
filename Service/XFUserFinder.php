<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Service;

use olml89\XenforoSubscriptions\Exception\XFUserNotFoundException;
use olml89\XenforoSubscriptions\Repository\XFUserRepository;
use XF\Entity\User as XFUser;

final class XFUserFinder
{
    public function __construct(
        private readonly XFUserRepository $userRepository,
    ) {}

    /**
     * @throws XFUserNotFoundException
     */
    public function find(int $user_id, string $password): XFUser
    {
        $xFUser = $this->userRepository->get($user_id) ?? throw XFUserNotFoundException::unexisting($user_id);

        if (!$xFUser->Auth->authenticate($password)) {
            throw XFUserNotFoundException::invalidPassword();
        }

        return $xFUser;
    }
}
