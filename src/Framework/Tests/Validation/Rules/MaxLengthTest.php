<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Tests\Validation\Rules;

use AbterPhp\Framework\Validation\Rules\MaxLength;
use PHPUnit\Framework\TestCase;

class MaxLengthTest extends TestCase
{
    /**
     * @return array[]
     */
    public function passesProvider(): array
    {
        return [
            'empty'             => ['', [], [1], true],
            'max-1-foo'         => ['foo', [], [1], false],
            'max-3-foo-default' => ['foo', [], [3], true],
            'max-3-foo-inc'     => ['foo', [], [3, true], true],
            'max-3-foo'         => ['foo', [], [3, false], false],
        ];
    }

    /**
     * @dataProvider passesProvider
     *
     * @param       $value
     * @param array $allValues
     * @param array $args
     * @param bool  $expectedResult
     */
    public function testPasses($value, array $allValues, array $args, bool $expectedResult): void
    {
        $sut = new MaxLength();
        $sut->setArgs($args);

        $actualResult = $sut->passes($value, $allValues);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetSlug(): void
    {
        $sut = new MaxLength();
        $sut->setArgs([1]);

        $actualResult = $sut->getSlug();

        $this->assertSame('maxLength', $actualResult);
    }

    public function testPassesThrowsExceptionIfMaxIsNotSet(): void
    {
        $this->expectException(\LogicException::class);

        $sut = new MaxLength();

        $sut->passes('foo', []);
    }
}
