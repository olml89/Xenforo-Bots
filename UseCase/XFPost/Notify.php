<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\XFPost;

use olml89\XenforoBots\Repository\BotSubscriptionRepository;
use olml89\XenforoBots\Service\WebhookNotifier;
use XF\Entity\Post as XFPost;

final class Notify
{
    private const POSTS_ENDPOINT = '/posts';

    public function __construct(
        private readonly BotSubscriptionRepository $subscriptionRepository,
        private readonly WebhookNotifier           $webhookNotifier,
    ) {}

    public function notify(XFPost $xFPost): void
    {
        $this->webhookNotifier->notify(
            endpoint: self::POSTS_ENDPOINT,
            subscriptions: $this->subscriptionRepository->groupByWebhook(),
            data: new XFPostData($xFPost),
        );
    }
}
