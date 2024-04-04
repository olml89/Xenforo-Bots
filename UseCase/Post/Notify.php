<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Post;

use olml89\XenforoBots\Entity\BotSubscriptionCollection;
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
        $botSubscriptions = new BotSubscriptionCollection();

        foreach ($notifiableBots as $notifiableBot) {
            $botSubscriptions = $botSubscriptions->merge($notifiableBot->BotSubscriptions);
        }

        $this->webhookNotifier->notify(
            endpoint: self::POSTS_ENDPOINT,
            data: new PostData($post),
            botSubscriptions: $botSubscriptions,
        );
    }
}
