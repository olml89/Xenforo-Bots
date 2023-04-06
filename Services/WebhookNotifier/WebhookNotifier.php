<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Services\WebhookNotifier;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use olml89\XenforoSubscriptions\Entities\Subscription;
use olml89\XenforoSubscriptions\UseCases\DataTransferObject;
use olml89\XenforoSubscriptions\ValueObjects\Url\Url;
use XF\Error;

final class WebhookNotifier
{
    public function __construct(
        private readonly Client $httpClient,
        private readonly Error $error,
    ) {}

    /**
     * @param Subscription[] $subscriptions
     */
    public function notify(string $endpoint, array $subscriptions, DataTransferObject $data): void
    {
        if (!$subscriptions) {
            return;
        }

        $webhooks = array_map(
            function (Subscription $subscription): Url {
                return $subscription->webhook;
            },
            $subscriptions,
        );

        $asyncRequests = function() use($webhooks, $endpoint, $data): Generator {
            foreach ($webhooks as $webhook) {
                yield function() use ($webhook, $endpoint, $data): PromiseInterface {
                    return $this->httpClient->postAsync(
                        $webhook.$endpoint,
                        ['json' => $data]
                    );
                };
            }
        };

        $pool = new Pool(
            client: $this->httpClient,
            requests: $asyncRequests(),
            config: [
                'rejected' => function (RequestException $reason, int $index) {
                    $this->error->logException($reason);
                },
            ]
        );

        $pool->promise()->wait();
    }
}
