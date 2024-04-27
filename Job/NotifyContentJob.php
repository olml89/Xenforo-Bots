<?php declare(strict_types=1);

namespace olml89\XenforoBots\Job;

use olml89\XenforoBots\Exception\PostNotFoundException;
use olml89\XenforoBots\Finder\PostFinder;
use olml89\XenforoBots\UseCase\Notification\Content\Content;
use olml89\XenforoBots\UseCase\Notification\Content\ContentFactory;
use olml89\XenforoBots\UseCase\Notification\Notify;
use olml89\XenforoBots\UseCase\Notification\NoActiveBotSubscriptionsException;
use XF\App;
use XF\Entity\Post;
use XF\Job\JobResult;

final class NotifyContentJob extends NotifyNotifiableJob
{
    private readonly ContentFactory $contentFactory;
    private readonly PostFinder $postFinder;
    private readonly Notify $notifyNotifiable;

    public function __construct(App $app, int|string $jobId, array $data = [])
    {
        $this->postFinder = $app->get(PostFinder::class);
        $this->contentFactory = $app->get(ContentFactory::class);
        $this->notifyNotifiable = $app->get(Notify::class);

        parent::__construct($app, $jobId, $data);
    }

    public static function getCreationData(Post $post): NotifyNotifiableJobData
    {
        return new NotifyNotifiableJobData(
            uniqueId: self::getUniqueId($post),
            jobClass: self::class,
            params: [
                'post_id' => $post->getEntityId(),
            ]
        );
    }

    /**
     * @param float|int $maxRunTime
     *
     * @throws PostNotFoundException
     * @throws NoActiveBotSubscriptionsException
     */
    public function run($maxRunTime): JobResult
    {
        $content = $this->getContent();

        $cachedRequests = $this->notifyNotifiable->notify(
            $content,
            $this->getCachedRequests()
        );

        return $this->getJobResult($cachedRequests);
    }

    /**
     * @throws PostNotFoundException
     * @throws NoActiveBotSubscriptionsException
     */
    private function getContent(): Content
    {
        return $this->contentFactory->create(
            post: $this->postFinder->find($this->getEntityId())
        );
    }

    protected function getEntityId(): int|string
    {
        return $this->data['post_id'];
    }

    protected static function notifiableClass(): string
    {
        return Content::class;
    }

    protected static function entityClass(): string
    {
        return Post::class;
    }
}
