<?php

namespace Apantle\FunPHP\Test;

use PHPUnit\Framework\TestCase;
use function Apantle\FunPHP\identity;
use function Apantle\FunPHP\head;
use function Apantle\FunPHP\compose;
use function Apantle\FunPHP\constant;

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
}
