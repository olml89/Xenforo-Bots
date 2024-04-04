<?php declare(strict_types=1);

namespace olml89\XenforoBots\Finder;

use olml89\XenforoBots\Exception\PostNotFoundException;
use XF\Entity\Post;
use XF\Mvc\Entity\Finder;

final class PostFinder
{
    public function __construct(
        private readonly Finder $postFinder,
    ) {}

    /**
     * @throws PostNotFoundException
     */
    public function find(int $post_id): Post
    {
        return $this->getPost($post_id) ?? throw PostNotFoundException::id($post_id);
    }

    private function getPost(int $post_id): ?Post
    {
        /**
         * @var Post
         */
        return $this->postFinder->whereId($post_id)->fetchOne();
    }
}
