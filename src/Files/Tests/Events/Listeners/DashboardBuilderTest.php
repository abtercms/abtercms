<?php

declare(strict_types=1);

namespace AbterPhp\Files\Tests\Events\Listeners;

use AbterPhp\Files\Events\Listeners\DashboardBuilder;
use AbterPhp\Framework\Dashboard\Dashboard;
use AbterPhp\Framework\Events\DashboardReady;
use PHPUnit\Framework\TestCase;

class DashboardBuilderTest extends TestCase
{
    /** @var DashboardBuilder - System Under Test */
    protected DashboardBuilder $sut;

    public function setUp(): void
    {
        $this->sut = new DashboardBuilder();
    }

    public function testHandle()
    {
        $dashboardMock = $this->createMock(Dashboard::class);
        $dashboardMock->expects($this->atLeastOnce())->method('offsetSet');

        $event = new DashboardReady($dashboardMock);

        $this->sut->handle($event);
    }
}
