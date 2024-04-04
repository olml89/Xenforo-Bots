<?php declare(strict_types=1);

namespace olml89\XenforoBots\Finder;
use olml89\XenforoBots\Exception\ConversationMessageNotFoundException;
use XF\Entity\ConversationMessage;
use XF\Mvc\Entity\Finder;

final class ConversationMessageFinder
{
    public function __construct(
        private readonly Finder $conversationMessageFinder,
    ) {}

    /**
     * @throws ConversationMessageNotFoundException
     */
    public function find(int $conversation_message_id): ConversationMessage
    {
        return $this->getConversationMessage($conversation_message_id)
            ?? throw ConversationMessageNotFoundException::id($conversation_message_id);
    }

    private function getConversationMessage(int $conversation_message_id): ?ConversationMessage
    {
        /**
         * @var ConversationMessage
         */
        return $this->conversationMessageFinder->whereId($conversation_message_id)->fetchOne();
    }
}
