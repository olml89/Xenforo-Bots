<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\ValueObjects\Url;

use olml89\XenforoSubscriptions\Exceptions\ApplicationException;

final class InvalidUrlException extends ApplicationException
{
    public function __construct(string $url, string $errorKey)
    {
        parent::__construct(
            message: sprintf('Must represent a valid URL, \'%s\' provided', $url),
            errorCode: 'invalid_url.'.$errorKey,
        );
    }
}
