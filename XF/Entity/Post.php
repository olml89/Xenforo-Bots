<?php declare(strict_types=1);

namespace olml89\Subscriptions\XF\Entity;

use olml89\Subscriptions\UseCases\XFPost\NotifyXFPost;
use XF;

final class Post extends XFCP_Post
{
    public function _postSave(): void
    {
        parent::_postSave();

        /** @var NotifyXFPost $notifyXFPost */
        $notifyXFPost = XF::app()->get(NotifyXFPost::class);
        $notifyXFPost->notify($this);
    }
}
