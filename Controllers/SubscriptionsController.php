<?php declare(strict_types=1);

namespace olml89\Subscriptions\Controllers;

use olml89\Subscriptions\Entities\Subscription;
use olml89\Subscriptions\Repositories\SubscriptionRepository;
use XF\Api\Controller\AbstractController;
use XF\Api\Mvc\Reply\ApiResult;
use XF\Db\Exception as XenforoDatabaseException;
use XF\Mvc\Reply\Exception as XenforoApiException;

final class SubscriptionsController extends AbstractController
{
    /**
     * @throws XenforoApiException
     */
    public function actionPost() : ApiResult
    {
        $this->assertRequiredApiInput([
            'user_id',
            'token',
            'webhook'
        ]);

        $subscription = new Subscription(
            userId: $this->request->filter('user_id', 'uint'),
            webhook: $this->request->filter('webhook', 'str'),
            token: $this->request->filter('token', 'str'),
        );

        $subscriptions = new SubscriptionRepository($this->em());

        try {
            $subscriptions->save($subscription);
        }
        catch (XenforoDatabaseException $e) {
            throw $this->exception(
                $this->apiError(
                    'The subscription has failed',
                    'subscription_not_saved',
                    ['exception' => $e],
                    500,
                )
            );
        }

        return $this->apiSuccess();
    }
}
