<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCase\Bot;

use olml89\XenforoSubscriptions\Entity\Bot;
use olml89\XenforoSubscriptions\Exception\BotNotFoundException;
use olml89\XenforoSubscriptions\Repository\BotRepository;

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
