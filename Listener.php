<?php declare(strict_types=1);

namespace olml89\Subscriptions;

use GuzzleHttp\Client;
use olml89\Subscriptions\Exceptions\ErrorHandler;
use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\Repositories\XFUserRepository;
use olml89\Subscriptions\Services\WebhookVerifier\WebhookVerifier;
use olml89\Subscriptions\Services\XFUserFinder\XFUserFinder;
use olml89\Subscriptions\UseCases\Subscription\CreateSubscription;
use XF\App;
use XF\Container;

final class Listener
{
    private static function createJsonHttpClient(App $app): Client
    {
        return $app->http()->createClient([
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'http_errors' => true,
        ]);
    }

    public static function appSetup(App $app): void
    {
        /** @var Container $container */
        $container = $app->container();

        $container[XFUserRepository::class] = function() use($app): XFUserRepository
        {
            return new XFUserRepository($app->em());
        };

        $container[XFUserFinder::class] = function() use($app): XFUserFinder
        {
            return new XFUserFinder($app->get(XFUserRepository::class));
        };

        $container[WebhookVerifier::class] = function() use($app): WebhookVerifier
        {
            return new WebhookVerifier(self::createJsonHttpClient($app));
        };

        $container[SubscriptionRepository::class] = function() use($app): SubscriptionRepository
        {
            return new SubscriptionRepository($app->em());
        };

        $container[ErrorHandler::class] = function() use($app, $container): ErrorHandler
        {
            return new ErrorHandler(
                error: $app->error(),
                debug: $container['config']['debug'],
            );
        };

        $container[CreateSubscription::class] = function() use($app): CreateSubscription
        {
            return new CreateSubscription(
                xFUserFinder: $app->get(XFUserFinder::class),
                xFUrlValidator: $app->validator('Url'),
                webhookVerifier: $app->get(WebhookVerifier::class),
                subscriptionRepository: $app->get(SubscriptionRepository::class),
                errorHandler: $app->get(ErrorHandler::class),
            );
        };
    }
}
