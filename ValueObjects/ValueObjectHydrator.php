<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

final class ValueObjectHydrator
{
    public function __construct(
        private readonly array $rows,
    ) {}

    /**
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    private function hydrateObject(string $className, array $arguments): object
    {
        $instance = (new ReflectionClass($className))->newInstanceWithoutConstructor();
        $reflectionClass = new ReflectionClass($instance);

        foreach ($arguments as $argument => $index) {

            if (!array_key_exists($index, $this->rows)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Row <%s> does not exist in the database',
                        $index,
                    )
                );
            }

            $property = $reflectionClass->getProperty($argument);

            // This means the property we are trying to hydrate is in fact from a parent class.
            // Trying to hydrate it from the child class results in an out-of-scope exception.
            if ($reflectionClass->name !== $property->class) {
                $parentReflectionClass = new ReflectionClass($property->class);
                $property = $parentReflectionClass->getProperty($property->name);
            }

            $property->setAccessible(true);
            $property->setValue($instance, $this->rows[$index]);
        }

        return $instance;
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
    public function hydrateValueObject(string $className, string $index): IntValueObject|StringValueObject
    {
        $this->ensureIsAValueObjectClass($className);

        return $this->hydrateObject(
            $className,
            ['value' => $index],
        );
    }
}
