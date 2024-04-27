<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification;

use olml89\XenforoBots\Entity\Bot;

interface SingularInteraction
{
    public function bot(): Bot;
}