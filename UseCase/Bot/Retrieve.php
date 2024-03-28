<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Bot;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Repository\BotRepository;

final class Retrieve
{
    public function __construct(
        private readonly BotRepository $botRepository,
    ) {}

    /**
     * @throws BotNotFoundException
     */
    public function retrieve(string $bot_id): Bot
    {
        return $this->botRepository->find($bot_id);
    }
}
