<?php declare(strict_types=1);

namespace olml89\XenforoBots\Entity;

use Countable;
use Iterator;

final class ActiveBotSubscriptionCollection implements Iterator, Countable
{
    /**
     * @var BotSubscription[]
     */
    private array $activeBotSubscriptions = [];

    public function __construct(Bot ...$bots) {
        foreach ($bots as $bot) {
            foreach ($bot->BotSubscriptions as $botSubscription) {
                if ($botSubscription->is_active) {
                    $this->activeBotSubscriptions[] = $botSubscription;
                }
            }
        }
    }

    public function count(): int
    {
        return count($this->activeBotSubscriptions);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }


    public function current(): BotSubscription
    {
        return current($this->activeBotSubscriptions);
    }

    public function next(): void
    {
        next($this->activeBotSubscriptions);
    }

    public function key(): int
    {
        return key($this->activeBotSubscriptions);
    }

    public function valid(): bool
    {
        return !is_null(key($this->activeBotSubscriptions));
    }

    public function rewind(): void
    {
        reset($this->activeBotSubscriptions);
    }
}
