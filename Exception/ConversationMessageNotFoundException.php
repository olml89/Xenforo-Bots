<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\XF\Entity\ConversationMessage;

final class ConversationMessageNotFoundException extends EntityNotFoundException
{
    protected static function errorCode(): string
    {
        return 'conversationMessage.retrieval.not_found';
    }

    protected static function entityClass(): string
    {
        return ConversationMessage::class;
    }
}
