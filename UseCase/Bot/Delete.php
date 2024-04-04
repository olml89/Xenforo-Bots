<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Bot;

use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Exception\BotRemovalException;
use olml89\XenforoBots\Repository\BotRepository;
use olml89\XenforoBots\Finder\BotFinder;

final class Delete
{
    public function __construct(
        private readonly BotFinder $botFinder,
        private readonly BotRepository $botRepository,
    ) {}

    /**
     * @throws BotNotFoundException
     * @throws BotRemovalException
     */
    public function delete(string $bot_id): void
    {
        $bot = $this->botFinder->find($bot_id);
        $this->botRepository->delete($bot);
    }
}
