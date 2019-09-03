<?php

use \PHPUnit\Framework\TestCase;
use function Apantle\FunPHP\unfold;
use function Apantle\FunPHP\identity;
use function Apantle\Hashmapper\hashMapper;

class UnfoldTest extends TestCase
{
    public function testBasicUnfold()
    {
        $input = new \DateTimeImmutable();

        $expected = [
            'targetA' => $input,
            'targetB' => $input
        ];

        $actual = unfold([
            'targetA' => 'Apantle\FunPHP\identity',
            'targetB' => 'Apantle\FunPHP\identity',
        ])($input);

        $this->assertEquals($expected, $actual);
        $this->assertEquals($input->format('U'), $actual['targetA']->format('U'));
        $this->assertEquals($input->format('U'), $actual['targetB']->format('U'));
    }

    public function testExpectsTameme()
    {
        $input = [ 1, 2, 3 ];

        $expected = [
            'targetA' => [ 4 => 1, 5 => 2, 6 => 3 ],
            'targetB' => [ 5 => 2 ]
        ];

        $tameme = new class extends ArrayObject {};

        $actual = unfold([
            'targetA' => function ($member, $input = null, $tameme) {
                $build = array_reduce($member, function ($accum, $num) {
                    $accum[$num + 3] = $num;
                    return $accum;
                }, []);

                $tameme['prev'] = $build;
                return $build;
            },
            'targetB' => function ($member, $input, $tameme) {
                $prev = $tameme['prev'];
                $build = [];
                foreach($prev as $key => $val) {
                    if($key % 2 !== 0) {
                        $build[$key] = $val;
                    }
                }
                return $build;
            }
        ], $tameme)($input);

        $this->assertEquals($expected, $actual);
    }

    public function testReceivesOptionalArgument()
    {
        $input = [
            'vendor' => 'tzkmx',
            'utility' => 'unfold'
        ];

        $expected = [
            'vendorName' => 'tzkmx',
            'vendorLen' => 5,
            'serialized' => 'a:2:{s:6:"vendor";s:5:"tzkmx";s:7:"utility";s:6:"unfold";}',
            'utility' => 'unfold',
            'utilLen' => 6,
            'package' => [ 'tzkmx/unfold' => $input ]
        ];

        $tameme = new class extends ArrayObject {};

        $actual = hashMapper([
            'vendor' => [ '...', unfold([
                'vendorName' => 'strval',
                'vendorLen' => 'strlen',
                'serialized' => function ($member, $hash, $tameme) {
                    $tameme['name'] = $member;
                    return serialize($hash);
                }
            ], $tameme)
            ],
            'utility' => [ '...', unfold([
                'utility' => 'strval',
                'utilLen' => 'strlen',
                'package' => function ($member, $hash, $tameme) {
                    $name = $tameme['name'];
                    return [ "$name/$member" => $hash ];
                }
            ], $tameme)
            ]
        ])($input);

        $this->assertEquals($expected, $actual);
    }
}
