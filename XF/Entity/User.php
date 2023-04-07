<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\XF\Entity;

use olml89\XenforoSubscriptions\Entity\Subscription;
use olml89\XenforoSubscriptions\Validator\UrlValidator;
use XF\Mvc\Entity\Structure;

/**
 * RELATIONS
 * @property Subscription[] Subscriptions
 */
final class User extends XFCP_User
{
    public static function getStructure(Structure $structure): Structure
    {
        $structure = parent::getStructure($structure);

        $structure->relations['Subscriptions'] = [
            'entity' => 'olml89\XenforoSubscriptions:Subscription',
            'type' => self::TO_MANY,
            'conditions' => 'user_id',
            'primary' => true,
        ];

        return $structure;
    }

    public function getSubscriptionByWebhook(string $webhook): ?Subscription
    {
        /** @var UrlValidator $validator */
        $validator = $this->app()->get(UrlValidator::class);
        $validator->ensureIsValid($webhook);

        foreach ($this->Subscriptions as $subscription) {
            if ($subscription->webhook === $webhook) {
                return $subscription;
            }
        }

        return null;
    }
}
