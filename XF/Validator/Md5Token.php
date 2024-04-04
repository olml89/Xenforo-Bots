<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Validator;


use XF\Validator\AbstractValidator;

final class Md5Token extends AbstractValidator
{
    /**
     * @param mixed $value
     * @param string $errorKey
     */
    public function isValid($value, &$errorKey = null): bool
    {
        if (!is_string($value) || mb_strlen($value) !== 32) {
            $errorKey = 'invalid_md5_token';
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
            'invalid_md5_token' => 'Value must be a valid md5 token',
        };
    }
}
