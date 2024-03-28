<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\XFPost;

use olml89\XenforoBots\UseCase\JsonSerializableObject;
use XF\Entity\Post as XFPost;

final class XFPostData extends JsonSerializableObject
{
    public readonly int $post_id;
    public readonly int $thread_id;
    public readonly int $author_id;
    public readonly string $author_name;
    public readonly int $create_date;
    public readonly int $update_date;
    public readonly string $message;

    public function __construct(XFPost $post)
    {
        $this->post_id = $post->post_id ;
        $this->thread_id = $post->thread_id ;
        $this->author_id = $post->user_id ;
        $this->author_name = $post->username ;
        $this->create_date = $post->post_date ;
        $this->update_date = $post->last_edit_date ;
        $this->message = $post->message ;
    }
}
