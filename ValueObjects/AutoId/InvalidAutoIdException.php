<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\ValueObjects\AutoId;

use olml89\XenforoSubscriptions\Exceptions\ApplicationException;

final class InvalidAutoIdException extends ApplicationException
{
    public function __construct(int $user_id)
    {
        parent::__construct(
            message: sprintf('AutoId must be bigger than 0, <%s> provided', $user_id),
            errorCode: 'invalid_auto_id',
        );
    }
}
