<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Tests\Events;

use AbterPhp\Framework\Events\TemplateEngineReady;
use AbterPhp\Framework\Template\Engine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TemplateEngineReadyTest extends TestCase
{
    /** @var TemplateEngineReady - System Under Test */
    protected TemplateEngineReady $sut;

    /** @var Engine|MockObject */
    protected $engineMock;

    public function setUp(): void
    {
        $this->engineMock = $this->createMock(Engine::class);

        $this->sut = new TemplateEngineReady($this->engineMock);

        parent::setUp();
    }

    public function testGetEngine(): void
    {
        $actualResult = $this->sut->getEngine();

        $this->assertSame($this->engineMock, $actualResult);
    }
}
