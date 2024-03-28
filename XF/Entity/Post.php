<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Entity;

use olml89\XenforoBots\UseCase\XFPost\Notify as NotifyXFPost;
use XF;

final class Post extends XFCP_Post
{
    protected function _postSave(): void
    {
        parent::_postSave();

        /** @var NotifyXFPost $notifyXFPost */
        $notifyXFPost = XF::app()->get(NotifyXFPost::class);
        $notifyXFPost->notify($this);
    }
}
