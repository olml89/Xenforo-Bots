<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

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
