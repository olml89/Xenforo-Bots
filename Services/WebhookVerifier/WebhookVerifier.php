<?php declare(strict_types=1);

namespace olml89\Subscriptions\Services\WebhookVerifier;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use olml89\Subscriptions\ValueObjects\Md5Hash\Md5Hash;
use olml89\Subscriptions\ValueObjects\Url\Url;

final class WebhookVerifier
{
    private const VERIFICATION_ENDPOINT = '/challenges';

    public function __construct(
        private readonly Client $httpClient,
    ) {}

    /**
     * @throws WebhookNotImplementedException
     */
    public function verify(Url $webhook, Md5Hash $challenge): void
    {
        try {
            $this->httpClient->head($webhook.self::VERIFICATION_ENDPOINT.'/'.$challenge);
        }
        catch (RequestException $reason) {
            throw new WebhookNotImplementedException($webhook, $reason);
        }
    }
}
