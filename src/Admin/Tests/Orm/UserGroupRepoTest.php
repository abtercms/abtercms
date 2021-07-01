<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Tests\Orm;

use AbterPhp\Admin\Domain\Entities\UserGroup as Entity;
use AbterPhp\Admin\Orm\DataMappers\UserGroupSqlDataMapper;
use AbterPhp\Admin\Orm\UserGroupRepo;
use AbterPhp\Admin\Tests\TestCase\Orm\RepoTestCase;
use Opulence\Orm\DataMappers\IDataMapper;
use Opulence\Orm\IEntityRegistry;
use PHPUnit\Framework\MockObject\MockObject;

class UserGroupRepoTest extends RepoTestCase
{
    /** @var UserGroupRepo - System Under Test */
    protected UserGroupRepo $sut;

    /** @var UserGroupSqlDataMapper|MockObject */
    protected $dataMapperMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new UserGroupRepo($this->className, $this->dataMapperMock, $this->unitOfWorkMock);
    }

    /**
     * @return UserGroupSqlDataMapper|MockObject
     */
    protected function createDataMapperMock(): IDataMapper
    {
        /** @var UserGroupSqlDataMapper|MockObject $mock */
        return $this->createMock(UserGroupSqlDataMapper::class);
    }

    public function testGetAll()
    {
        $entityStub0 = new Entity('foo0', 'foo-0', 'Foo 0');
        $entityStub1 = new Entity('foo1', 'foo-1', 'Foo 1');
        $entities    = [$entityStub0, $entityStub1];

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getAll')->willReturn($entities);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getAll();

        $this->assertSame($entities, $actualResult);
    }

    public function testGetByIdFromCache()
    {
        $entityStub = new Entity('foo0', 'foo-0', 'Foo 0');

        $entityRegistry = $this->createEntityRegistryStub($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $this->dataMapperMock->expects($this->never())->method('getById');

        $id = 'foo';

        $actualResult = $this->sut->getById($id);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testGetByIdFromDataMapper()
    {
        $entityStub = new Entity('foo0', 'foo-0', 'Foo 0');

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $this->dataMapperMock->expects($this->once())->method('getById')->willReturn($entityStub);

        $id = 'foo';

        $actualResult = $this->sut->getById($id);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testAdd()
    {
        $entityStub = new Entity('foo0', 'foo-0', 'Foo 0');

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForInsertion')->with($entityStub);

        $this->sut->add($entityStub);
    }

    public function testDelete()
    {
        $entityStub = new Entity('foo0', 'foo-0', 'Foo 0');

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForDeletion')->with($entityStub);

        $this->sut->delete($entityStub);
    }

    public function testGetByIdentifier()
    {
        $identifier = 'foo-0';
        $entityStub = new Entity('foo0', $identifier, 'Foo 0');

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getByIdentifier')->willReturn($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getByIdentifier($identifier);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testGetPage()
    {
        $entityStub0 = new Entity('foo0', 'foo-0', 'Foo 0');
        $entityStub1 = new Entity('foo1', 'foo-1', 'Foo 1');
        $entities    = [$entityStub0, $entityStub1];

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getPage')->willReturn($entities);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getPage(0, 10, [], [], []);

        $this->assertSame($entities, $actualResult);
    }

    /**
     * @param Entity|null $entity
     *
     * @return MockObject
     */
    protected function createEntityRegistryStub(?Entity $entity): MockObject
    {
        $entityRegistry = $this->createMock(IEntityRegistry::class);

        $entityRegistry->expects($this->any())->method('registerEntity');
        $entityRegistry->expects($this->any())->method('getEntity')->willReturn($entity);

        return $entityRegistry;
    }
}
