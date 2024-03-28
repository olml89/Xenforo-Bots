<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCase\Bot;

use olml89\XenforoSubscriptions\Entity\Bot;
use olml89\XenforoSubscriptions\Exception\ApiKeyCreationException;
use olml89\XenforoSubscriptions\Exception\ApiKeyStorageException;
use olml89\XenforoSubscriptions\Exception\BotCreationException;
use olml89\XenforoSubscriptions\Exception\BotStorageException;
use olml89\XenforoSubscriptions\Exception\UserCreationException;
use olml89\XenforoSubscriptions\Exception\UserStorageException;
use olml89\XenforoSubscriptions\Factory\ApiKeyFactory;
use olml89\XenforoSubscriptions\Factory\BotFactory;
use olml89\XenforoSubscriptions\Factory\UserFactory;
use olml89\XenforoSubscriptions\Repository\ApiKeyRepository;
use olml89\XenforoSubscriptions\Repository\BotRepository;
use olml89\XenforoSubscriptions\Repository\UserRepository;
use XF\Db\AbstractAdapter;
use XF\Entity\ApiKey;
use XF\Entity\User;

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
     * @throws BotCreationException
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
        catch (BotCreationException|BotStorageException $e) {
            $this->database->rollback();
            throw $e;
        }
    }

    /**
     * @throws BotCreationException
     * @throws BotStorageException
     */
    private function createBot(string $username, string $password): Bot
    {
        try {
            $user = $this->createUser($username, $password);
            $bot = $this->botFactory->create($user);
            $apiKey = $this->createApiKey($bot);
            $bot->api_key_id = $apiKey->api_key_id;
            $this->botRepository->save($bot);

            return $bot;
        }
        catch (UserCreationException|UserStorageException|ApiKeyCreationException|ApiKeyStorageException $e) {
            throw BotCreationException::childEntityException($e);
        }
    }

    /**
     * @throws UserCreationException
     * @throws UserStorageException
     */
    private function createUser(string $username, string $password): User
    {
        $user = $this->userFactory->create($username, $password);
        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @throws ApiKeyCreationException
     * @throws ApiKeyStorageException
     */
    private function createApiKey(Bot $bot): ApiKey
    {
        $apiKey = $this->apiKeyFactory->create($bot);
        $this->apiKeyRepository->save($apiKey);

        return $apiKey;
    }
}
