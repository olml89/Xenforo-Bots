<?php declare(strict_types=1);

namespace olml89\XenforoBots\Service\Notifier;
use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

final class CachedRequests
{
    private const MAX_ATTEMPTS = 5;

    /**
     * The key is the string representation of a UriInterface
     *
     * The value is an int representing the number of times the request has been attempted without success
     * (so, a 0 value means the request has been attempted successfully)
     *
     * @var array<string, int>
     */
    private array $cachedRequests = [];

    public function __construct(array $cachedRequests)
    {
        foreach ($cachedRequests as $uriString => $numAttempts) {
            $this->cachedRequests[(string)(new Uri($uriString))] = $numAttempts;
        }
    }

    public function maxAttempts(): int
    {
        return self::MAX_ATTEMPTS;
    }

    private function exists(UriInterface $uri): bool
    {
        return array_key_exists((string)$uri, $this->cachedRequests);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertExists(UriInterface $uri): void
    {
        if (!$this->exists($uri)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Result for request to %s was not expected',
                    $uri,
                )
            );
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function attempts(UriInterface $uri): int
    {
        $this->assertExists($uri);
        return $this->cachedRequests[(string)$uri];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function hasAttemptsLeft(UriInterface $uri): bool
    {
        return !$this->exists($uri)
            || ($this->attempts($uri) >= 0 && $this->attempts($uri) < $this->maxAttempts());
    }

    private function initializeRequest(UriInterface $uri): void
    {
        $this->cachedRequests[(string)$uri] = 0;
    }

    public function prepare(UriInterface $uri): void
    {
        if ($this->exists($uri)) {
            return;
        }

        $this->initializeRequest($uri);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function success(UriInterface $uri): void
    {
        $this->assertExists($uri);
        $this->initializeRequest($uri);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function fail(UriInterface $uri): void
    {
        $this->assertExists($uri);
        ++$this->cachedRequests[(string)$uri];

        if ($this->cachedRequests[(string)$uri] > $this->maxAttempts()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Maximum number of %s attempts for request to %s was reached',
                    $this->maxAttempts(),
                    $uri,
                )
            );
        }
    }

    public function toArray(): array
    {
        return $this->cachedRequests;
    }

    public function failed(): array
    {
        $failedRequests = array_filter(
            $this->cachedRequests,
            fn (int $numberOfAttempts): bool => $numberOfAttempts === $this->maxAttempts(),
        );

        return array_keys($failedRequests);
    }

    public function completed(): bool
    {
        $requestsWithAttemptsLeft = array_filter(
            $this->cachedRequests,
            fn (int $numberOfAttempts): bool => $numberOfAttempts > 0 && $numberOfAttempts < $this->maxAttempts()
        );

        return count($requestsWithAttemptsLeft) === 0;
    }

    public function hasFailedRequests(): bool
    {
        return $this->completed() && count($this->failed()) > 0;
    }
}