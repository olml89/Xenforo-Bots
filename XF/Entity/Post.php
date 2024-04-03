<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Entity;

use olml89\XenforoBots\UseCase\Post\Notify as NotifyPost;
use XF;

final class Post extends XFCP_Post
{
    protected function _postSave(): void
    {
        parent::_postSave();

        /** @var NotifyPost $notifyPost */
        $notifyPost = XF::app()->get(NotifyPost::class);
        $notifyPost->notify($this);
    }
}
