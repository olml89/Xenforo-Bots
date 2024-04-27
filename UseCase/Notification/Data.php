<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification;

use JsonSerializable;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use XF\Entity\Post;

final class Data implements JsonSerializable
{
    public function __construct(
        public readonly int $content_id,
        public readonly int $parent_content_id,
        public readonly int $author_id,
        public readonly string $author_name,
        public readonly int $creation_date,
        public readonly int $edition_date,
        public readonly string $message,
    ) {}

    public static function fromPost(Post $post): self
    {
        return new self(
            content_id: $post->post_id,
            parent_content_id: $post->thread_id,
            author_id: $post->user_id,
            author_name: $post->username,
            creation_date: $post->post_date,
            edition_date: $post->last_edit_date,
            message: $post->message
        );
    }

    public static function fromConversationMessage(ConversationMessage $conversationMessage): self
    {
        return new self(
            content_id: $conversationMessage->message_id,
            parent_content_id: $conversationMessage->conversation_id,
            author_id: $conversationMessage->user_id,
            author_name: $conversationMessage->username,
            creation_date: $conversationMessage->message_date,
            edition_date: $conversationMessage->message_date,
            message: $conversationMessage->message
        );
    }

    public function jsonSerialize(): array
    {
        return (array)$this;
    }
}
