<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCase\XFPost;

use olml89\XenforoSubscriptions\Repository\SubscriptionRepository;
use olml89\XenforoSubscriptions\Service\WebhookNotifier;
use XF\Entity\Post as XFPost;

final class Notify
{
    private const POSTS_ENDPOINT = '/posts';

    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly WebhookNotifier $webhookNotifier,
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
