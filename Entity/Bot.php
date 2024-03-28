<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Entity;

use olml89\XenforoSubscriptions\XF\Validator\Uuid;
use XF;
use XF\Api\Result\EntityResult;
use XF\Entity\ApiKey;
use XF\Entity\User;
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
 * @property-read User $User
 * @property-read ApiKey $ApiKey
 * @property-read Subscription[] $Subscriptions
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
        $structure->table = 'olml89_xenforo_subscriptions_bot';
        $structure->shortName = 'olml89\XenforoSubscriptions:Bot';
        $structure->contentType = 'olml89_xenforo_subscriptions_bot';
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

    protected function setupApiResultData(
        EntityResult $result,
        $verbosity = self::VERBOSITY_NORMAL,
        array $options = [],
    ): void {
        $result->User = $this->User;
        $result->ApiKey = $this->ApiKey;
    }
}
