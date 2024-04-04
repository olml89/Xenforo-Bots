<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Bot;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Finder\BotFinder;

final class Retrieve
{
    public function __construct(
        private readonly BotFinder $botFinder,
    ) {}

    /**
     * @throws BotNotFoundException
     */
    public function retrieve(string $bot_id): Bot
    {
        return $this->botFinder->find($bot_id);
    }
}
