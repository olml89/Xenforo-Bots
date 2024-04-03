<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\ConversationMessage;

use olml89\XenforoBots\UseCase\JsonSerializableObject;
use XF\Entity\ConversationMessage;

final class ConversationMessageData extends JsonSerializableObject
{
    public readonly string $bot_id;
    public readonly int $message_id;
    public readonly int $conversation_id;
    public readonly int $sender_id;
    public readonly string $sender_name;
    public readonly int $message_date;
    public readonly string $message;

    public function __construct(ConversationMessage $conversationMessage)
    {
        $this->message_id = $conversationMessage->message_id;
        $this->conversation_id = $conversationMessage->conversation_id;
        $this->sender_id = $conversationMessage->user_id;
        $this->sender_name = $conversationMessage->username;
        $this->message_date = $conversationMessage->message_date;
        $this->message = $conversationMessage->message;
    }
}
