<?php declare(strict_types=1);

namespace olml89\Subscriptions\XF\Entity;

use olml89\Subscriptions\UseCases\XFUserAlert\NotifyXFUserAlert;
use XF;

final class UserAlert extends XFCP_UserAlert
{
    private const notifiableContentType = 'post';
    private const notifiableActions = ['quote', 'mention'];

    private function isNotifiable(): bool
    {
        return $this->content_type === self::notifiableContentType
            && in_array($this->action, self::notifiableActions);
    }

    public function _postSave(): void
    {
        parent::_postSave();

        if (!$this->isNotifiable()) {
            return;
        }

        /** @var NotifyXFUserAlert $notifyUserAlert */
        $notifyUserAlert = XF::app()->get(NotifyXFUserAlert::class);
        $notifyUserAlert->notify($this);
    }
}
