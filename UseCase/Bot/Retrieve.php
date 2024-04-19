<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Bot;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\ApiKeyNotAuthorizedException;
use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Finder\BotFinder;
use olml89\XenforoBots\XF\Entity\ApiKey;

final class Retrieve
{
    public function __construct(
        private readonly BotFinder $botFinder,
    ) {}

    /**
     * @throws ApiKeyNotAuthorizedException
     * @throws BotNotFoundException
     */
    public function retrieve(ApiKey $owner, string $bot_id): Bot
    {
        $bot = $this->botFinder->find($bot_id);
        $owner->owns($bot);

        return $bot;
    }
}
