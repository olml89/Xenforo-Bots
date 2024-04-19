<?php declare(strict_types=1);

namespace olml89\XenforoBots\Entity;

use olml89\XenforoBots\Exception\BotNotAuthorizedException;
use olml89\XenforoBots\Exception\BotSubscriptionAlreadyExistsException;
use olml89\XenforoBots\Exception\BotSubscriptionNotFoundException;
use olml89\XenforoBots\XF\Entity\ApiKey;
use olml89\XenforoBots\XF\Entity\User;
use olml89\XenforoBots\XF\Validator\Uuid;
use XF;
use XF\Api\Result\EntityResult;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 *
 * @property int $owner_id
 * @property string $bot_id
 * @property int $user_id
 * @property int $api_key_id
 * @property int $created_at
 *
 * RELATIONS
 *
 * @property-read ApiKey $Owner
 * @property-read User $User
 * @property-read ApiKey $ApiKey
 * @property-read BotSubscriptionCollection $BotSubscriptions
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
            'owner_id' => [
                'type' => self::UINT,
                'required' => true,
            ],
            'bot_id' => [
                'type' => self::STR,
                'length' => 36,
                'required' => true,
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
            ],
            'created_at' => [
                'type' => self::UINT,
                'default' => XF::$time,
                'required' => true,
                'api' => true,
            ]
        ];
        $structure->relations = [
            'Owner' => [
                'entity' => 'XF:ApiKey',
                'type' => self::TO_ONE,
                'conditions' => 'owner_id',
                'primary' => true,
            ],
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
                'api' => true,
            ],
            'BotSubscriptions' => [
                'entity' => 'olml89\XenforoBots:BotSubscription',
                'type' => self::TO_MANY,
                'conditions' => 'bot_id',
                'cascadeDelete' => true,
                'api' => true,
            ],
        ];
        $structure->getters = [
            'BotSubscriptions' => true,
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

    protected function getBotSubscriptions(): BotSubscriptionCollection
    {
        return new BotSubscriptionCollection(...$this->getRelation('BotSubscriptions'));
    }

    public function same(Bot $bot): bool
    {
        return $this->bot_id === $bot->bot_id;
    }

    public function attachToOwner(ApiKey $owner): void
    {
        $this->owner_id = $owner->api_key_id;
        $this->hydrateRelation('Owner', $owner);
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
        if (!$this->same($botSubscription->Bot)) {
            throw BotNotAuthorizedException::doesNotOwn($this, $botSubscription);
        }
    }

    /**
     * @throws BotSubscriptionAlreadyExistsException
     */
    public function subscribe(BotSubscription $botSubscription): void
    {
        // Bot can only subscribe to a BotSubscription if the BotSubscription has not a subscriber,
        // or if the subscriber is this same Bot.
        if (!is_null($botSubscription->Bot) && !$botSubscription->Bot->same($this)) {
            throw BotSubscriptionAlreadyExistsException::alreadySubscribed($botSubscription);
        }

        // We can set a BotSubscription with the same webhook if it is the same BotSubscription, to update it
        $checkingEqualsFunction = function(BotSubscription $existingBotSubscription) use ($botSubscription): bool {
            return !$botSubscription->same($existingBotSubscription)
                && $botSubscription->equals($existingBotSubscription);
        };

        if ($this->BotSubscriptions->contains($checkingEqualsFunction)) {
            throw BotSubscriptionAlreadyExistsException::sameWebhook($botSubscription);
        }

        $this->BotSubscriptions->set($botSubscription);
        $botSubscription->setSubscriber($this);
    }

    /**
     * @throws BotSubscriptionNotFoundException
     */
    public function unsubscribe(BotSubscription $botSubscription): void
    {
        $this->BotSubscriptions->remove($botSubscription);
    }

    protected function setupApiResultData(
        EntityResult $result,
        $verbosity = self::VERBOSITY_NORMAL,
        array $options = [],
    ): void {}
}
