<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Api\Controller;

use olml89\XenforoBots\Exception\BotNotAuthorizedException;
use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Exception\BotSubscriptionNotFoundException;
use olml89\XenforoBots\Exception\BotSubscriptionRemovalException;
use olml89\XenforoBots\Exception\UserNotAuthorizedException;
use olml89\XenforoBots\Service\Authorizer;
use olml89\XenforoBots\UseCase\BotSubscription\Delete;
use olml89\XenforoBots\UseCase\BotSubscription\Retrieve;
use XF\Api\Controller\AbstractController;
use XF\Api\Mvc\Reply\ApiResult;
use XF\App;
use XF\Http\Request;
use XF\Mvc\ParameterBag;

final class BotSubscription extends AbstractController
{
    private Authorizer $authorizer;
    private Retrieve $retrieveBotSubscription;
    private Delete $deleteBotSubscription;

    public function __construct(App $app, Request $request)
    {
        $this->authorizer = $app->get(Authorizer::class);
        $this->retrieveBotSubscription = $app->get(Retrieve::class);
        $this->deleteBotSubscription = $app->get(Delete::class);

        parent::__construct($app, $request);
    }

    /**
     * @throws UserNotAuthorizedException
     * @throws BotNotFoundException
     * @throws BotNotAuthorizedException
     * @throws BotSubscriptionNotFoundException
     */
    public function actionGet(ParameterBag $params): ApiResult
    {
        $bot = $this->authorizer->getAuthorizedBot($params->get('bot_id'));

        $botSubscription = $this->retrieveBotSubscription->retrieve(
            bot: $bot,
            bot_subscription_id: $params->get('bot_subscription_id'),
        );

        return $this->apiSuccess([
            'botSubscription' => $botSubscription,
        ]);
    }

    /**
     * @throws UserNotAuthorizedException
     * @throws BotNotFoundException
     * @throws BotNotAuthorizedException
     * @throws BotSubscriptionNotFoundException
     * @throws BotSubscriptionRemovalException
     */
    public function actionDelete(ParameterBag $params): ApiResult
    {
        $bot = $this->authorizer->getAuthorizedBot($params->get('bot_id'));

        $this->deleteBotSubscription->delete(
            bot: $bot,
            bot_subscription_id: $params->get('bot_subscription_id'),
        );

        return $this->apiSuccess();
    }
}
