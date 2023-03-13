<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\Subscription;

use olml89\Subscriptions\Entities\Subscription;
use olml89\Subscriptions\Exceptions\ApplicationException;
use olml89\Subscriptions\Exceptions\ErrorHandler;
use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\Services\WebhookVerifier\WebhookVerifier;
use olml89\Subscriptions\Services\XFUserFinder\XFUserFinder;
use olml89\Subscriptions\ValueObjects\AutoId\AutoId;
use olml89\Subscriptions\ValueObjects\Md5Hash\Md5Hash;
use olml89\Subscriptions\ValueObjects\Url\Url;
use olml89\Subscriptions\ValueObjects\Uuid\Uuid;
use olml89\Subscriptions\ValueObjects\Uuid\UuidGenerator;
use olml89\Subscriptions\ValueObjects\Uuid\UuidValidator;
use olml89\Subscriptions\XF\Api\Result\UseCaseResponse;
use XF\Db\DuplicateKeyException as XFDuplicateKeyException;
use XF\Db\Exception as XFDatabaseException;
use XF\Validator\Url as XFUrlValidator;

final class CreateSubscription
{
    public function __construct(
        private readonly UuidGenerator $uuidGenerator,
        private readonly UuidValidator $uuidValidator,
        private readonly XFUrlValidator $xFUrlValidator,
        private readonly XFUserFinder $xFUserFinder,
        private readonly WebhookVerifier $webhookVerifier,
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly ErrorHandler $errorHandler,
    ) {}

    /**
     * @throws CreateSubscriptionException | ExistingSubscriptionException | SaveSubscriptionException
     */
    public function create(int $user_id, string $webhook, string $token): UseCaseResponse
    {
        try {
            $subscription = new Subscription(
                id: Uuid::random($this->uuidGenerator, $this->uuidValidator),
                userId: new AutoId($user_id),
                webhook: new Url($webhook, $this->xFUrlValidator),
                token: new Md5Hash($token),
            );

            $this->xFUserFinder->find($subscription->userId);
            $this->webhookVerifier->verify($subscription->webhook, $subscription->token);
            $this->subscriptionRepository->save($subscription);

            return new UseCaseResponse(new CreatedSubscriptionPresenter($subscription));
        }
        catch (ApplicationException $applicationException) {
            throw new CreateSubscriptionException($applicationException, $this->errorHandler);
        }
        catch (XFDuplicateKeyException) {
            throw new ExistingSubscriptionException($subscription);
        }
        catch (XFDatabaseException $xfDatabaseException) {
            throw new SaveSubscriptionException($xfDatabaseException, $this->errorHandler);
        }
    }
}
