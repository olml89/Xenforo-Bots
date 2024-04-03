<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\UserAlert;

use olml89\XenforoBots\UseCase\JsonSerializableObject;
use XF\Entity\UserAlert;

final class UserAlertData extends JsonSerializableObject
{
    public readonly int $content_id;
    public readonly string $content_type;
    public readonly int $user_id;

    public function __construct(UserAlert $userAlert)
    {
        $this->content_id = $userAlert->content_id;
        $this->content_type = $userAlert->content_type;
        $this->user_id = $userAlert->user_id;
    }
}
