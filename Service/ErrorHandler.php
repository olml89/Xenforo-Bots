<?php declare(strict_types=1);

namespace olml89\XenforoBots\Service;

use Throwable;
use XF\Error;

final class ErrorHandler
{
    public function __construct(
        private readonly Error $error,
        private readonly bool $debug,
    ) {}

    public function handle(Throwable $e): ?Throwable
    {
        if (!$this->debug) {
            $this->logException($e);

            return null;
        }

        return $e;
    }

    private function logException(Throwable $e): void
    {
        $this->error->logException($e);
    }
}
