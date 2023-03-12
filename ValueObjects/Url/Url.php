<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\Url;

use olml89\Subscriptions\ValueObjects\StringValueObject;

final class Url extends StringValueObject
{
    /**
     * @throws InvalidUrlException
     */
    public function __construct(string $url)
    {
        $this->ensureIsAValidUrl($url);

        parent::__construct($url);
    }

    /**
     * @throws InvalidUrlException
     */
    private function ensureIsAValidUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL) === true) {
            throw new InvalidUrlException($url);
        }
    }
}
