<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Listeners;

use GuzzleHttp\Client;
use Laminas\Validator\Uuid as LaminasUuid;
use olml89\XenforoSubscriptions\Factory\ApiKeyFactory;
use olml89\XenforoSubscriptions\Factory\BotFactory;
use olml89\XenforoSubscriptions\Factory\SubscriptionFactory;
use olml89\XenforoSubscriptions\Factory\UserFactory;
use olml89\XenforoSubscriptions\Repository\ApiKeyRepository;
use olml89\XenforoSubscriptions\Repository\BotRepository;
use olml89\XenforoSubscriptions\Repository\SubscriptionRepository;
use olml89\XenforoSubscriptions\Repository\UserRepository;
use olml89\XenforoSubscriptions\Repository\XFUserRepository;
use olml89\XenforoSubscriptions\Service\Authenticator;
use olml89\XenforoSubscriptions\Service\ErrorHandler;
use olml89\XenforoSubscriptions\Service\SubscriptionFinder;
use olml89\XenforoSubscriptions\Service\UuidGenerator;
use olml89\XenforoSubscriptions\Service\WebhookNotifier;
use olml89\XenforoSubscriptions\Service\XFUserFinder;
use olml89\XenforoSubscriptions\UseCase\Bot\Create as CreateBot;
use olml89\XenforoSubscriptions\UseCase\Bot\Delete as DeleteBot;
use olml89\XenforoSubscriptions\UseCase\Bot\Retrieve as RetrieveBot;
use olml89\XenforoSubscriptions\UseCase\Subscription\Create as CreateSubscription;
use olml89\XenforoSubscriptions\UseCase\Subscription\Delete as DeleteSubscription;
use olml89\XenforoSubscriptions\UseCase\Subscription\Retrieve as RetrieveSubscription;
use olml89\XenforoSubscriptions\UseCase\XFConversationMessage\Notify as NotifyXFConversationMessage;
use olml89\XenforoSubscriptions\UseCase\XFPost\Notify as NotifyXFPost;
use olml89\XenforoSubscriptions\UseCase\XFUserAlert\Notify as NotifyXFUserAlert;
use olml89\XenforoSubscriptions\XF\Validator\Uuid;
use Stripe\Util\RandomGenerator;
use XF\App;
use XF\Container;
use XF\Validator\AbstractValidator;

final class AppSetup
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

    public static function listen(App $app): void
    {
        /** @var Container $container */
        $container = $app->container();

        /**
         * Factories
         */
        $container[UserFactory::class] = function() use ($app): UserFactory
        {
            /** @var \XF\Repository\User $userRepository */
            $userRepository = $app->repository('XF:User');

            return new UserFactory(
                userRepository: $userRepository,
            );
        };

        $container[ApiKeyFactory::class] = function() use ($app): ApiKeyFactory
        {
            return new ApiKeyFactory(
                entityManager: $app->em(),
                app: $app,
            );
        };

        $container[BotFactory::class] = function() use ($app): BotFactory
        {
            return new BotFactory(
                entityManager: $app->em(),
                uuidGenerator: $app->get(UuidGenerator::class),
            );
        };

        $container[SubscriptionFactory::class] = function() use($app): SubscriptionFactory
        {
            return new SubscriptionFactory(
                xFUserFinder: $app->get(XFUserFinder::class),
                entityManager: $app->em(),
                uuidGenerator: $app->get(UuidGenerator::class),
                errorHandler: $app->get(ErrorHandler::class),
            );
        };

        /**
         * Repositories
         */
        $container[UserRepository::class] = function() use ($app): UserRepository
        {
            return new UserRepository();
        };

        $container[ApiKeyRepository::class] = function() use ($app): ApiKeyRepository
        {
            return new ApiKeyRepository();
        };

        $container[BotRepository::class] = function() use ($app): BotRepository
        {
            return new BotRepository(
                botFinder: $app->finder('olml89\XenforoSubscriptions:Bot'),
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

        /**
         * Services
         */
        $container[Authenticator::class] = function() use ($app): Authenticator
        {
            return new Authenticator();
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
            return new SubscriptionFinder(urlValidator: $app->get(Url::class));
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

        /**
         * UseCases
         */
        $container[CreateBot::class] = function() use ($app): CreateBot
        {
            return new CreateBot(
                database: $app->db(),
                userFactory: $app->get(UserFactory::class),
                userRepository: $app->get(UserRepository::class),
                apiKeyFactory: $app->get(ApiKeyFactory::class),
                apiKeyRepository: $app->get(ApiKeyRepository::class),
                botFactory: $app->get(BotFactory::class),
                botRepository: $app->get(BotRepository::class),
            );
        };

        $container[RetrieveBot::class] = function() use ($app): RetrieveBot
        {
            return new RetrieveBot(
                botRepository: $app->get(BotRepository::class),
            );
        };

        $container[DeleteBot::class] = function() use ($app): DeleteBot
        {
            return new DeleteBot(
                botRepository: $app->get(BotRepository::class),
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

        $container[NotifyXFConversationMessage::class] = function() use($app): NotifyXFConversationMessage
        {
            return new NotifyXFConversationMessage(
                subscriptionRepository: $app->get(SubscriptionRepository::class),
                webhookNotifier: $app->get(WebhookNotifier::class),
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

        /**
         * Custom validators
         */
        $container->extendFactory(
            type: 'validator',
            callable: function($class, array $params, Container $container, callable $original) use ($app): AbstractValidator
            {
                if ($class === 'Uuid') {
                    return new Uuid(
                        laminasUuid: new LaminasUuid(),
                        app: $app,
                    );
                }

                return $original($class, $params, $container, $original);
            }
        );
    }
}
