<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use XF\Entity\Post;

final class PostNotFoundException extends EntityNotFoundException
{
    protected static function errorCode(): string
    {
        return 'post.retrieval.not_found';
    }

    protected static function entityClass(): string
    {
        return Post::class;
    }
}
