<?php

namespace ltribolet\Collection\Tests;

use ltribolet\Collection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testBuild()
    {
        $generator = $this->provideGenerator();
        $array = range('a', 'z');

        $collection1 = Collection::build($array);
        $collection2 = Collection::build($generator);

        self::assertInstanceOf(\Iterator::class, $collection1->getInnerIterator());
        self::assertInstanceOf(\Iterator::class, $collection2->getInnerIterator());

        $this->expectException(\InvalidArgumentException::class);
        Collection::build(null);
    }

    public function testEach()
    {
        $generator = $this->provideGenerator();
        $collection = Collection::build($generator);

        $string = '';
        $collection->each(function ($value) use (&$string){
            $string .= $value;

            return true;
        });

        self::assertSame('abcdefghijklmnopqrstuvwxyz', $string);
    }

    public function testFilter()
    {
        $generator = $this->provideGenerator();
        $collection = Collection::build($generator);
        $filter = 'z';

        $result = $collection->filter(function ($value) use($filter) {
            return $value === $filter;
        });

        $result->rewind();
        self::assertSame('z', $result->current());
    }

    public function testFromArray()
    {
        $array = range(1, 10);
        $collection = Collection::fromArray($array);

        $i = 0;
        foreach ($collection as $value) {
            self::assertSame($array[$i], $value);
            ++$i;
        }
    }

    public function testFromCallable()
    {
        $callable = function() {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
            yield 5;
        };
        $collection = Collection::fromCallable($callable);

        $i = 1;
        foreach ($collection as $value) {
            self::assertSame($i, $value);
            ++$i;
        }
    }

    public function testFailFromCallable()
    {
        $this->expectException(\InvalidArgumentException::class);

        $callable = function() {
            return 1;
        };

        Collection::fromCallable($callable);
    }

    public function testFromGenerator()
    {
        $generator = $this->provideGenerator();
        $collection = Collection::fromGenerator($generator);

        foreach ($collection as $value) {
            self::assertSame($generator->current(), $value);
            $generator->next();
        }
    }

    public function testMap()
    {
        $array = range(1, 10);
        $collection = Collection::build($array);


        $result = $collection->map(function ($value) {
            return $value * 10;
        });

        $i = 0;
        foreach ($result as $value) {
            self::assertSame($array[$i] * 10, $value);
            ++$i;
        }
    }

    public function testChainingMethods()
    {
        $array = range(1, 10);
        $truth = range(2, 10, 2);
        $collection = Collection::build($array);


        $result = $collection
            ->filter(function($value) {
                return $value % 2 === 0;
            })
            ->map(function ($value) {
                return $value * 10;
            }
        );

        $i = 0;
        foreach ($result as $value) {
            self::assertSame($truth[$i] * 10, $value);
            ++$i;
        }
    }

    private function provideGenerator()
    {
        yield from range('a', 'z');
    }
}
