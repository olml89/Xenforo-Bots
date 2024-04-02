<?php declare(strict_types=1);

namespace olml89\XenforoBots\Factory;

use olml89\XenforoBots\Exception\UserValidationException;
use XF\Entity\User;
use XF\PrintableException;
use XF\Repository\User as UserRepository;

final class UserFactory
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    /**
     * @throws UserValidationException
     */
    public function create(string $username, string $password): User
    {
        $user = $this->instantiateUser($username, $password);

        if ($user->hasErrors()) {
            throw UserValidationException::entity($user);
        }

        return $user;
    }

    private function instantiateUser(string $username, string $password): User
    {
        $user = $this->userRepository->setupBaseUser();

        $user->username = $username;
        $user->Auth->setPassword($password);

        // This allows the creation of an User without a set email address
        $user->setOption('admin_edit', true);

        return $user;
    }
}
