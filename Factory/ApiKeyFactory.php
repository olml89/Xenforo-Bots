<?php declare(strict_types=1);

namespace olml89\XenforoBots\Factory;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\ApiKeyValidationException;
use XF\App;
use XF\Entity\ApiKey;
use XF\Mvc\Entity\Manager as EntityManager;
use XF\Service\ApiKey\Manager as ApiKeyManager;

final class ApiKeyFactory
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly App $app,
    ) {}

    /**
     * @throws ApiKeyValidationException
     */
    public function create(Bot $bot): ApiKey
    {
        $apiKeyManager = $this->instantiateApiKeyManager($bot);
        $apiKey = $apiKeyManager->getKey();

        if ($apiKey->hasErrors()) {
            throw ApiKeyValidationException::entity($apiKey);
        }

        return $apiKey;
    }

    private function instantiateApiKeyManager(Bot $bot): ApiKeyManager
    {
        /** @var ApiKey $apiKey */
        $apiKey = $this->entityManager->create(
            shortName: 'XF:ApiKey',
        );

        $apiKeyManager = new ApiKeyManager($this->app, $apiKey);

        $apiKeyManager->setTitle(sprintf(
            'Bot %s',
            $bot->bot_id,
        ));

        $apiKeyManager->setActive(true);
        $apiKeyManager->setScopes(false, $bot::SCOPES);
        $apiKeyManager->setKeyType('user', $bot->User->username);

        return $apiKeyManager;
    }
}
