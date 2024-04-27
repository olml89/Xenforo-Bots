<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscriptionCollection;
use olml89\XenforoBots\Job\NotifyNotifiableJobData;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use XF\Entity\Post;

interface Notifiable
{
    public function jobCreationData(): NotifyNotifiableJobData;
    public function entity(): ConversationMessage|Post;
    public function endpoint(Bot $bot): string;
    public function botSubscriptions(): BotSubscriptionCollection;
    public function data(): Data;
}