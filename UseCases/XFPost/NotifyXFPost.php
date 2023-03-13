<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\XFPost;

use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\Services\WebhookNotifier\WebhookNotifier;
use XF\Entity\Post as XFPost;

final class NotifyXFPost
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
            subscriptions: $this->subscriptionRepository->getByWebhook(),
            data: new XFPostData($xFPost),
        );
    }
}
