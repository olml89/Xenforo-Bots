<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Post;

use olml89\XenforoBots\Entity\ActiveBotSubscriptionCollection;
use olml89\XenforoBots\Repository\BotRepository;
use olml89\XenforoBots\Service\WebhookNotifier;
use olml89\XenforoBots\XF\Entity\Post;

final class Notify
{
    private const POSTS_ENDPOINT = '/posts';

    public function __construct(
        private readonly BotRepository $botRepository,
        private readonly WebhookNotifier $webhookNotifier,
    ) {}

    public function notify(Post $post): void
    {
        $notifiableBots = $this->botRepository->getAll();
        $activeBotSubscriptions = new ActiveBotSubscriptionCollection(...$notifiableBots);

        $this->webhookNotifier->notify(
            endpoint: self::POSTS_ENDPOINT,
            data: new PostData($post),
            botSubscriptions: $activeBotSubscriptions,
        );
    }
}
