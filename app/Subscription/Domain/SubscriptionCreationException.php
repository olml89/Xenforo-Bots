<?php declare(strict_types=1);

namespace olml89\XenforoBots\Subscription\Domain;

use Exception;
use olml89\XenforoBots\Bot\Domain\Bot;
use Throwable;

final class SubscriptionCreationException extends Exception
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct(
            message: $message,
            previous: $previous,
        );
    }

    public static function alreadySubscribed(Bot $bot): self
    {
        return new self(
            sprintf(
                'Bot <%s> is already subscribed to \'%s\'',
                $bot->id(),
                $bot->subscription()->xenforoUrl(),
            )
        );
    }
}
