<?php declare(strict_types=1);

namespace olml89\XenforoBots\Service\Notifier;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\UseCase\Notification\Notifiable;
use Psr\Http\Message\UriInterface;

final class WebhookNotifier
{
    public function __construct(
        private readonly Client $httpClient,
    ) {}

    public function notify(Notifiable $notifiable, CachedRequests $cachedRequests): CachedRequests
    {
        /** @var Request[] $asyncRequests */
        $asyncRequests = [];

        foreach ($notifiable->botSubscriptions() as $botSubscription) {
            $request = $this->generateRequest($botSubscription, $notifiable);
            $uri = $request->getUri();

            if (!$cachedRequests->hasAttemptsLeft($uri)) {
                continue;
            }

            $cachedRequests->prepare($uri);
            $asyncRequests[] = $request;
        }

        $asyncRequestsProcessor = function (array $asyncRequests): Generator {
            foreach ($asyncRequests as $asyncRequest) {
                yield function () use ($asyncRequest): PromiseInterface {
                    return $this->httpClient->sendAsync($asyncRequest);
                };
            }
        };

        $pool = new Pool(
            client: $this->httpClient,
            requests: $asyncRequestsProcessor($asyncRequests),
            config: [
                'fulfilled' => function (Response $response, int $index) use ($asyncRequests, $cachedRequests): void {
                    $request = $asyncRequests[$index];
                    $cachedRequests->success($request->getUri());
                },
                'rejected' => function (RequestException $reason) use ($cachedRequests): void {
                    $uri = (string)$reason->getRequest()->getUri();
                    $body = (string)$reason->getResponse()->getBody();
                    $cachedRequests->fail($reason->getRequest()->getUri());
                },
            ]
        );

        $pool->promise()->wait();

        return $cachedRequests;
    }

    private function generateRequest(BotSubscription $botSubscription, Notifiable $notifiable): Request
    {
        $uri = $this->calculateUri($botSubscription, $notifiable);

        return new Request(
            method: 'POST',
            uri: (string)$uri,
            headers: [
                'Platform-Api-Key' => $botSubscription->platform_api_key,
            ],
            body: json_encode($notifiable->data())
        );
    }

    private function calculateUri(BotSubscription $botSubscription, Notifiable $notifiable): UriInterface
    {
        $uri = sprintf(
            '%s/%s',
            $botSubscription->webhook,
            $notifiable->endpoint($botSubscription->Bot),
        );

        return new Uri($uri);
    }
}
