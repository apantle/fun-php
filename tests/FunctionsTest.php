<?php

namespace Apantle\FunPHP\Test;

use PHPUnit\Framework\TestCase;
use function Apantle\FunPHP\identity;
use function Apantle\FunPHP\head;
use function Apantle\FunPHP\compose;
use function Apantle\FunPHP\constant;
use function Apantle\FunPHP\curryToUnary;

class FunctionsTest extends TestCase
{
    public function testIdentity()
    {
        $id = new \DateTime();

        $maybeSameId = identity($id);

        $this->assertSame($id, $maybeSameId);
    }

    public function testHead()
    {
        $list = [1, 2, 3];

        $first = head($list);

        $this->assertEquals(1, $first);
    }

    public function testCompose()
    {
        $list = [
            ['A', 'B'],
            2,
            3,
        ];

        $composedFn = compose('Apantle\FunPHP\head', 'Apantle\FunPHP\head');

        $this->assertTrue(is_callable($composedFn));

        $actual = $composedFn($list);

        $this->assertEquals('A', $actual);
    }

    public function testConstant()
    {
        $date = new \DateTimeImmutable('now');

        $constantFn = constant($date);
        $this->assertSame($date, call_user_func($constantFn));
    }

    public function testCurryToUnaryArgOnRight()
    {
        $expectingPredicate = curryToUnary('array_filter', [1, 2, 3, 4]);

        $predicate = function ($num) { return $num % 2 === 0; };

        $result = $expectingPredicate($predicate);

        $this->assertEquals([2, 4], array_values($result));
    }

    public function testCurryToUnaryCustomUnaryArgPosition()
    {
        $map = curryToUnary('array_map', \Apantle\FunPHP\_, [1, 2, 3, 4]);
        $result = $map(function ($num) { return $num * 10; });

        $this->assertEquals([10, 20, 30, 40], $result);
    }
}
