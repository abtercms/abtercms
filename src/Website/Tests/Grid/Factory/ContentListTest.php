<?php

declare(strict_types=1);

namespace AbterPhp\Website\Tests\Grid\Factory;

use AbterPhp\Admin\Grid\Factory\GridFactory;
use AbterPhp\Admin\Grid\Factory\PaginationFactory;
use AbterPhp\Website\Grid\Factory\ContentList;
use AbterPhp\Website\Grid\Factory\Table\ContentList as TableFactory;
use AbterPhp\Website\Grid\Filters\ContentList as Filters;
use AbterPhp\Framework\Grid\IGrid;
use Opulence\Routing\Urls\UrlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContentListTest extends TestCase
{
    /** @var ContentList - System Under Test */
    protected ContentList $sut;

    /** @var MockObject|UrlGenerator */
    protected $urlGeneratorMock;

    /** @var MockObject|PaginationFactory */
    protected $paginationFactoryMock;

    /** @var MockObject|TableFactory */
    protected $tableFactoryMock;

    /** @var MockObject|GridFactory */
    protected $gridFactoryMock;

    /** @var MockObject|Filters */
    protected $filtersMock;

    public function setUp(): void
    {
        $this->urlGeneratorMock      = $this->createMock(UrlGenerator::class);
        $this->paginationFactoryMock = $this->createMock(PaginationFactory::class);
        $this->tableFactoryMock      = $this->createMock(TableFactory::class);
        $this->gridFactoryMock       = $this->createMock(GridFactory::class);
        $this->filtersMock           = $this->createMock(Filters::class);

        $this->sut = new ContentList(
            $this->urlGeneratorMock,
            $this->paginationFactoryMock,
            $this->tableFactoryMock,
            $this->gridFactoryMock,
            $this->filtersMock
        );
    }

    public function testCreateGrid()
    {
        $params  = [];
        $baseUrl = '';

        $actualResult = $this->sut->createGrid($params, $baseUrl);

        $this->assertInstanceOf(IGrid::class, $actualResult);
    }
}
