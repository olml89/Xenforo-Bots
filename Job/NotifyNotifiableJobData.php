<?php declare(strict_types=1);

namespace olml89\XenforoBots\Job;

final class NotifyNotifiableJobData
{
    public function __construct(
        public readonly string $uniqueId,
        /**
         * @var class-string<NotifyNotifiableJob>
         */
        public readonly string $jobClass,
        public readonly array $params,
    ) {}
}