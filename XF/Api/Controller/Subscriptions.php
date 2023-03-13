<?php declare(strict_types=1);

namespace olml89\Subscriptions\XF\Api\Controller;

use olml89\Subscriptions\UseCases\Subscription\CreateSubscription;
use XF\Api\Controller\AbstractController;
use XF\Api\Mvc\Reply\ApiResult;
use XF\App;
use XF\Http\Request;
use XF\Mvc\Reply\Exception;

final class Subscriptions extends AbstractController
{
    private CreateSubscription $createSubscription;

    public function __construct(App $app, Request $request)
    {
        $this->createSubscription = $app->get(CreateSubscription::class);

        parent::__construct($app, $request);
    }

    /**
     * @throws Exception
     */
    public function actionPost(): ApiResult
    {
        $this->assertRequiredApiInput([
            'user_id',
            'token',
            'webhook'
        ]);

        $createSubscriptionResult = $this->createSubscription->create(
            user_id: $this->request->filter('user_id', 'uint'),
            webhook: $this->request->filter('webhook', 'str'),
            token: $this->request->filter('token', 'str'),
        );

        return $this->apiResult($createSubscriptionResult);
    }
}
