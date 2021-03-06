<?php

declare(strict_types = 1);

namespace ltribolet\Collection;

final class Collection extends \IteratorIterator
{
    /**
     * @param mixed $elements
     *
     * @throws \InvalidArgumentException
     * @return Collection
     */
    public static function build($elements = null): Collection
    {
        if (\is_callable($elements)) {
            return static::fromCallable($elements);
        }

        if ($elements instanceof \Generator) {
            return static::fromGenerator($elements);
        }

        if (\is_array($elements)) {
            return static::fromArray($elements);
        }

        if ($elements === null) {
            return static::fromArray([]);
        }

        throw new \InvalidArgumentException('Type unknown, cannot handle');
    }

    public static function fromArray(array $elements): Collection
    {
        return new static(new \ArrayIterator($elements));
    }

    public static function fromGenerator(\Generator $elements): Collection
    {
        return new static($elements);
    }

    public static function fromCallable(callable $callable): Collection
    {
        // Kick off the callable into a generator.
        $generator = $callable();
        if (!$generator instanceof \Generator) {
            throw new \InvalidArgumentException('fromGenerator failed');
        }

        return new static($generator);
    }

    public function each(callable $callback): Collection
    {
        foreach ($this->getInnerIterator() as $key => $value) {
            $callback($value, $key);
        }

        return $this;
    }

    public function filter(callable $callback): Collection
    {
        return self::fromCallable(function () use ($callback) {
            foreach ($this->getInnerIterator() as $key => $value) {
                if ($callback($value, $key)) {
                    yield $key => $value;
                }
            }
        });
    }

    public function map(callable $callback): Collection
    {
        return self::fromCallable(function () use ($callback) {
            foreach ($this->getInnerIterator() as $key => $value) {
                yield $key => $callback($value);
            }
        });
    }

    public function add($value, $key = null)
    {
        return self::fromCallable(function () use ($value, $key) {
            if (!$key) {
                $value = [$key => $value];
            }

            $appendIterator = new \AppendIterator();
            $appendIterator->append($this->getInnerIterator());
            $appendIterator->append(self::build($value));

            foreach ($appendIterator as $iterationKey => $iterationValue) {
                yield $key => $iterationValue;
            }
        });
    }

    /**
     * @param callable|null $callback
     * @param mixed|null    $default
     *
     * @return mixed
     */
    public function first(?callable $callback = null, $default = null)
    {
        if (!$callback && \iterator_count($this->getInnerIterator()) === 0) {
            return $default;
        }

        if (!$callback) {
            $this->getInnerIterator()->rewind();

            return $this->getInnerIterator()->current();
        }

        foreach ($this->getInnerIterator() as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }
}
