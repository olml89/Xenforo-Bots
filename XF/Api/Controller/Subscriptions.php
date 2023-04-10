<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\XF\Api\Controller;

use olml89\XenforoSubscriptions\UseCase\Subscription\Create;
use olml89\XenforoSubscriptions\UseCase\Subscription\Delete;
use olml89\XenforoSubscriptions\UseCase\Subscription\Retrieve;
use XF\Api\Controller\AbstractController;
use XF\Api\Mvc\Reply\ApiResult;
use XF\App;
use XF\Http\Request;
use XF\Mvc\Reply\Exception;

final class Subscriptions extends AbstractController
{
    private Retrieve $retrieveSubscription;
    private Create $createSubscription;
    private Delete $deleteSubscription;

    public function __construct(App $app, Request $request)
    {
        $this->retrieveSubscription = $app->get(Retrieve::class);
        $this->createSubscription = $app->get(Create::class);
        $this->deleteSubscription = $app->get(Delete::class);

        parent::__construct($app, $request);
    }

    /**
     * @throws Exception
     */
    public function actionGet(): ApiResult
    {
        $this->assertRequiredApiInput([
            'user_id',
            'webhook',
        ]);

        $subscription = $this->retrieveSubscription->retrieve(
            user_id: $this->request->filter('user_id', 'uint'),
            webhook: $this->request->filter('webhook', 'str'),
        );

        return $this->apiSuccess(['subscription' => $subscription]);
    }

    /**
     * @throws Exception
     */
    public function actionPost(): ApiResult
    {
        $this->assertRequiredApiInput([
            'user_id',
            'password',
            'webhook',
        ]);

        $subscription = $this->createSubscription->create(
            user_id: $this->request->filter('user_id', 'uint'),
            password: $this->request->filter('password', 'str'),
            webhook: $this->request->filter('webhook', 'str'),
        );

        return $this->apiSuccess(['subscription' => $subscription->toApiResult()]);
    }

    /**
     * @throws Exception
     */
    public function actionDelete(): ApiResult
    {
        $this->assertRequiredApiInput([
            'user_id',
            'webhook',
        ]);

        $this->deleteSubscription->delete(
            user_id: $this->request->filter('user_id', 'uint'),
            webhook: $this->request->filter('webhook', 'str'),
        );

        return $this->apiSuccess();
    }
}
