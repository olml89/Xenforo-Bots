<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification\PublicInteraction;

use Exception;
use olml89\XenforoBots\XF\Entity\UserAlert;

final class UnnotifiableUserAlertException extends Exception
{
    public function __construct(UserAlert $userAlert)
    {
        parent::__construct(
            sprintf(
                '%s %s is not notifiable because its content is not a Post or it is not a quote or a mention or 
                        its Receiver is not a Bot',
                $userAlert::class,
                $userAlert->getEntityId(),
            )
        );
    }
}