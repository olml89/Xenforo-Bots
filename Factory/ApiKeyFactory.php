<?php declare(strict_types=1);

namespace olml89\XenforoBots\Factory;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\ApiKeyValidationException;
use olml89\XenforoBots\XF\Entity\ApiKey;
use XF\App;
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
        $apiKey = $this->instantiateApiKey($bot);

        if ($apiKey->hasErrors()) {
            throw ApiKeyValidationException::entity($apiKey);
        }

        return $apiKey;
    }

    private function instantiateApiKey(Bot $bot): ApiKey
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

        return $apiKey;
    }
}
