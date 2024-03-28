<?php declare(strict_types=1);

namespace olml89\XenforoBots\Service;

use olml89\XenforoBots\Exception\XFUserNotFoundException;
use olml89\XenforoBots\Repository\XFUserRepository;
use olml89\XenforoBots\XF\Entity\User as XFUser;

final class XFUserFinder
{
    public function __construct(
        private readonly XFUserRepository $userRepository,
    ) {}

    /**
     * @throws XFUserNotFoundException
     */
    public function find(int $user_id): XFUser
    {
        return $this->userRepository->get($user_id)
            ?? throw XFUserNotFoundException::unexisting($user_id);
    }

    /**
     * @throws XFUserNotFoundException
     */
    public function findWithPassword(int $user_id, string $password): XFUser
    {
        $xFUser = $this->find($user_id);

        if (!$xFUser->Auth->authenticate($password)) {
            throw XFUserNotFoundException::invalidPassword();
        }

        return $xFUser;
    }
}
