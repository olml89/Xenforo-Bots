<?php declare(strict_types=1);

namespace olml89\XenforoBots\Job;

use olml89\XenforoBots\Exception\PostNotFoundException;
use olml89\XenforoBots\Finder\PostFinder;
use olml89\XenforoBots\UseCase\Post\Notify;
use XF\App;
use XF\Entity\Post;

final class NotifyPostCreationJob extends NotifyEntityCreationJob
{
    private readonly PostFinder $postFinder;
    private readonly Notify $notifyPost;

    public function __construct(App $app, int|string $jobId, array $data = [])
    {
        $this->postFinder = $app->get(PostFinder::class);
        $this->notifyPost = $app->get(Notify::class);

        parent::__construct($app, $jobId, $data);
    }

    /**
     * @param float|int $maxRunTime
     *
     * @throws PostNotFoundException
     */
    public function run($maxRunTime): void
    {
        $post = $this->postFinder->find($this->getEntityId());

        $this->notifyPost->notify($post);
    }

    protected static function entityClass(): string
    {
        return Post::class;
    }
}
