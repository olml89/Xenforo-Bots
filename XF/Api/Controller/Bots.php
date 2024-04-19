<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Api\Controller;

use olml89\XenforoBots\Exception\ApiKeyNotAuthorizedException;
use olml89\XenforoBots\Exception\BotValidationException;
use olml89\XenforoBots\Exception\BotStorageException;
use olml89\XenforoBots\Service\Authorizer;
use olml89\XenforoBots\UseCase\Bot\Create;
use XF\Api\Controller\AbstractController;
use XF\Api\Mvc\Reply\ApiResult;
use XF\App;
use XF\Http\Request;

final class Bots extends AbstractController
{
    private readonly Authorizer $authorizer;
    private readonly Create $createBot;

    public function __construct(App $app, Request $request)
    {
        $this->authorizer = $app->get(Authorizer::class);
        $this->createBot = $app->get(Create::class);

        parent::__construct($app, $request);
    }

    /**
     * @throws ApiKeyNotAuthorizedException
     * @throws BotValidationException
     * @throws BotStorageException
     */
    public function actionPost(): ApiResult
    {
        $owner = $this->authorizer->getAuthorizedSuperUserKey();

        $this->assertRequiredApiInput([
            'username',
            'password',
        ]);

        $bot = $this->createBot->create(
            owner: $owner,
            username: $this->request->filter('username', 'str'),
            password: $this->request->filter('password', 'str'),
        );

        return $this->apiSuccess([
            'bot' => $bot,
        ]);
    }
}
