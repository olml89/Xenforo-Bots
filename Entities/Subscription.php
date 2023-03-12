<?php declare(strict_types=1);

namespace olml89\Subscriptions\Entities;

use olml89\Subscriptions\ValueObjects\Md5Hash\Md5Hash;
use olml89\Subscriptions\ValueObjects\Url\Url;
use olml89\Subscriptions\ValueObjects\UserId\UserId;

final class Subscription
{
    public function __construct(
        public readonly UserId $userId,
        public readonly Url $webhook,
        public readonly Md5Hash $token,
    ) {}
}
