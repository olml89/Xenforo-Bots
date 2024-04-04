<?php declare(strict_types=1);

namespace olml89\XenforoBots\Job;

use olml89\XenforoBots\Exception\UserAlertNotFoundException;
use olml89\XenforoBots\Finder\UserAlertFinder;
use olml89\XenforoBots\UseCase\UserAlert\Notify;
use XF\App;

final class NotifyUserAlertCreationJob extends NotifyEntityCreationJob
{
    private readonly UserAlertFinder $userAlertFinder;
    private readonly Notify $notifyUserAlert;

    public function __construct(App $app, int|string $jobId, array $data = [])
    {
        $this->userAlertFinder = $app->get(UserAlertFinder::class);
        $this->notifyUserAlert = $app->get(Notify::class);

        parent::__construct($app, $jobId, $data);
    }

    /**
     * @param float|int $maxRunTime
     *
     * @throws UserAlertNotFoundException
     */
    public function run($maxRunTime): void
    {
        $userAlert = $this->userAlertFinder->find($this->getEntityId());

        $this->notifyUserAlert->notify($userAlert);
    }

    protected static function entityClass(): string
    {
        return UserAlertFinder::class;
    }
}
