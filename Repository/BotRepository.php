<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Repository;

use olml89\XenforoSubscriptions\Entity\Bot;
use olml89\XenforoSubscriptions\Exception\BotNotFoundException;
use olml89\XenforoSubscriptions\Exception\BotRemovalException;
use olml89\XenforoSubscriptions\Exception\BotStorageException;
use Throwable;
use XF\Mvc\Entity\Finder;

final class BotRepository
{
    public function __construct(
        private readonly Finder $botFinder,
    ) {}

    public function get(string $bot_id): ?Bot
    {
        /** @var Bot $bot */
        $bot = $this
            ->botFinder
            ->where('bot_id', '=', $bot_id)
            ->fetchOne();

        return $bot;
    }

    /**
     * @throws BotNotFoundException
     */
    public function find(string $bot_id): Bot
    {
        return $this->get($bot_id) ?? throw BotNotFoundException::id($bot_id);
    }

    /**
     * @throws BotStorageException
     */
    public function save(Bot $bot): void
    {
        try {
            $bot->save();
        }
        catch (Throwable $e) {
            throw BotStorageException::entity($bot, $e);
        }
    }

    /**
     * @throws BotRemovalException
     */
    public function delete(Bot $bot): void
    {
        try {
            $bot->delete();
        }
        catch (Throwable $e) {
            throw BotRemovalException::entity($bot, $e);
        }
    }
}
