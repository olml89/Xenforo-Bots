<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions;

use GuzzleHttp\Client;
use Laminas\Validator\Uuid;
use olml89\XenforoSubscriptions\Entity\SubscriptionFactory;
use olml89\XenforoSubscriptions\Repository\SubscriptionRepository;
use olml89\XenforoSubscriptions\Repository\XFUserRepository;
use olml89\XenforoSubscriptions\Service\ErrorHandler;
use olml89\XenforoSubscriptions\Service\SubscriptionFinder;
use olml89\XenforoSubscriptions\Service\UuidGenerator;
use olml89\XenforoSubscriptions\Service\WebhookNotifier;
use olml89\XenforoSubscriptions\Service\XFUserFinder;
use olml89\XenforoSubscriptions\UseCase\Subscription\Create as CreateSubscription;
use olml89\XenforoSubscriptions\UseCase\Subscription\Delete as DeleteSubscription;
use olml89\XenforoSubscriptions\UseCase\Subscription\Retrieve as RetrieveSubscription;
use olml89\XenforoSubscriptions\UseCase\XFConversationMessage\Notify as NotifyXFConversationMessage;
use olml89\XenforoSubscriptions\UseCase\XFPost\Notify as NotifyXFPost;
use olml89\XenforoSubscriptions\UseCase\XFUserAlert\Notify as NotifyXFUserAlert;
use olml89\XenforoSubscriptions\Validator\UrlValidator;
use olml89\XenforoSubscriptions\Validator\UuidValidator;
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

        $container[SubscriptionFactory::class] = function() use($app): SubscriptionFactory
        {
            return new SubscriptionFactory(
                xFUserFinder: $app->get(XFUserFinder::class),
                entityManager: $app->em(),
                uuidGenerator: $app->get(UuidGenerator::class),
                errorHandler: $app->get(ErrorHandler::class),
            );
        };

        $container[SubscriptionRepository::class] = function() use($app): SubscriptionRepository
        {
            return new SubscriptionRepository(
                subscriptionFinder: $app->finder('olml89\XenforoSubscriptions:Subscription'),
                errorHandler: $app->get(ErrorHandler::class),
            );
        };

        $container[XFUserRepository::class] = function() use($app): XFUserRepository
        {
            return new XFUserRepository(userFinder: $app->finder('XF:User'));
        };

        $container[XFUserFinder::class] = function() use($app): XFUserFinder
        {
            return new XFUserFinder(userRepository: $app->get(XFUserRepository::class));
        };

        $container[ErrorHandler::class] = function() use($app, $container): ErrorHandler
        {
            return new ErrorHandler(
                error: $app->error(),
                debug: $container['config']['debug'],
            );
        };

        $container[SubscriptionFinder::class] = function() use($app): SubscriptionFinder
        {
            return new SubscriptionFinder(urlValidator: $app->get(UrlValidator::class));
        };

        $container[UuidGenerator::class] = function() use($app): UuidGenerator
        {
            return new UuidGenerator(stripeRandomGenerator: new RandomGenerator());
        };

        $container[WebhookNotifier::class] = function() use($app): WebhookNotifier
        {
            return new WebhookNotifier(
                httpClient: self::createJsonHttpClient($app),
                error: $app->error(),
            );
        };

        $container[CreateSubscription::class] = function() use($app): CreateSubscription
        {
            return new CreateSubscription(
                subscriptionFactory: $app->get(SubscriptionFactory::class),
                subscriptionRepository: $app->get(SubscriptionRepository::class),
            );
        };

        $container[DeleteSubscription::class] = function() use($app): DeleteSubscription
        {
            return new DeleteSubscription(
                xFUserFinder: $app->get(XFUserFinder::class),
                subscriptionFinder: $app->get(SubscriptionFinder::class),
                subscriptionRepository: $app->get(SubscriptionRepository::class),
            );
        };

        $container[RetrieveSubscription::class] = function() use($app): RetrieveSubscription
        {
            return new RetrieveSubscription(xFUserFinder: $app->get(XFUserFinder::class));
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

        $container[NotifyXFConversationMessage::class] = function() use($app): NotifyXFConversationMessage
        {
            return new NotifyXFConversationMessage(
                subscriptionRepository: $app->get(SubscriptionRepository::class),
                webhookNotifier: $app->get(WebhookNotifier::class),
            );
        };

        $container[UrlValidator::class] = function() use($app): UrlValidator
        {
            return new UrlValidator(xFUrlValidator: $app->validator('Url'));
        };

        $container[UuidValidator::class] = function() use($app): UuidValidator
        {
            return new UuidValidator(laminasUuidValidator: new Uuid());
        };
    }
}
