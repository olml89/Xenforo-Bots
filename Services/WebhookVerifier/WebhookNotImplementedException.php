<?php declare(strict_types=1);

namespace olml89\Subscriptions\Services\WebhookVerifier;

use Exception;
use olml89\Subscriptions\Exceptions\ApplicationException;
use olml89\Subscriptions\ValueObjects\Url\Url;

final class WebhookNotImplementedException extends ApplicationException
{
    public function __construct(Url $webhook, Exception $reason)
    {
        parent::__construct(
            message: sprintf(
                'The webhook \'%s\' does not implement an endpoint supporting the processing of subscriptions',
                $webhook
            ),
            errorCode: 'webhook_not_implemented',
            previous: $reason,
        );
    }
}
