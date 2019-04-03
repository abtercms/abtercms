<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events\Listeners;

use AbterPhp\Framework\Events\NavigationReady;
use AbterPhp\Framework\Html\Component\Button;
use AbterPhp\Framework\Html\Component\ButtonFactory;
use AbterPhp\Framework\Navigation\Navigation;
use AbterPhp\Framework\Session\MockSessionFactory;
use Opulence\Sessions\ISession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NavigationBuilderTest extends TestCase
{
    /** @var NavigationBuilder */
    protected $sut;

    /** @var array */
    protected $sessionData = [
        'username' => 'jane',
    ];

    /** @var ISession|MockObject */
    protected $sessionMock;

    /** @var ButtonFactory|MockObject */
    protected $buttonFactoryMock;

    public function setUp()
    {
        $this->sessionMock = MockSessionFactory::create($this, $this->sessionData);

        $this->buttonFactoryMock = $this->getMockBuilder(ButtonFactory::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->sut = new NavigationBuilder($this->sessionMock, $this->buttonFactoryMock);
    }

    public function testHandleWithoutMatchingIntent()
    {
        /** @var Navigation|MockObject $navigationMock */
        $navigationMock = $this->getMockBuilder(Navigation::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $event = new NavigationReady($navigationMock);

        $navigationMock->expects($this->once())->method('hasIntent')->willReturn(false);

        $this->sut->handle($event);
    }

    public function testHandleWithMatchingIntent()
    {
        /** @var Navigation|MockObject $navigationMock */
        $navigationMock = $this->getMockBuilder(Navigation::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $event = new NavigationReady($navigationMock);

        $navigationMock->expects($this->once())->method('hasIntent')->willReturn(true);

        $this->buttonFactoryMock->expects($this->once())->method('createFromName')->willReturn(new Button());

        $this->sut->handle($event);
    }
}
