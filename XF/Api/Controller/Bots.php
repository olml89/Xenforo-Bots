<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\XF\Api\Controller;

use olml89\XenforoSubscriptions\Exception\BotCreationException;
use olml89\XenforoSubscriptions\Exception\BotNotFoundException;
use olml89\XenforoSubscriptions\Exception\BotRemovalException;
use olml89\XenforoSubscriptions\Exception\BotStorageException;
use olml89\XenforoSubscriptions\Service\Authenticator;
use olml89\XenforoSubscriptions\UseCase\Bot\Create;
use olml89\XenforoSubscriptions\UseCase\Bot\Delete;
use olml89\XenforoSubscriptions\UseCase\Bot\Retrieve;
use olml89\XenforoSubscriptions\XF\Mvc\Reply\ForbiddenException;
use XF\Api\Controller\AbstractController;
use XF\Api\Mvc\Reply\ApiResult;
use XF\App;
use XF\Http\Request;
use XF\Mvc\ParameterBag;

final class Bots extends AbstractController
{
    private readonly Authenticator $authenticator;
    private readonly Create $createBot;
    private readonly Retrieve $retrieveBot;
    private readonly Delete $deleteBot;

    public function __construct(App $app, Request $request)
    {
        $this->authenticator = $app->get(Authenticator::class);
        $this->createBot = $app->get(Create::class);
        $this->retrieveBot = $app->get(Retrieve::class);
        $this->deleteBot = $app->get(Delete::class);

        parent::__construct($app, $request);
    }

    /**
     * @throws ForbiddenException
     * @throws BotNotFoundException
     */
    public function actionGet(ParameterBag $params): ApiResult
    {
        $this->authenticator->assertSuperUserKey();

        $bot = $this->retrieveBot->retrieve($params->get('bot_id'));

        return $this->apiSuccess([
            'bot' => $bot,
        ]);
    }

    /**
     * @throws ForbiddenException
     * @throws BotCreationException
     * @throws BotStorageException
     */
    public function actionPost(): ApiResult
    {
        $this->authenticator->assertSuperUserKey();

        $this->assertRequiredApiInput([
            'username',
            'password',
        ]);

        $bot = $this->createBot->create(
            username: $this->request->filter('username', 'str'),
            password: $this->request->filter('password', 'str'),
        );

        return $this->apiSuccess([
            'bot' => $bot,
        ]);
    }

    /**
     * @throws ForbiddenException
     * @throws BotNotFoundException
     * @throws BotRemovalException
     */
    public function actionDelete(ParameterBag $params): ApiResult
    {
        $this->authenticator->assertSuperUserKey();
        $this->deleteBot->delete($params->get('bot_id'));

        return $this->apiSuccess();
    }
}
