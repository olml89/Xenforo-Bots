<?php declare(strict_types=1);

namespace olml89\XenforoBots\Entity;

use Countable;
use Iterator;
use olml89\XenforoBots\Exception\BotSubscriptionNotFoundException;

final class BotSubscriptionCollection implements Countable, Iterator
{
    /**
     * @var array<string, BotSubscription>
     */
    private array $botSubscriptions = [];

    public function __construct(BotSubscription ...$botSubscriptions)
    {
        foreach ($botSubscriptions as $botSubscription) {
            $this->set($botSubscription);
        }
    }

    public function has(string $bot_subscription_id): bool
    {
        return array_key_exists($bot_subscription_id, $this->botSubscriptions);
    }

    /**
     * @throws BotSubscriptionNotFoundException
     */
    public function get(string $bot_subscription_id): BotSubscription
    {
        if (!$this->has($bot_subscription_id)) {
            throw BotSubscriptionNotFoundException::id($bot_subscription_id);
        }

        return $this->botSubscriptions[$bot_subscription_id];
    }

    public function set(BotSubscription $botSubscription): void
    {
        $this->botSubscriptions[$botSubscription->bot_subscription_id] = $botSubscription;
    }

    /**
     * @throws BotSubscriptionNotFoundException
     */
    public function remove(BotSubscription $botSubscription): void
    {
        if (!$this->has($botSubscription->bot_subscription_id)) {
            throw BotSubscriptionNotFoundException::id($botSubscription->bot_subscription_id);
        }

        unset($this->botSubscriptions[$botSubscription->bot_subscription_id]);
    }

    public function merge(BotSubscriptionCollection $botSubscriptions): self
    {
        $mergedBotSubscriptions = new BotSubscriptionCollection(...$this->botSubscriptions);

        foreach ($botSubscriptions->botSubscriptions as $botSubscription) {
            $mergedBotSubscriptions->set($botSubscription);
        }

        return $mergedBotSubscriptions;
    }

    /**
     * @param callable(BotSubscription):bool $callable
     */
    public function search(callable $callable): ?BotSubscription
    {
        foreach ($this->botSubscriptions as $botSubscription) {
            if ($callable($botSubscription)) {
                return $botSubscription;
            }
        }

        return null;
    }

    /**
     * @throws BotSubscriptionNotFoundException
     */
    public function find(callable $callable, string $errorMessage = 'not found with specified search conditions'): BotSubscription
    {
        return $this->search($callable) ?? throw BotSubscriptionNotFoundException::withMessage($errorMessage);
    }

    /**
     * @param callable(BotSubscription):bool $callable
     */
    public function contains(callable $callable): bool
    {
        return !is_null($this->search($callable));
    }

    /**
     * @param callable(BotSubscription): bool $callable
     */
    public function filter(callable $callable): self
    {
        $filteredBotSubscriptions = array_filter(
            $this->botSubscriptions,
            $callable
        );

        return new self(...$filteredBotSubscriptions);
    }

    public function count(): int
    {
        return count($this->botSubscriptions);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function current(): BotSubscription
    {
        return current($this->botSubscriptions);
    }

    public function next(): void
    {
        next($this->botSubscriptions);
    }

    public function key(): ?string
    {
        return key($this->botSubscriptions);
    }

    public function valid(): bool
    {
        return !is_null(key($this->botSubscriptions));
    }

    public function rewind(): void
    {
        reset($this->botSubscriptions);
    }

    public function toArray(): array
    {
        return $this->botSubscriptions;
    }
}
