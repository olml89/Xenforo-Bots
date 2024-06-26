<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Entity;

use olml89\XenforoBots\Entity\Bot;
use XF\Api\Result\EntityResult;
use XF\Mvc\Entity\Structure;

/**
 * @extends \XF\Entity\User
 *
 * RELATIONS
 *
 * @property-read ?Bot $Bot
 */
final class User extends XFCP_User
{
    public static function getStructure(Structure $structure): Structure
    {
        $structure = parent::getStructure($structure);

        $structure->relations['Bot'] = [
            'entity' => 'olml89\XenforoBots:Bot',
            'type' => self::TO_ONE,
            'conditions' => 'user_id',
            'cascadeDelete' => true,
        ];
        $structure->defaultWith[] = 'Bot';

        return $structure;
    }

    public function same(User $user): bool
    {
        return $this->user_id === $user->user_id;
    }
}
