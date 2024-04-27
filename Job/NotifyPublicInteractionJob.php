<?php declare(strict_types=1);

namespace olml89\XenforoBots\Job;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Exception\PostNotFoundException;
use olml89\XenforoBots\Exception\UserAlertNotFoundException;
use olml89\XenforoBots\Finder\BotFinder;
use olml89\XenforoBots\Finder\PostFinder;
use olml89\XenforoBots\Finder\UserAlertFinder;
use olml89\XenforoBots\UseCase\Notification\NoActiveBotSubscriptionsException;
use olml89\XenforoBots\UseCase\Notification\Notify;
use olml89\XenforoBots\UseCase\Notification\UnnotifiableUserAlertException;
use olml89\XenforoBots\UseCase\Notification\PublicInteraction\PublicInteraction;
use XF\App;
use XF\Entity\Post;
use XF\Job\JobResult;

final class NotifyPublicInteractionJob extends NotifyNotifiableJob
{
    private readonly PostFinder $postFinder;
    private readonly BotFinder $botFinder;
    private readonly Notify $notifyNotifiable;

    public function __construct(App $app, int|string $jobId, array $data = [])
    {
        $this->postFinder = $app->get(PostFinder::class);
        $this->botFinder = $app->get(BotFinder::class);
        $this->notifyNotifiable = $app->get(Notify::class);

        parent::__construct($app, $jobId, $data);
    }

    public static function getCreationData(Post $post, Bot $bot): NotifyNotifiableJobData
    {
        return new NotifyNotifiableJobData(
            uniqueId: self::getUniqueId($post),
            jobClass: self::class,
            params: [
                'post_id' => $post->getEntityId(),
                'bot_id' => $bot->getEntityId(),
            ]
        );
    }

    /**
     * @param float|int $maxRunTime
     *
     * @throws BotNotFoundException
     * @throws PostNotFoundException
     * @throws NoActiveBotSubscriptionsException
     */
    public function run($maxRunTime): JobResult
    {
        $publicInteraction = $this->getPublicInteraction();

        $cachedRequests = $this->notifyNotifiable->notify(
            $publicInteraction,
            $this->getCachedRequests()
        );

        return $this->getJobResult($cachedRequests);
    }

    /**
     * @throws BotNotFoundException
     * @throws PostNotFoundException
     * @throws NoActiveBotSubscriptionsException
     */
    private function getPublicInteraction(): PublicInteraction
    {
        return new PublicInteraction(
            post: $this->postFinder->find($this->getEntityId()),
            bot: $this->botFinder->find($this->data['bot_id'])
        );
    }

    protected function getEntityId(): int|string
    {
        return $this->data['post_id'];
    }

    protected static function notifiableClass(): string
    {
        return PublicInteraction::class;
    }

    protected static function entityClass(): string
    {
        return Post::class;
    }
}
