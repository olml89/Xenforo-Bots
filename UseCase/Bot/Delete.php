<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Bot;

use olml89\XenforoBots\Exception\ApiKeyNotAuthorizedException;
use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Exception\BotRemovalException;
use olml89\XenforoBots\Repository\BotRepository;
use olml89\XenforoBots\Finder\BotFinder;
use olml89\XenforoBots\XF\Entity\ApiKey;

final class Delete
{
    public function __construct(
        private readonly BotFinder $botFinder,
        private readonly BotRepository $botRepository,
    ) {}

    /**
     * @throws ApiKeyNotAuthorizedException
     * @throws BotNotFoundException
     * @throws BotRemovalException
     */
    public function delete(ApiKey $owner, string $bot_id): void
    {
        $bot = $this->botFinder->find($bot_id);
        $owner->owns($bot);

        $this->botRepository->delete($bot);
    }
}
