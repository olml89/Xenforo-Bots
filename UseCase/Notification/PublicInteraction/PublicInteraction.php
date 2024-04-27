<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification\PublicInteraction;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\Entity\BotSubscriptionCollection;
use olml89\XenforoBots\Job\NotifyNotifiableJobData;
use olml89\XenforoBots\Job\NotifyPublicInteractionJob;
use olml89\XenforoBots\UseCase\Notification\Data;
use olml89\XenforoBots\UseCase\Notification\NoActiveBotSubscriptionsException;
use olml89\XenforoBots\UseCase\Notification\Notifiable;
use olml89\XenforoBots\UseCase\Notification\SingularInteraction;
use XF\Entity\Post;

final class PublicInteraction implements Notifiable, SingularInteraction
{
    private const ENDPOINT = 'bots/%s/interactions/public';

    private readonly Post $post;
    private readonly Bot $bot;
    private readonly BotSubscriptionCollection $botSubscriptions;
    private readonly NotifyNotifiableJobData $jobCreationData;
    private readonly Data $data;

    /**
     * @throws NoActiveBotSubscriptionsException
     */
    public function __construct(Post $post, Bot $bot)
    {
        $this->post = $post;
        $this->bot = $bot;

        $this->botSubscriptions = $this
            ->bot
            ->BotSubscriptions
            ->filter(
                fn (BotSubscription $botSubscription): bool => $botSubscription->is_active
            );

        if ($this->botSubscriptions->isEmpty()) {
            throw new NoActiveBotSubscriptionsException($this->post);
        }

        $this->jobCreationData = NotifyPublicInteractionJob::getCreationData($this->post, $this->bot);
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

    public function bot(): Bot
    {
        return $this->bot;
    }

    public function endpoint(Bot $bot): string
    {
        return sprintf(self::ENDPOINT, $bot->bot_id);
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