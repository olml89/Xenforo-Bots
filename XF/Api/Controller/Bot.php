<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Api\Controller;

use olml89\XenforoBots\Exception\ApiKeyNotAuthorizedException;
use olml89\XenforoBots\Exception\BotNotFoundException;
use olml89\XenforoBots\Exception\BotRemovalException;
use olml89\XenforoBots\Service\Authorizer;
use olml89\XenforoBots\UseCase\Bot\Delete;
use olml89\XenforoBots\UseCase\Bot\Retrieve;
use XF\Api\Controller\AbstractController;
use XF\Api\Mvc\Reply\ApiResult;
use XF\App;
use XF\Http\Request;
use XF\Mvc\ParameterBag;

final class Bot extends AbstractController
{
    private readonly Authorizer $authorizer;
    private readonly Retrieve $retrieveBot;
    private readonly Delete $deleteBot;

    public function __construct(App $app, Request $request)
    {
        $this->authorizer = $app->get(Authorizer::class);
        $this->retrieveBot = $app->get(Retrieve::class);
        $this->deleteBot = $app->get(Delete::class);

        parent::__construct($app, $request);
    }

    /**
     * @throws ApiKeyNotAuthorizedException
     * @throws BotNotFoundException
     */
    public function actionGet(ParameterBag $params): ApiResult
    {
        $owner = $this->authorizer->getAuthorizedSuperUserKey();

        $bot = $this->retrieveBot->retrieve(
            owner: $owner,
            bot_id: $params->get('bot_id')
        );

        return $this->apiSuccess([
            'bot' => $bot,
        ]);
    }

    /**
     * @throws ApiKeyNotAuthorizedException
     * @throws BotNotFoundException
     * @throws BotRemovalException
     */
    public function actionDelete(ParameterBag $params): ApiResult
    {
        $owner = $this->authorizer->getAuthorizedSuperUserKey();

        $this->deleteBot->delete(
            owner: $owner,
            bot_id: $params->get('bot_id')
        );

        return $this->apiSuccess();
    }
}
