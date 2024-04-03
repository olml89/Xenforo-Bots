<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Entity;

use olml89\XenforoBots\UseCase\UserAlert\Notify as NotifyUserAlert;
use XF;

/**
 * @extends \XF\Entity\UserAlert
 *
 * RELATIONS
 *
 * @property-read User $Receiver
 * @property-read User $User
 */
final class UserAlert extends XFCP_UserAlert
{
    private const notifiableContentType = 'post';
    private const notifiableActions = ['quote', 'mention'];

    private function isNotifiable(): bool
    {
        return $this->content_type === self::notifiableContentType
            && in_array($this->action, self::notifiableActions);
    }

    protected function _postSave(): void
    {
        parent::_postSave();

        if (!$this->isNotifiable()) {
            return;
        }

        /** @var NotifyUserAlert $notifyUserAlert */
        $notifyUserAlert = XF::app()->get(NotifyUserAlert::class);
        $notifyUserAlert->notify($this);
    }
}
