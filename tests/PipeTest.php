<?php

namespace Apantle\FunPHP\Test;

use \PHPUnit\Framework\TestCase;
use function Apantle\FunPHP\pipe;

class CustomClosure {
    public function __invoke() {}
}

class PipeTest extends TestCase
{
    public function testPipeFunction()
    {
        $closure1 = $this->prophesize(CustomClosure::class);
        $closure1->__invoke('o','n','e')->willReturn('one');
        $closure2 = $this->prophesize(CustomClosure::class);
        $closure2->__invoke('one')->willReturn('one, two');
        $closure3 = $this->prophesize(CustomClosure::class);
        $closure3->__invoke('one, two')->willReturn('one, two, three');

        $result = pipe(
          $closure1->reveal(),
          $closure2->reveal(),
          $closure3->reveal()
        )('o', 'n', 'e');

        $this->assertEquals('one, two, three', $result);

        $closure1->checkProphecyMethodsPredictions();
        $closure2->checkProphecyMethodsPredictions();
        $closure3->checkProphecyMethodsPredictions();
    }
}
