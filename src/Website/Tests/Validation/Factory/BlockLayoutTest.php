<?php

declare(strict_types=1);

namespace AbterPhp\Website\Tests\Validation\Factory;

use AbterPhp\Admin\Tests\TestDouble\Validation\StubRulesFactory;
use AbterPhp\Framework\Validation\Rules\Forbidden;
use AbterPhp\Website\Validation\Factory\BlockLayout;
use Opulence\Validation\Rules\Factories\RulesFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BlockLayoutTest extends TestCase
{
    /** @var BlockLayout - System Under Test */
    protected BlockLayout $sut;

    /** @var RulesFactory|MockObject */
    protected $rulesFactoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->rulesFactoryMock = StubRulesFactory::createRulesFactory(
            $this,
            ['forbidden' => new Forbidden()]
        );

        $this->sut = new BlockLayout($this->rulesFactoryMock);
    }

    /**
     * @return array
     */
    public function createValidatorProvider(): array
    {
        return [
            'empty-data'                          => [
                [],
                true,
            ],
            'valid-data'                          => [
                [
                    'identifier' => 'foo',
                    'body'       => 'bar',
                ],
                true,
            ],
            'invalid-id-present'                  => [
                [
                    'id'         => 'baf16ace-8fae-48a8-bbad-a610d7960e31',
                    'identifier' => 'foo',
                ],
                false,
            ],
        ];
    }

    /**
     * @dataProvider createValidatorProvider
     *
     * @param array $data
     * @param bool  $expectedResult
     */
    public function testCreateValidatorExisting(array $data, bool $expectedResult)
    {
        $validator = $this->sut->createValidator();

        $actualResult = $validator->isValid($data);

        $this->assertSame($expectedResult, $actualResult);
    }
}
