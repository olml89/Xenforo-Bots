<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCases\Subscription;

use olml89\XenforoSubscriptions\Entities\Subscription;
use olml89\XenforoSubscriptions\Exceptions\ApplicationException;
use olml89\XenforoSubscriptions\Exceptions\ErrorHandler;
use olml89\XenforoSubscriptions\Repositories\SubscriptionRepository;
use olml89\XenforoSubscriptions\Services\XFUserFinder\XFUserFinder;
use olml89\XenforoSubscriptions\ValueObjects\AutoId\AutoId;
use olml89\XenforoSubscriptions\ValueObjects\Url\Url;
use olml89\XenforoSubscriptions\ValueObjects\Uuid\UuidGenerator;
use olml89\XenforoSubscriptions\XF\Api\Result\UseCaseResponse;
use XF\Db\DuplicateKeyException as XFDuplicateKeyException;
use XF\Db\Exception as XFDatabaseException;
use XF\Validator\Url as XFUrlValidator;

final class CreateSubscription
{
    public function __construct(
        private readonly UuidGenerator $uuidGenerator,
        private readonly XFUrlValidator $xFUrlValidator,
        private readonly XFUserFinder $xFUserFinder,
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly ErrorHandler $errorHandler,
    ) {}

    /**
     * @throws CreateSubscriptionException | ExistingSubscriptionException | SaveSubscriptionException
     */
    public function create(int $user_id, string $password, string $webhook): UseCaseResponse
    {
        try {
            $subscription = new Subscription(
                id: $this->uuidGenerator->random(),
                userId: new AutoId($user_id),
                webhook: new Url($webhook, $this->xFUrlValidator),
            );

            $this->xFUserFinder->find($subscription->userId, $password);
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
