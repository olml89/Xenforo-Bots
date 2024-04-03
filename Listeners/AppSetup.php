<?php declare(strict_types=1);

namespace olml89\XenforoBots\Listeners;

use GuzzleHttp\Client;
use Laminas\Validator\Uuid as LaminasUuid;
use olml89\XenforoBots\Factory\ApiKeyFactory;
use olml89\XenforoBots\Factory\BotFactory;
use olml89\XenforoBots\Factory\BotSubscriptionFactory;
use olml89\XenforoBots\Factory\UserFactory;
use olml89\XenforoBots\Repository\ApiKeyRepository;
use olml89\XenforoBots\Repository\BotRepository;
use olml89\XenforoBots\Repository\BotSubscriptionRepository;
use olml89\XenforoBots\Repository\UserRepository;
use olml89\XenforoBots\Service\Authorizer;
use olml89\XenforoBots\Service\BotSubscriptionFinder;
use olml89\XenforoBots\Service\ErrorHandler;
use olml89\XenforoBots\Service\UuidGenerator;
use olml89\XenforoBots\Service\WebhookNotifier;
use olml89\XenforoBots\Service\BotFinder;
use olml89\XenforoBots\UseCase\Bot\Create as CreateBot;
use olml89\XenforoBots\UseCase\Bot\Delete as DeleteBot;
use olml89\XenforoBots\UseCase\Bot\Retrieve as RetrieveBot;
use olml89\XenforoBots\UseCase\BotSubscription\Activate as ActivateBotSubscription;
use olml89\XenforoBots\UseCase\BotSubscription\Create as CreateBotSubscription;
use olml89\XenforoBots\UseCase\BotSubscription\Deactivate as DeactivateBotSubscription;
use olml89\XenforoBots\UseCase\BotSubscription\Delete as DeleteBotSubscription;
use olml89\XenforoBots\UseCase\BotSubscription\Retrieve as RetrieveBotSubscription;
use olml89\XenforoBots\UseCase\ConversationMessage\Notify as NotifyConversationMessage;
use olml89\XenforoBots\UseCase\Post\Notify as NotifyPost;
use olml89\XenforoBots\UseCase\UserAlert\Notify as NotifyUserAlert;
use olml89\XenforoBots\XF\Validator\Uuid;
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

        $container[BotSubscriptionFactory::class] = function() use($app): BotSubscriptionFactory
        {
            return new BotSubscriptionFactory(
                entityManager: $app->em(),
                uuidGenerator: $app->get(UuidGenerator::class),
            );
        };

        $container[UserFactory::class] = function() use ($app): UserFactory
        {
            /** @var \XF\Repository\User $userRepository */
            $userRepository = $app->repository('XF:User');

            return new UserFactory(
                userRepository: $userRepository,
            );
        };

        /**
         * Repositories
         */
        $container[ApiKeyRepository::class] = function() use ($app): ApiKeyRepository
        {
            return new ApiKeyRepository(
                errorHandler: $app->get(ErrorHandler::class),
            );
        };

        $container[BotRepository::class] = function() use ($app): BotRepository
        {
            return new BotRepository(
                errorHandler: $app->get(ErrorHandler::class),
                botFinder: $app->finder('olml89\XenforoBots:Bot'),
            );
        };

        $container[BotSubscriptionRepository::class] = function() use($app): BotSubscriptionRepository
        {
            return new BotSubscriptionRepository(
                errorHandler: $app->get(ErrorHandler::class),
                botSubscriptionFinder: $app->finder('olml89\XenforoBots:BotSubscription'),
            );
        };

        $container[UserRepository::class] = function() use ($app): UserRepository
        {
            return new UserRepository(
                errorHandler: $app->get(ErrorHandler::class),
            );
        };

        /**
         * Services
         */
        $container[Authorizer::class] = function() use ($app): Authorizer
        {
            return new Authorizer(
                botFinder: $app->get(BotFinder::class),
            );
        };

        $container[BotFinder::class] = function() use($app): BotFinder
        {
            return new BotFinder(
                botRepository: $app->get(BotRepository::class),
            );
        };

        $container[BotSubscriptionFinder::class] = function() use($app): BotSubscriptionFinder
        {
            return new BotSubscriptionFinder(
                botSubscriptionRepository: $app->get(BotSubscriptionRepository::class),
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
                botFinder: $app->get(BotFinder::class),
            );
        };

        $container[DeleteBot::class] = function() use ($app): DeleteBot
        {
            return new DeleteBot(
                botFinder: $app->get(BotFinder::class),
                botRepository: $app->get(BotRepository::class),
            );
        };

        $container[CreateBotSubscription::class] = function() use($app): CreateBotSubscription
        {
            return new CreateBotSubscription(
                botSubscriptionFactory: $app->get(BotSubscriptionFactory::class),
                botSubscriptionRepository: $app->get(BotSubscriptionRepository::class),
            );
        };

        $container[RetrieveBotSubscription::class] = function() use($app): RetrieveBotSubscription
        {
            return new RetrieveBotSubscription(
                botSubscriptionFinder: $app->get(BotSubscriptionFinder::class),
            );
        };

        $container[DeleteBotSubscription::class] = function() use($app): DeleteBotSubscription
        {
            return new DeleteBotSubscription(
                botSubscriptionFinder: $app->get(BotSubscriptionFinder::class),
                botSubscriptionRepository: $app->get(BotSubscriptionRepository::class),
            );
        };

        $container[ActivateBotSubscription::class] = function() use ($app): ActivateBotSubscription
        {
            return new ActivateBotSubscription(
                botSubscriptionFinder: $app->get(BotSubscriptionFinder::class),
                botSubscriptionRepository: $app->get(BotSubscriptionRepository::class),
            );
        };

        $container[DeactivateBotSubscription::class] = function() use ($app): DeactivateBotSubscription
        {
            return new DeactivateBotSubscription(
                botSubscriptionFinder: $app->get(BotSubscriptionFinder::class),
                botSubscriptionRepository: $app->get(BotSubscriptionRepository::class),
            );
        };

        $container[NotifyConversationMessage::class] = function() use($app): NotifyConversationMessage
        {
            return new NotifyConversationMessage(
                webhookNotifier: $app->get(WebhookNotifier::class),
            );
        };

        $container[NotifyPost::class] = function() use($app): NotifyPost
        {
            return new NotifyPost(
                botRepository: $app->get(BotRepository::class),
                webhookNotifier: $app->get(WebhookNotifier::class),
            );
        };

        $container[NotifyUserAlert::class] = function() use($app): NotifyUserAlert
        {
            return new NotifyUserAlert(
                webhookNotifier: $app->get(WebhookNotifier::class),
            );
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
