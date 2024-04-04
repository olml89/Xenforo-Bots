<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Entity;

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
}
