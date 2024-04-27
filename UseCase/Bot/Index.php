<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Bot;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Repository\BotRepository;
use olml89\XenforoBots\XF\Entity\ApiKey;

final class Index
{
    public function __construct(
       private readonly BotRepository $botRepository,
    ) {}

    /**
     * @return Bot[]
     */
    public function index(ApiKey $owner): array
    {
        return $this->botRepository->getOwnedBy($owner);
    }
}