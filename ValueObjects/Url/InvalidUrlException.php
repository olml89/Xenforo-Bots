<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\Url;

use olml89\Subscriptions\Exceptions\InputException;

final class InvalidUrlException extends InputException
{
    public function __construct(string $url)
    {
        parent::__construct(
            message: sprintf('Must represent a valid URL, \'%s\' provided', $url),
            errorCode: 'invalid_url',
        );
    }
}
