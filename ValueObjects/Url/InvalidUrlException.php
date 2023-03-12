<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\Url;

use olml89\Subscriptions\Exceptions\ApiException;

final class InvalidUrlException extends ApiException
{
    public function __construct(string $url)
    {
        parent::__construct(
            message: sprintf('Must represent a valid URL, \'%s\' provided', $url),
            httpCode: 400,
        );
    }
}
