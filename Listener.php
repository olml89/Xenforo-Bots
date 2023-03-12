<?php declare(strict_types=1);

namespace olml89\Subscriptions;

use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\Repositories\XFUserRepository;
use olml89\Subscriptions\Services\XFUserFinder\XFUserFinder;
use olml89\Subscriptions\UseCases\Subscription\CreateSubscription;
use XF\App;
use XF\Container;

final class Listener
{
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

        $container[SubscriptionRepository::class] = function() use($app): SubscriptionRepository
        {
            return new SubscriptionRepository($app->em());
        };

        $container[CreateSubscription::class] = function() use($app): CreateSubscription
        {
            return new CreateSubscription(
                xFUserFinder: $app->get(XFUserFinder::class),
                subscriptionRepository: $app->get(SubscriptionRepository::class),
            );
        };
    }
}
