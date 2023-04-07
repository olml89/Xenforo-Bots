<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Service;

use Throwable;
use XF\Error;

final class ErrorHandler
{
    public function __construct(
        private readonly Error $error,
        private readonly bool $debug,
    ) {}

    public function handle(?Throwable $e): ?Throwable
    {
        if (is_null($e)) {
            return null;
        }

        $this->logException($e);

        return $this->getContext($e);
    }

    private function logException(Throwable $e): void
    {
        $this->error->logException($e);
    }

    private function getContext(Throwable $e): ?Throwable
    {
        return $this->debug ? $e : null;
    }
}
