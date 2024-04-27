<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification\Content;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\Entity\BotSubscriptionCollection;
use olml89\XenforoBots\Job\NotifyContentJob;
use olml89\XenforoBots\Job\NotifyNotifiableJobData;
use olml89\XenforoBots\UseCase\Notification\Data;
use olml89\XenforoBots\UseCase\Notification\NoActiveBotSubscriptionsException;
use olml89\XenforoBots\UseCase\Notification\Notifiable;
use XF\Entity\Post;

final class Content implements Notifiable
{
    private const ENDPOINT = 'contents';

    private readonly Post $post;
    private readonly BotSubscriptionCollection $botSubscriptions;
    private readonly NotifyNotifiableJobData $jobCreationData;
    private readonly Data $data;

    /**
     * @throws NoActiveBotSubscriptionsException
     */
    public function __construct(Post $post, BotSubscriptionCollection $botSubscriptions)
    {
        $this->post = $post;

        $this->botSubscriptions = $botSubscriptions->filter(
            fn (BotSubscription $botSubscription): bool => $botSubscription->is_active
        );

        if ($this->botSubscriptions->isEmpty()) {
            throw new NoActiveBotSubscriptionsException($this->post);
        }

        $this->jobCreationData = NotifyContentJob::getCreationData($this->post);
        $this->data = Data::fromPost($this->post);
    }

    public function jobCreationData(): NotifyNotifiableJobData
    {
        return $this->jobCreationData;
    }

    public function entity(): Post
    {
        return $this->post;
    }

    public function endpoint(Bot $bot): string
    {
        return self::ENDPOINT;
    }

    public function botSubscriptions(): BotSubscriptionCollection
    {
        return $this->botSubscriptions;
    }

    public function data(): Data
    {
        return $this->data;
    }
}