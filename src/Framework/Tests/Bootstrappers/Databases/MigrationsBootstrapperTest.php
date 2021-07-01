<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Tests\Bootstrappers\Databases;

use AbterPhp\Framework\Bootstrappers\Database\MigrationsBootstrapper;
use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\IMigrator;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class MigrationsBootstrapperTest extends TestCase
{
    /** @var MigrationsBootstrapper - System Under Test */
    protected MigrationsBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new MigrationsBootstrapper();
    }

    public function testRegisterBindings(): void
    {
        $connectionMock = $this->getMockBuilder(IConnection::class)->getMock();

        $this->sut->setMigrationPaths([]);

        $container = new Container();
        $container->bindInstance(IConnection::class, $connectionMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(IMigrator::class);
        $this->assertInstanceOf(IMigrator::class, $actual);
    }
}
