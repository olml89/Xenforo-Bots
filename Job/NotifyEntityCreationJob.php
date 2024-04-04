<?php declare(strict_types=1);

namespace olml89\XenforoBots\Job;

use XF\Job\AbstractJob;
use XF\Mvc\Entity\Entity;

abstract class NotifyEntityCreationJob extends AbstractJob
{
    /**
     * @return class-string<Entity>
     */
    abstract protected static function entityClass(): string;

    protected function getEntityId(): int|string
    {
        return $this->data['id'];
    }

    public function getStatusMessage(): string
    {
        return sprintf(
            'Notifying creation of %s with id %s',
            static::entityClass(),
            $this->getEntityId(),
        );
    }

    public function canCancel(): bool
    {
        return false;
    }

    public function canTriggerByChoice(): bool
    {
        return false;
    }
}
