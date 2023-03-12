<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\XFUserAlert;

use olml89\Subscriptions\UseCases\DataTransferObject;
use XF\Entity\UserAlert as XFUserAlert;

final class XFUserAlertData extends DataTransferObject
{
    public readonly int $content_id;
    public readonly string $content_type;
    public readonly int $user_id;

    public function __construct(XFUserAlert $xFUserAlert)
    {
        $this->content_id = $xFUserAlert->content_id;
        $this->content_type = $xFUserAlert->content_type;
        $this->user_id = $xFUserAlert->user_id;
    }
}
