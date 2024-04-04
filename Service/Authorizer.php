<?php declare(strict_types=1);

namespace olml89\XenforoBots\Service;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\BotNotAuthorizedException;
use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Exception\UserNotAuthorizedException;
use olml89\XenforoBots\Finder\BotFinder;
use olml89\XenforoBots\XF\Entity\ApiKey;
use XF;

final class Authorizer
{
    public function __construct(
        private readonly BotFinder $botFinder,
    ) {}

    /**
     * @throws UserNotAuthorizedException
     */
    public function assertSuperUserKey(): void
    {
        $apiKey = XF::apiKey();

        if ($apiKey->key_type !== 'super') {
            throw UserNotAuthorizedException::noSuperUser($apiKey->User);
        }
    }

    /**
     * @throws UserNotAuthorizedException
     * @throws BotNotFoundException
     * @throws BotNotAuthorizedException
     */
    public function getAuthorizedBot(string $bot_id): Bot
    {
        $authenticatedBot = $this->getAuthenticatedBot();
        $requestedBot = $this->botFinder->find($bot_id);

        if (!$authenticatedBot->same($requestedBot)) {
            throw BotNotAuthorizedException::notAllowed($authenticatedBot);
        }

        return $requestedBot;
    }

    /**
     * @throws UserNotAuthorizedException
     */
    private function getAuthenticatedBot(): Bot
    {
        /** @var ApiKey $apiKey */
        $apiKey = XF::apiKey();

        if ($apiKey->key_type !== 'user') {
            UserNotAuthorizedException::noPlainUser($apiKey->User);
        }

        $authenticatedUser = $apiKey->User;

        return $authenticatedUser->Bot ?? throw UserNotAuthorizedException::noBot($authenticatedUser);
    }
}
