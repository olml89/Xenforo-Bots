<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\XF\Validator;

use Laminas\Validator\Uuid as LaminasUuid;
use XF\App;
use XF\Validator\AbstractValidator;

final class Uuid extends AbstractValidator
{
    public function __construct(
        private readonly LaminasUuid $laminasUuid,
        App $app,
    ) {
        parent::__construct($app);
    }

    /**
     * @param mixed $value
     * @param string $errorKey
     */
    public function isValid($value, &$errorKey = null): bool
    {
        if (!$this->laminasUuid->isValid($value)) {
            $errorKey = 'invalid_uuid';
            return false;
        }

        return true;
    }

    /**
     * @param string $errorKey
     */
    public function getPrintableErrorValue($errorKey): string
    {
        return match ($errorKey) {
            'invalid_uuid' => 'Invalid UUID',
        };
    }
}
