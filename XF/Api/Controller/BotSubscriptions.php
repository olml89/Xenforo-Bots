<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Api\Controller;

use olml89\XenforoBots\Exception\BotNotAuthorizedException;
use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Exception\BotSubscriptionAlreadyExistsException;
use olml89\XenforoBots\Exception\BotSubscriptionValidationException;
use olml89\XenforoBots\Exception\BotSubscriptionStorageException;
use olml89\XenforoBots\Exception\UserNotAuthorizedException;
use olml89\XenforoBots\Service\Authorizer;
use olml89\XenforoBots\UseCase\BotSubscription\Create;
use XF\Api\Controller\AbstractController;
use XF\Api\Mvc\Reply\ApiResult;
use XF\App;
use XF\Http\Request;
use XF\Mvc\ParameterBag;

final class BotSubscriptions extends AbstractController
{
    private Authorizer $authorizer;
    private Create $createBotSubscription;

    public function __construct(App $app, Request $request)
    {
        $this->authorizer = $app->get(Authorizer::class);
        $this->createBotSubscription = $app->get(Create::class);

        parent::__construct($app, $request);
    }

    /**
     * @throws UserNotAuthorizedException
     * @throws BotNotFoundException
     * @throws BotNotAuthorizedException
     * @throws BotSubscriptionValidationException
     * @throws BotSubscriptionAlreadyExistsException
     * @throws BotSubscriptionStorageException
     */
    public function actionPost(ParameterBag $params): ApiResult
    {
        $bot = $this->authorizer->getAuthorizedBot($params->get('bot_id'));

        $this->assertRequiredApiInput([
            'platform_api_key',
            'webhook',
        ]);

        $botSubscription = $this->createBotSubscription->create(
            bot: $bot,
            platform_api_key: $this->request->filter('platform_api_key', 'str'),
            webhook: $this->request->filter('webhook', 'str'),
        );

        return $this->apiSuccess([
            'botSubscription' => $botSubscription,
        ]);
    }
}
