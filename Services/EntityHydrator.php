<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Services;

use DateTimeImmutable;
use InvalidArgumentException;
use olml89\XenforoSubscriptions\ValueObjects\IntValueObject;
use olml89\XenforoSubscriptions\ValueObjects\StringValueObject;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use ValueError;

final class EntityHydrator
{
    private const DATETIME_DATABASE_FORMAT = 'Y-m-d H:i:s';

    public function __construct(
        private readonly object $entity,
    ) {}

    /**
     * @throws ReflectionException
     */
    private function getProperty(object $object, string $propertyName): ReflectionProperty
    {
        $reflectionClass = new ReflectionClass($object);

        return $reflectionClass->hasProperty($propertyName)
            ? $reflectionClass->getProperty($propertyName)
            : $reflectionClass->getParentClass()->getProperty($propertyName);
    }

    /**
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    private function hydrateObject(object $object, string $propertyName, mixed $value): object
    {
        $property = $this->getProperty($object, $propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);

        return $object;
    }

    private function ensureIsAValueObjectClass(string $className): void
    {
        if (
            !is_subclass_of($className, IntValueObject::class)
            && !is_subclass_of($className, StringValueObject::class)
        ) {
            throw new InvalidArgumentException(
                sprintf('Class <%s> does not implement a ValueObject', $className)
            );
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function hydrateValueObject(string $className, mixed $value): IntValueObject|StringValueObject
    {
        $this->ensureIsAValueObjectClass($className);

        return $this->hydrateObject(
            self::getInstance($className),
            'value',
            $value,
        );
    }

    /**
     * @throws ReflectionException
     */
    public function hydrateProperty(string $property, mixed $value): self
    {
        $this->hydrateObject(
            $this->entity,
            $property,
            $value,
        );

        return $this;
    }

    /**
     * @throws ReflectionException
     */
    public static function getInstance(string $className): object
    {
        return (new ReflectionClass($className))->newInstanceWithoutConstructor();
    }

    /**
     * @throws ValueError
     */
    public function hydrateDateTimeImmutable(string $datetime): DateTimeImmutable
    {
        $dateTime = DateTimeImmutable::createFromFormat(
            self::DATETIME_DATABASE_FORMAT,
            $datetime,
        );

        if (!$dateTime) {
            throw new ValueError(
                sprintf(
                    '<%> can not be converted to a <%s> format DateTimeImmutable',
                    $dateTime,
                    self::DATETIME_DATABASE_FORMAT,
                )
            );
        }

        return $dateTime;
    }
}
