<?php declare(strict_types=1);

namespace olml89\Subscriptions\Controllers;

use olml89\Subscriptions\Entities\Subscription;
use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\ValueObjects\Md5Hash\Md5Hash;
use olml89\Subscriptions\ValueObjects\Url\Url;
use olml89\Subscriptions\ValueObjects\UserId\UserId;
use XF\Api\Controller\AbstractController;
use XF\Api\Mvc\Reply\ApiResult;
use XF\Db\Exception as XFDatabaseException;
use XF\Mvc\Reply\Exception as XFApiException;

final class SubscriptionsController extends AbstractController
{
    /**
     * @throws XFApiException
     */
    public function actionPost() : ApiResult
    {
        $this->assertRequiredApiInput([
            'user_id',
            'token',
            'webhook'
        ]);

        $subscription = new Subscription(
            userId: new UserId($this->request->filter('user_id', 'uint')),
            webhook: new Url($this->request->filter('webhook', 'str')),
            token: new Md5Hash($this->request->filter('token', 'str')),
        );

        $subscriptions = new SubscriptionRepository($this->em());

        try {
            $subscriptions->save($subscription);
        }
        catch (XFDatabaseException $e) {
            throw new SaveSubscriptionException(\XF::$debugMode ? $e : null);
        }

        return $this->apiSuccess();
    }
}
