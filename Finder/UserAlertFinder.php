<?php declare(strict_types=1);

namespace olml89\XenforoBots\Finder;

use olml89\XenforoBots\Exception\UserAlertNotFoundException;
use olml89\XenforoBots\XF\Entity\UserAlert;
use XF\Mvc\Entity\Finder;

final class UserAlertFinder
{
    public function __construct(
        private readonly Finder $userAlertFinder,
    ) {}

    /**
     * @throws UserAlertNotFoundException
     */
    public function find(int $user_alert_id): UserAlert
    {
        return $this->getUserAlert($user_alert_id) ?? throw UserAlertNotFoundException::id($user_alert_id);
    }

    private function getUserAlert(int $user_alert_id): ?UserAlert
    {
        /**
         * @var UserAlert
         */
        return $this->userAlertFinder->whereId($user_alert_id)->fetchOne();
    }
}
