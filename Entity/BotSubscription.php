<?php declare(strict_types=1);

namespace olml89\XenforoBots\Entity;

use olml89\XenforoBots\Exception\BotSubscriptionAlreadyExistsException;
use olml89\XenforoBots\XF\Validator\Url;
use olml89\XenforoBots\XF\Validator\Uuid;
use XF;
use XF\Api\Result\EntityResult;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 *
 * @property string $bot_subscription_id
 * @property string $bot_id
 * @property bool $is_active
 * @property string $webhook
 * @property int $subscribed_at
 *
 * RELATIONS
 *
 * @property Bot $Bot
 */
class BotSubscription extends Entity
{
    public static function getStructure(Structure $structure): Structure
    {
        $structure->table = 'olml89_xenforo_bots_bot_subscription';
        $structure->shortName = 'olml89\XenforoBots:BotSubscription';
        $structure->contentType = 'olml89_xenforo_bots_bot_subscription';
        $structure->primaryKey = 'bot_subscription_id';
        $structure->columns = [
            'bot_subscription_id' => [
                'type' => self::STR,
                'length' => 36,
                'api' => true
            ],
            'bot_id' => [
                'type' => self::STR,
                'length' => 36,
            ],
            'is_active' => [
                'type' => self::BOOL,
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
            'Bot' => [
                'entity' => 'olml89\XenforoBots:Bot',
                'type' => self::TO_ONE,
                'conditions' => 'bot_id',
                'primary' => true,
            ],
        ];
        $structure->defaultWith = ['Bot'];

        return $structure;
    }

    protected function verifyBotSubscriptionId(string &$bot_subscription_id): bool
    {
        /** @var Uuid $validator */
        $validator = $this->app()->validator('Uuid');

        if (!$validator->isValid($bot_subscription_id, $errorKey)) {
            $this->error($validator->getPrintableErrorValue($errorKey), 'bot_subscription_id');

            return false;
        }

        return true;
    }

    protected function verifyWebhook(string &$webhook): bool
    {
        /** @var Url $validator */
        $validator = $this->app()->validator('Url');

        if (!$validator->isValid($webhook, $errorKey)) {
            $this->error($validator->getPrintableErrorValue($errorKey), 'webhook');

            return false;
        }

        return true;
    }

    public function activate(): void
    {
        $this->is_active = true;
    }

    public function deactivate(): void
    {
        $this->is_active = false;
    }

    /**
     * @throws BotSubscriptionAlreadyExistsException
     */
    public function setSubscriber(Bot $bot): void
    {
        foreach ($bot->BotSubscriptions as $alreadyExistingSubscription) {
            if ($this->webhook === $alreadyExistingSubscription->webhook) {
                throw new BotSubscriptionAlreadyExistsException($bot, $this);
            }
        }

        $this->bot_id = $bot->bot_id;
        $this->hydrateRelation('Bot', $bot);
    }

    protected function setupApiResultData(
        EntityResult $result,
        $verbosity = self::VERBOSITY_NORMAL,
        array $options = [],
    ): void {}
}
