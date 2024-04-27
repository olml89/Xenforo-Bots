<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification;

use Exception;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use XF\Entity\Post;

final class NoActiveBotSubscriptionsException extends Exception
{
    public function __construct(ConversationMessage|Post $entity)
    {
        parent::__construct(
            sprintf(
                '%s is not notifiable because it does not involve Bots with active BotSubscriptions',
                $entity::class,
            )
        );
    }
}