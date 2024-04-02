<?php declare(strict_types=1);

namespace olml89\XenforoBots\Factory;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\BotValidationException;
use olml89\XenforoBots\Service\UuidGenerator;
use XF\Entity\User;
use XF\Mvc\Entity\Manager;

final class BotFactory
{
    public function __construct(
        private readonly Manager $entityManager,
        private readonly UuidGenerator $uuidGenerator,
    ) {}

    /**
     * @throws BotValidationException
     */
    public function create(User $user): Bot
    {
        $bot = $this->instantiateBot($user);

        if ($bot->hasErrors()) {
            throw BotValidationException::entity($bot);
        }

        return $bot;
    }

    private function instantiateBot(User $user): Bot
    {
        /** @var Bot $bot */
        $bot = $this->entityManager->create(
            shortName: 'olml89\XenforoBots:Bot'
        );

        $bot->bot_id = $this->uuidGenerator->random();
        $bot->attachToUser($user);

        return $bot;
    }
}
