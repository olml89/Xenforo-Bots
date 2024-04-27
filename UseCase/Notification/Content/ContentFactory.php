<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification\Content;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\Entity\BotSubscriptionCollection;
use olml89\XenforoBots\Repository\BotRepository;
use olml89\XenforoBots\UseCase\Notification\NoActiveBotSubscriptionsException;
use XF\Entity\Post;

final class ContentFactory
{
    public function __construct(
        private readonly BotRepository $botRepository,
    ) {}

    /**
     * @throws NoActiveBotSubscriptionsException
     */
    public function create(Post $post, ?Bot $excludedBot = null): Content
    {
        $notifiableBots = $this->botRepository->getAll();

        if (!is_null($excludedBot)) {
            $notifiableBots = array_filter(
                $notifiableBots,
                fn (Bot $bot): bool => !$bot->same($excludedBot)
            );
        }

        $botSubscriptions = new BotSubscriptionCollection();

        foreach ($notifiableBots as $notifiableBot) {
            $botSubscriptions = $botSubscriptions->merge(
                $notifiableBot->BotSubscriptions->filter(
                    fn (BotSubscription $botSubscription): bool => $botSubscription->is_active
                )
            );
        }

        return new Content($post, $botSubscriptions);
    }
}