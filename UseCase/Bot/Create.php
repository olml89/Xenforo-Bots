<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Bot;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\ApiKeyValidationException;
use olml89\XenforoBots\Exception\ApiKeyStorageException;
use olml89\XenforoBots\Exception\BotValidationException;
use olml89\XenforoBots\Exception\BotStorageException;
use olml89\XenforoBots\Exception\UserValidationException;
use olml89\XenforoBots\Exception\UserStorageException;
use olml89\XenforoBots\Factory\ApiKeyFactory;
use olml89\XenforoBots\Factory\BotFactory;
use olml89\XenforoBots\Factory\UserFactory;
use olml89\XenforoBots\Repository\ApiKeyRepository;
use olml89\XenforoBots\Repository\BotRepository;
use olml89\XenforoBots\Repository\UserRepository;
use olml89\XenforoBots\XF\Entity\ApiKey;
use olml89\XenforoBots\XF\Entity\User;
use XF\Db\AbstractAdapter;

final class Create
{
    public function __construct(
        private readonly AbstractAdapter $database,
        private readonly UserFactory $userFactory,
        private readonly UserRepository $userRepository,
        private readonly ApiKeyFactory $apiKeyFactory,
        private readonly ApiKeyRepository $apiKeyRepository,
        private readonly BotFactory $botFactory,
        private readonly BotRepository $botRepository,
    ) {}

    /**
     * @throws BotValidationException
     * @throws BotStorageException
     */
    public function create(string $username, string $password): Bot
    {
        try {
            $this->database->beginTransaction();
            $bot = $this->createBot($username, $password);
            $this->database->commit();

            return $bot;
        }
        catch (BotValidationException|BotStorageException $e) {
            $this->database->rollback();
            throw $e;
        }
    }

    /**
     * @throws BotValidationException
     * @throws BotStorageException
     */
    private function createBot(string $username, string $password): Bot
    {
        try {
            $user = $this->createUser($username, $password);
            $bot = $this->botFactory->create($user);
            $apiKey = $this->createApiKey($bot);
            $bot->setApiKey($apiKey);
            $this->botRepository->save($bot);

            return $bot;
        }
        catch (UserValidationException|UserStorageException|ApiKeyValidationException|ApiKeyStorageException $e) {
            //throw $e;
            throw BotValidationException::fromDomainException($e);
        }
    }

    /**
     * @throws UserValidationException
     * @throws UserStorageException
     */
    private function createUser(string $username, string $password): User
    {
        $user = $this->userFactory->create($username, $password);
        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @throws ApiKeyValidationException
     * @throws ApiKeyStorageException
     */
    private function createApiKey(Bot $bot): ApiKey
    {
        $apiKey = $this->apiKeyFactory->create($bot);
        $this->apiKeyRepository->save($apiKey);

        return $apiKey;
    }
}
