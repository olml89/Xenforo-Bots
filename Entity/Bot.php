<?php declare(strict_types=1);

namespace olml89\XenforoBots\Entity;

use olml89\XenforoBots\Exception\BotNotAuthorizedException;
use olml89\XenforoBots\Exception\BotSubscriptionAlreadyExistsException;
use olml89\XenforoBots\XF\Entity\ApiKey;
use olml89\XenforoBots\XF\Entity\User;
use olml89\XenforoBots\XF\Validator\Uuid;
use XF;
use XF\Api\Result\EntityResult;
use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 *
 * @property string $bot_id
 * @property int $user_id
 * @property int $api_key_id
 * @property int $created_at
 *
 * RELATIONS
 *
 * @property-read User $User
 * @property-read ApiKey $ApiKey
 * @property-read ArrayCollection|BotSubscription[] $BotSubscriptions
 */
final class Bot extends Entity
{
    /**
     * @var string[]
     */
    public const SCOPES = [
        'alert:read',
        'alert:write',
        'conversation:read',
        'conversation:write',
        'thread:read',
        'thread:write',
    ];

    public static function getStructure(Structure $structure): Structure
    {
        $structure->table = 'olml89_xenforo_bots_bot';
        $structure->shortName = 'olml89\XenforoBots:Bot';
        $structure->contentType = 'olml89_xenforo_bots_bot';
        $structure->primaryKey = 'bot_id';
        $structure->columns = [
            'bot_id' => [
                'type' => self::STR,
                'length' => 36,
                'api' => true
            ],
            'user_id' => [
                'type' => self::UINT,
                'required' => true,
                'api' => true,
            ],
            'api_key_id' => [
                'type' => self::UINT,
                'required' => true,
                'api' => true,
            ],
            'created_at' => [
                'type' => self::UINT,
                'required' => true,
                'default' => XF::$time,
                'api' => true,
            ]
        ];
        $structure->relations = [
            'User' => [
                'entity' => 'XF:User',
                'type' => self::TO_ONE,
                'conditions' => 'user_id',
                'primary' => true,
                'cascadeDelete' => true,
            ],
            'ApiKey' => [
                'entity' => 'XF:ApiKey',
                'type' => self::TO_ONE,
                'conditions' => 'api_key_id',
                'primary' => true,
                'cascadeDelete' => true,
            ],
            'BotSubscriptions' => [
                'entity' => 'olml89\XenforoBots:BotSubscription',
                'type' => self::TO_MANY,
                'conditions' => 'bot_id',
                'cascadeDelete' => true,
            ],
        ];
        $structure->defaultWith = [
            'User',
            'ApiKey',
        ];

        return $structure;
    }

    protected function verifyBotId(string &$bot_id): bool
    {
        /** @var Uuid $validator */
        $validator = $this->app()->validator('Uuid');

        if (!$validator->isValid($bot_id, $errorKey)) {
            $this->error($validator->getPrintableErrorValue($errorKey), 'bot_id');

            return false;
        }

        return true;
    }

    public function attachToUser(User $user): void
    {
        $this->user_id = $user->user_id;
        $this->hydrateRelation('User', $user);
    }

    public function setApiKey(ApiKey $apiKey): void
    {
        $this->api_key_id = $apiKey->api_key_id;
        $this->hydrateRelation('ApiKey', $apiKey);
    }

    /**
     * @throws BotNotAuthorizedException
     */
    public function owns(BotSubscription $botSubscription): void
    {
        foreach ($this->BotSubscriptions as $ownedBotSubscription) {
            if ($botSubscription->bot_subscription_id === $ownedBotSubscription->bot_subscription_id) {
                return;
            }
        }

        throw BotNotAuthorizedException::notAllowed($this);
    }

    /**
     * @throws BotSubscriptionAlreadyExistsException
     */
    public function subscribe(BotSubscription $botSubscription): void
    {
        $botSubscription->setSubscriber($this);

        $this->hydrateRelation(
            'BotSubscriptions',
            $this->BotSubscriptions->merge(new ArrayCollection([$botSubscription]))
        );
    }

    public function unsubscribe(BotSubscription $botSubscription): void
    {
        $this->hydrateRelation(
            'BotSubscriptions',
            $this->BotSubscriptions->filter(
                function (BotSubscription $alreadyExistingBotSubscription) use ($botSubscription): bool {
                    return $alreadyExistingBotSubscription->bot_subscription_id !== $botSubscription->bot_subscription_id;
                }
            )
        );
    }

    protected function setupApiResultData(
        EntityResult $result,
        $verbosity = self::VERBOSITY_NORMAL,
        array $options = [],
    ): void {
        $result->skipColumn('user_id');
        $result->skipColumn('api_key_id');

        $result->includeRelation('BotSubscriptions');
    }
}
