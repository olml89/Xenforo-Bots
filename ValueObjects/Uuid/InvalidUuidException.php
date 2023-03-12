<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\Uuid;

use olml89\Subscriptions\Exceptions\ApplicationException;

final class InvalidUuidException extends ApplicationException
{
    public function __construct(string $uuid)
    {
        parent::__construct(
            message: sprintf('Must represent a valid UUID, \'%s\' provided', $uuid),
            errorCode: 'invalid_uuid',
        );
    }
}
