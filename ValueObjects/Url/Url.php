<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\ValueObjects\Url;

use olml89\XenforoSubscriptions\ValueObjects\StringValueObject;
use XF\Validator\Url as XFUrlValidator;

final class Url extends StringValueObject
{
    /**
     * @throws InvalidUrlException
     */
    public function __construct(string $url, XFUrlValidator $xfUrlValidator)
    {
        $this->ensureIsAValidUrl($url, $xfUrlValidator);

        parent::__construct($url);
    }

    /**
     * @throws InvalidUrlException
     */
    private function ensureIsAValidUrl(string $url, XFUrlValidator $xfUrlValidator): void
    {
        $xfUrlValidator->setOption('allow_empty', false);
        $errorKey = '';

        if (!$xfUrlValidator->isValid($url, $errorKey)) {
            throw new InvalidUrlException($url, $errorKey);
        }
    }
}
