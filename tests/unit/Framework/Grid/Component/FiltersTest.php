<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Grid\Filter\Filter;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\I18n\MockTranslatorFactory;
use PHPUnit\Framework\MockObject\MockObject;

class FiltersTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNodesMustBeFilters()
    {
        $sut = new Filters();

        $sut[] = new Component();
    }

    public function testSetParamsSetsParamsOnAllNodes()
    {
        $params = ['foo' => 'Foo!', 'bar' => 'Bar?'];

        /** @var Filter|MockObject $filter0 */
        $filter0 = $this->getMockBuilder(Filter::class)
            ->setMethods(['setParams'])
            ->getMock();

        /** @var Filter|MockObject $filter1 */
        $filter1 = $this->getMockBuilder(Filter::class)
            ->setMethods(['setParams'])
            ->getMock();

        $sut = new Filters();

        $sut[] = $filter0;
        $sut[] = $filter1;

        $filter0->expects($this->once())->method('setParams')->with($params)->willReturnSelf();
        $filter1->expects($this->once())->method('setParams')->with($params)->willReturnSelf();

        $sut->setParams($params);
    }

    /**
     * @return array
     */
    public function getUrlProvider(): array
    {
        return [
            'no-filters'         => ['/foo?', '/foo?'],
            'no-matching-filter' => ['/foo?', '/foo?', '', ''],
            'one-filter'         => ['/foo?', '/foo?bar&', 'bar'],
            'two-filters'        => ['/foo?', '/foo?bar&baz&', 'bar', 'baz'],
        ];
    }

    /**
     * @dataProvider getUrlProvider
     *
     * @param string $baseUrl
     * @param string $expectedResult
     * @param string ...$whereConditions
     */
    public function testGetUrlCollectsQueryPartsFromAllNodes(
        string $baseUrl,
        string $expectedResult,
        string ...$whereConditions
    ) {
        $sut = new Filters();

        foreach ($whereConditions as $queryPart) {
            /** @var Filter|MockObject $filter */
            $filter = $this->getMockBuilder(Filter::class)
                ->setMethods(['getQueryPart'])
                ->getMock();

            $filter->expects($this->once())->method('getQueryPart')->willReturn($queryPart);

            $sut[] = $filter;
        }

        $actualResult = $sut->getUrl($baseUrl);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function getWhereConditionsProvider(): array
    {
        return [
            'no-filters'          => [[], []],
            'one-exact-filter'    => [['a = ?'], ['a = ?']],
            'one-prefix-filter'   => [['a LIKE ?'], ['a LIKE ?']],
            'one-like-filter'     => [['a LIKE ?'], ['a LIKE ?']],
            'one-regexp-filter'   => [['a REGEXP ?'], ['a REGEXP ?']],
            'two-filters-simple'  => [['a = ?', 'b = ?'], ['a = ?'], ['b = ?']],
            'two-filters-complex' => [['a LIKE ?', 'b REGEXP ?'], ['a LIKE ?'], ['b REGEXP ?']],
        ];
    }

    /**
     * @dataProvider getWhereConditionsProvider
     *
     * @param array $expectedResult
     * @param array ...$whereConditions
     */
    public function testGetWhereConditionsCollectsPartsFromAllNodes(
        array $expectedResult,
        array ...$whereConditions
    ) {
        $sut = new Filters();

        foreach ($whereConditions as $whereCondition) {
            /** @var Filter|MockObject $filter */
            $filter = $this->getMockBuilder(Filter::class)
                ->setMethods(['getWhereConditions'])
                ->getMock();

            $filter->expects($this->once())->method('getWhereConditions')->willReturn($whereCondition);

            $sut[] = $filter;
        }

        $actualResult = $sut->getWhereConditions();

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function getSqlParamsProvider(): array
    {
        return [
            'no-filters'          => [[], []],
            'one-exact-filter'    => [['a' => 'abc'], ['a' => 'abc']],
            'one-prefix-filter'   => [['a' => 'abc%'], ['a' => 'abc%']],
            'one-like-filter'     => [['a' => '%ab%c%'], ['a' => '%ab%c%']],
            'one-regexp-filter'   => [['a' => '^abc$'], ['a' => '^abc$']],
            'two-filters-simple'  => [['a' => 'abc', 'b' => 'bcd'], ['a' => 'abc'], ['b' => 'bcd']],
            'two-filters-complex' => [['a' => '%ab%c%', 'b' => '^bcd$'], ['a' => '%ab%c%'], ['b' => '^bcd$']],
        ];
    }

    /**
     * @dataProvider getSqlParamsProvider
     *
     * @param array $expectedResult
     * @param array ...$sqlParams
     */
    public function testGetSqlParamsCollectsPartsFromAllNodes(
        array $expectedResult,
        array ...$sqlParams
    ) {
        $sut = new Filters();

        foreach ($sqlParams as $sqlParamsPiece) {
            /** @var Filter|MockObject $filter */
            $filter = $this->getMockBuilder(Filter::class)
                ->setMethods(['getQueryParams'])
                ->getMock();

            $filter->expects($this->once())->method('getQueryParams')->willReturn($sqlParamsPiece);

            $sut[] = $filter;
        }

        $actualResult = $sut->getSqlParams();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testSetTranslatorSetsTranslatorOnButtons()
    {
        $mockTranslator = MockTranslatorFactory::createSimpleTranslator($this, []);

        $sut = new Filters();

        $sut->setTranslator($mockTranslator);

        $this->assertNotNull($sut->getHiderBtn()->getTranslator());
        $this->assertNotNull($sut->getFilterBtn()->getTranslator());
        $this->assertNotNull($sut->getResetBtn()->getTranslator());
    }

    public function testSetTranslatorSetsTranslatorOnFilters()
    {
        $filterCount = 2;

        $mockTranslator = MockTranslatorFactory::createSimpleTranslator($this, []);

        $sut = new Filters();

        for ($i = 0; $i < $filterCount; $i++) {
            /** @var Filter|MockObject $filter */
            $filter = $this->getMockBuilder(Filter::class)
                ->setMethods(['setTranslator'])
                ->getMock();

            $filter->expects($this->once())->method('setTranslator')->with($mockTranslator);

            $sut[] = $filter;
        }

        $sut->setTranslator($mockTranslator);
    }

    public function testGetAllNodesIncludesButtons()
    {
        $sut = new Filters();

        $allNodes = $sut->getAllNodes();

        $this->assertContains($sut->getHiderBtn(), $allNodes);
        $this->assertContains($sut->getFilterBtn(), $allNodes);
        $this->assertContains($sut->getResetBtn(), $allNodes);
    }

    public function testGetAllNodesReturnsFilters()
    {
        $filterCount = 2;

        $sut = new Filters();

        for ($i = 0; $i < $filterCount; $i++) {
            /** @var Filter|MockObject $filter */
            $filter = $this->getMockBuilder(Filter::class)->getMock();

            $sut[] = $filter;
        }

        $allNodes = $sut->getAllNodes();

        $this->assertCount($filterCount, $sut);
        for ($i = 0; $i < $filterCount; $i++) {
            $this->assertContains($sut[$i], $allNodes);
        }
    }

    public function testRenderWithoutFilters()
    {
        $sut = new Filters();

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertRegExp('/^\<div class\=\"hidable\"/', $actualResult);
        $this->assertRegExp('/\<p class\=\"hider\"\>\<button/', $actualResult);
        $this->assertRegExp('/\<div class\=\"hidee\"\>\<form/', $actualResult);

        $this->assertSame($actualResult, $repeatedResult);
    }

    public function testRenderWithFilters()
    {
        $filterCount = 2;

        $sut = new Filters();

        for ($i = 0; $i < $filterCount; $i++) {
            /** @var Filter|MockObject $filter */
            $filter = $this->getMockBuilder(Filter::class)->setMethods(['__toString'])->getMock();
            $filter->expects($this->atLeastOnce())->method('__toString')->willReturn("filter-$i");
            $sut[] = $filter;
        }

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertRegExp('/^\<div class\=\"hidable\"/', $actualResult);
        $this->assertRegExp('/\<p class\=\"hider\"\>\<button/', $actualResult);
        $this->assertRegExp('/\<div class\=\"hidee\"\>\<form/', $actualResult);

        $this->assertSame($actualResult, $repeatedResult);
    }

    public function testSetTemplateCanOverruleDefaultTemplate()
    {
        $template = '--||--';

        $sut = new Filters();

        $sut->setTemplate($template);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($template, $actualResult);
        $this->assertSame($actualResult, $repeatedResult);
    }
}