<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Validator;

use olml89\XenforoSubscriptions\Exception\InvalidUrlException;
use XF\Validator\Url;

final class UrlValidator
{
    private readonly Url $xFUrlValidator;
    private ?string $errorKey = null;

    public function __construct(Url $xFUrlValidator)
    {
        $this->xFUrlValidator = $xFUrlValidator;
        $this->xFUrlValidator->setOption('allow_empty', false);
    }

    public function isValid(string $url): bool
    {
        return $this->xFUrlValidator->isValid($url);
    }

    /**
     * @throws InvalidUrlException
     */
    public function ensureIsValid(string $url): void
    {
        if (!$this->xFUrlValidator->isValid($url, $this->errorKey)) {
            throw new InvalidUrlException($url, $this->errorKey);
        }
    }
}
