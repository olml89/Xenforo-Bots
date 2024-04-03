<?php declare(strict_types=1);

namespace olml89\XenforoBots\Service;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use olml89\XenforoBots\Entity\ActiveBotSubscriptionCollection;
use olml89\XenforoBots\UseCase\JsonSerializableObject;
use XF\Error;

final class WebhookNotifier
{
    public function __construct(
        private readonly Client $httpClient,
        private readonly Error $error,
    ) {}

    public function notify(string $endpoint, JsonSerializableObject $data, ActiveBotSubscriptionCollection $botSubscriptions): void
    {
        if ($botSubscriptions->isEmpty()) {
            return;
        }

        $asyncRequests = function() use($botSubscriptions, $endpoint, $data): Generator {
            foreach ($botSubscriptions as $botSubscription) {
                yield function() use ($botSubscription, $endpoint, $data): PromiseInterface {
                    $url = sprintf(
                        '%s/bots/%s%s',
                        $botSubscription->webhook,
                        $botSubscription->Bot->bot_id,
                        $endpoint,
                    );

                    return $this->httpClient->postAsync($url, [
                        'json' => $data,
                    ]);
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
