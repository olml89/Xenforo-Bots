<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Validator;

use XF\Validator\Url as UrlValidator;

final class Url extends UrlValidator
{
    /**
     * @param string $errorKey
     */
    public function getPrintableErrorValue($errorKey): string
    {
        return match ($errorKey) {
            'invalid' => 'Invalid URL',
            'disallowed_scheme' => 'Disallowed scheme',
            'no_authority' => 'No authority',
        };
    }
}
