<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\XF\Entity;

use olml89\XenforoSubscriptions\UseCase\XFPost\Notify as NotifyXFPost;
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
