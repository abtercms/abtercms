<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Tests\Form\Label;

use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\Tests\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\Tests\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class LabelTest extends TestCase
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $attributes = StubAttributeFactory::createAttributes();
        $str        = Attributes::toString($attributes);

        return [
            'simple'            => [
                'a',
                'ABC',
                [],
                null,
                null,
                '<label for="a">ABC</label>',
            ],
            'with attributes'   => [
                'a',
                'ABC',
                $attributes,
                null,
                null,
                "<label$str for=\"a\">ABC</label>",
            ],
            'with translations' => [
                'a',
                'ABC',
                [],
                ['ABC' => 'CBA'],
                null,
                '<label for="a">CBA</label>',
            ],
            'custom tag'        => [
                'a',
                'ABC',
                [],
                [],
                'foo',
                '<foo for="a">ABC</foo>',
            ],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string              $inputId
     * @param string              $content
     * @param array<string,mixed> $attributes
     * @param string[]|null       $translations
     * @param string|null         $tag
     * @param string              $expectedResult
     */
    public function testRender(
        string $inputId,
        string $content,
        ?array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createElement($inputId, $content, $attributes, $translations, $tag);

        $this->assertSame($expectedResult, (string)$sut);
    }

    /**
     * @param string              $inputId
     * @param string              $content
     * @param array<string,mixed> $attributes
     * @param string[]|null       $translations
     * @param string|null         $tag
     *
     * @return Label
     */
    private function createElement(
        string $inputId,
        string $content,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): Label {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $label = new Label($inputId, $content, [], $attributes, $tag);

        $label->setTranslator($translatorMock);

        return $label;
    }
}