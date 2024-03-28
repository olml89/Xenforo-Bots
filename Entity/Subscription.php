<?php declare(strict_types=1);

namespace olml89\XenforoBots\Entity;

use olml89\XenforoBots\Exception\InvalidUrlException;
use olml89\XenforoBots\Exception\InvalidUuidException;
use olml89\XenforoBots\Validator\Url;
use olml89\XenforoBots\Validator\Uuid;
use XF;
use XF\Api\Result\EntityResult;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string $subscription_id
 * @property int $user_id
 * @property string $webhook
 * @property int $subscribed_at
 *
 * RELATIONS
 * @property \XF\Entity\User $User
 */
class Subscription extends Entity
{
    public static function getStructure(Structure $structure): Structure
    {
        $structure->table = 'olml89_xenforo_subscriptions_subscription';
        $structure->shortName = 'olml89\XenforoBots:Subscription';
        $structure->contentType = 'olml89_xenforo_subscriptions_subscription';
        $structure->primaryKey = 'subscription_id';
        $structure->columns = [
            'subscription_id' => [
                'type' => self::STR,
                'length' => 36,
                'api' => true
            ],
            'user_id' => [
                'type' => self::UINT,
                'required' => true,
                'api' => true,
            ],
            'webhook' => [
                'type' => self::STR,
                'maxLength' => 1048,
                'api' => true,
            ],
            'subscribed_at' => [
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
            ],
        ];
        $structure->defaultWith = ['User'];

        return $structure;
    }

    /**
     * @throws InvalidUuidException
     */
    protected function verifySubscriptionId(string &$value): bool
    {
        /** @var Uuid $validator */
        $validator = $this->app()->get(Uuid::class);
        $validator->ensureIsValid($value);

        return true;
    }

    /**
     * @throws InvalidUrlException
     */
    protected function verifyWebhook(string &$value): bool
    {
        /** @var Url $validator */
        $validator = $this->app()->get(Url::class);
        $validator->ensureIsValid($value);

        return true;
    }

    protected function setupApiResultData(
        EntityResult $result,
        $verbosity = self::VERBOSITY_NORMAL,
        array $options = [],
    ): void {}

    protected function _postDelete()
    {
        $this->User->clearCache('Subscriptions');
    }
}
