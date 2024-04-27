<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification\PublicInteraction;

use olml89\XenforoBots\UseCase\Notification\NoActiveBotSubscriptionsException;
use olml89\XenforoBots\XF\Entity\UserAlert;
use XF\Entity\Post;

final class PublicInteractionFactory
{
    private const REQUIRED_TYPE = 'post';

    private const ALLOWED_ACTIONS = [
        'quote',
        'mention',
    ];

    private function isNotifiable(UserAlert $userAlert): bool
    {
        return $userAlert->content_type === self::REQUIRED_TYPE
            && in_array($userAlert->action, self::ALLOWED_ACTIONS)
            && (!is_null($userAlert->Receiver->Bot));
    }

    /**
     * @throws UnnotifiableUserAlertException
     * @throws NoActiveBotSubscriptionsException
     */
    public function create(UserAlert $userAlert): PublicInteraction
    {
        if (!$this->isNotifiable($userAlert)) {
            throw new UnnotifiableUserAlertException($userAlert);
        }

         /** @var Post $post */
        $post = $userAlert->Content;

        return new PublicInteraction($post, $userAlert->Receiver->Bot);
    }
}