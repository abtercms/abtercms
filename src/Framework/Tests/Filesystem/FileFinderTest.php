<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Tests\Filesystem;

use AbterPhp\Framework\Filesystem\FileFinder;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileFinderTest extends TestCase
{
    /** @var FileFinder - System Under Test */
    protected FileFinder $sut;

    public function setUp(): void
    {
        $this->sut = new FileFinder();

        parent::setUp();
    }

    /**
     * @return FilesystemOperator|MockObject
     */
    protected function createFilesystemMock()
    {
        return $this->createMock(Filesystem::class);
    }

    public function testReadWithoutFilesystems(): void
    {
        $path = 'foo';

        $actualResult = $this->sut->read($path);

        $this->assertNull($actualResult);
    }

    public function testReadWithOnlyDefaultFilesystem(): void
    {
        $fs = $this->createFilesystemMock();

        $path           = 'foo';
        $expectedResult = 'bar';

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->any())->method('fileExists')->willReturn(true);
        $fs->expects($this->once())->method('read')->with($path)->willReturn($expectedResult);

        $actualResult = $this->sut->read($path);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReadWithOnlyVendorFilesystemImplicit(): void
    {
        $fs = $this->createFilesystemMock();

        $path           = '/vendor-one/foo';
        $realPath       = '/foo';
        $expectedResult = 'bar';

        $this->sut->registerFilesystem($fs, 'vendor-one', 1);

        $fs->expects($this->any())->method('fileExists')->willReturn(true);
        $fs->expects($this->once())->method('read')->with($realPath)->willReturn($expectedResult);

        $actualResult = $this->sut->read($path);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReadWithOnlyVendorFilesystemExplicit(): void
    {
        $fs = $this->createFilesystemMock();

        $path           = 'foo';
        $expectedResult = 'bar';

        $this->sut->registerFilesystem($fs, 'vendor-one', 1);

        $fs->expects($this->any())->method('fileExists')->willReturn(true);
        $fs->expects($this->once())->method('read')->with($path)->willReturn($expectedResult);

        $actualResult = $this->sut->read($path, 'vendor-one');

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReadRespectsPriorities(): void
    {
        $fs1 = $this->createFilesystemMock();
        $fs2 = $this->createFilesystemMock();

        $path           = 'foo';
        $expectedResult = 'bar';

        $this->sut->registerFilesystem($fs1);
        $this->sut->registerFilesystem($fs2, 'vendor-one', 1);

        $fs1->expects($this->any())->method('fileExists')->willReturn(true);
        $fs2->expects($this->any())->method('fileExists')->willReturn(true);
        $fs1->expects($this->never())->method('read');
        $fs2->expects($this->once())->method('read')->with($path)->willReturn($expectedResult);

        $actualResult = $this->sut->read($path, 'vendor-one');

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReadSuppressesExceptionsInReading(): void
    {
        $fs1 = $this->createFilesystemMock();

        $path = 'foo';

        $this->sut->registerFilesystem($fs1, 'vendor-one', 1);

        $fs1->expects($this->any())->method('fileExists')->willReturn(true);
        $fs1->expects($this->once())->method('read')->willThrowException(new \Exception('baz'));

        $actualResult = $this->sut->read($path, 'vendor-one');

        $this->assertNull($actualResult);
    }

    public function testFileExistsWithoutFilesystems(): void
    {
        $path = 'foo';

        $actualResult = $this->sut->fileExists($path);

        $this->assertFalse($actualResult);
    }

    public function testFileExistsWithOnlyDefaultFilesystem(): void
    {
        $fs = $this->createFilesystemMock();

        $path = 'foo';

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->once())->method('fileExists')->willReturn(true);

        $actualResult = $this->sut->fileExists($path);

        $this->assertTrue($actualResult);
    }

    public function testFileExistsWithOnlyVendorFilesystemImplicit(): void
    {
        $fs = $this->createFilesystemMock();

        $path = '/vendor-one/foo';

        $this->sut->registerFilesystem($fs, 'vendor-one', 1);

        $fs->expects($this->once())->method('fileExists')->willReturn(true);

        $actualResult = $this->sut->fileExists($path);

        $this->assertTrue($actualResult);
    }

    public function testFileExistsWithOnlyVendorFilesystemExplicit(): void
    {
        $fs = $this->createFilesystemMock();

        $path = 'foo';

        $this->sut->registerFilesystem($fs, 'vendor-one', 1);

        $fs->expects($this->once())->method('fileExists')->willReturn(true);

        $actualResult = $this->sut->fileExists($path, 'vendor-one');

        $this->assertTrue($actualResult);
    }
}
