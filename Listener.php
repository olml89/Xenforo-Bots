<?php declare(strict_types=1);

namespace olml89\Subscriptions;

use GuzzleHttp\Client;
use Laminas\Validator\Uuid;
use olml89\Subscriptions\Exceptions\ErrorHandler;
use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\Repositories\XFUserRepository;
use olml89\Subscriptions\Services\LaminasUuidValidator;
use olml89\Subscriptions\Services\StripeRandomUuidGenerator;
use olml89\Subscriptions\Services\WebhookNotifier\WebhookNotifier;
use olml89\Subscriptions\Services\WebhookVerifier\WebhookVerifier;
use olml89\Subscriptions\Services\XFUserFinder\XFUserFinder;
use olml89\Subscriptions\UseCases\Subscription\CreateSubscription;
use olml89\Subscriptions\UseCases\XFPost\NotifyXFPost;
use olml89\Subscriptions\UseCases\XFUserAlert\NotifyXFUserAlert;
use olml89\Subscriptions\ValueObjects\Uuid\UuidGenerator;
use olml89\Subscriptions\ValueObjects\Uuid\UuidValidator;
use Stripe\Util\RandomGenerator;
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
            return new XFUserRepository(entityManager: $app->em());
        };

        $container[XFUserFinder::class] = function() use($app): XFUserFinder
        {
            return new XFUserFinder(xFUserRepository: $app->get(XFUserRepository::class));
        };

        $container[WebhookVerifier::class] = function() use($app): WebhookVerifier
        {
            return new WebhookVerifier(httpClient: self::createJsonHttpClient($app));
        };

        $container[SubscriptionRepository::class] = function() use($app): SubscriptionRepository
        {
            return new SubscriptionRepository(
                entityManager: $app->em(),
                error: $app->error(),
            );
        };

        $container[ErrorHandler::class] = function() use($app, $container): ErrorHandler
        {
            return new ErrorHandler(
                error: $app->error(),
                debug: $container['config']['debug'],
            );
        };

        $container[UuidGenerator::class] = function() use($app): UuidGenerator
        {
            return new StripeRandomUuidGenerator(generator: new RandomGenerator());
        };

        $container[UuidValidator::class] = function() use($app): UuidValidator
        {
            return new LaminasUuidValidator(validator: new Uuid());
        };

        $container[CreateSubscription::class] = function() use($app): CreateSubscription
        {
            return new CreateSubscription(
                uuidGenerator: $app->get(UuidGenerator::class),
                uuidValidator: $app->get(UuidValidator::class),
                xFUrlValidator: $app->validator('Url'),
                xFUserFinder: $app->get(XFUserFinder::class),
                webhookVerifier: $app->get(WebhookVerifier::class),
                subscriptionRepository: $app->get(SubscriptionRepository::class),
                errorHandler: $app->get(ErrorHandler::class),
            );
        };

        $container[WebhookNotifier::class] = function() use($app): WebhookNotifier
        {
            return new WebhookNotifier(
                httpClient: self::createJsonHttpClient($app),
                error: $app->error(),
            );
        };

        $container[NotifyXFPost::class] = function() use($app): NotifyXFPost
        {
            return new NotifyXFPost(
                subscriptionRepository: $app->get(SubscriptionRepository::class),
                webhookNotifier: $app->get(WebhookNotifier::class),
            );
        };

        $container[NotifyXFUserAlert::class] = function() use($app): NotifyXFUserAlert
        {
            return new NotifyXFUserAlert(
                subscriptionRepository: $app->get(SubscriptionRepository::class),
                webhookNotifier: $app->get(WebhookNotifier::class),
            );
        };
    }
}
