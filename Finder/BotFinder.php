<?php declare(strict_types=1);

namespace olml89\XenforoBots\Finder;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Repository\BotRepository;

final class BotFinder
{
    public function __construct(
        private readonly BotRepository $botRepository,
    ) {}

    /**
     * @throws BotNotFoundException
     */
    public function find(string $bot_id): Bot
    {
        return $this->botRepository->get($bot_id) ?? throw BotNotFoundException::id($bot_id);
    }
}
