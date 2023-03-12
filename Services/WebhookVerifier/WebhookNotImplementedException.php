<?php declare(strict_types=1);

namespace olml89\Subscriptions\Services\WebhookVerifier;

use olml89\Subscriptions\Exceptions\InputException;
use olml89\Subscriptions\ValueObjects\Url\Url;

final class WebhookNotImplementedException extends InputException
{
    public function __construct(Url $webhook)
    {
        parent::__construct(
            message: sprintf(
                'The webhook \'%s\' does not implement an endpoint supporting the processing of subscriptions',
                $webhook
            ),
            errorCode: 'subscription.create.webhook_not_implemented',
        );
    }
}
