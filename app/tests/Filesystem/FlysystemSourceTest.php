<?php

namespace App\Tests\Filesystem;

use App\Filesystem\FileInfoInterface;
use App\Filesystem\FlysystemSource;
use League\Flysystem;
use PHPUnit\Framework\TestCase;

/**
 * Class FlysystemSourceTest
 *
 * @see https://flysystem.thephpleague.com/docs/architecture/
 *
 * @package App\Tests\Filesystem
 */
class FlysystemSourceTest extends TestCase
{
    /** @var Flysystem\FilesystemInterface $flysystem_prophecy */
    private $flysystem_prophecy;

    public function setUp(): void
    {
        parent::setUp();
        $this->flysystem_prophecy = $this->prophesize(Flysystem\FilesystemInterface::class);
    }

    /**
     * If no path is given, all the files in the flysystem should be returned
     */
    public function test_files_with_no_path() : void
    {
        // define prophecies for a flysystem that has a couple of different files in a nested structure
        $this->flysystem_prophecy
            ->getMetadata(null)
            ->willReturn(['type' => 'dir', 'path' => '/']);

        $this->flysystem_prophecy
            ->listContents(null, true)
            ->willReturn([
                ['type' => 'file', 'path' => '/file-1.php'],
                ['type' => 'dir', 'path' => '/dir/'],
                ['type' => 'file', 'path' => '/dir/file-2.php'],
                ['type' => 'dir', 'path' => '/dir/subdir/'],
                ['type' => 'file', 'path' => '/dir/subdir/file-3.php'],
            ]);

        // create the source adapter
        $adapter = new FlysystemSource($this->flysystem_prophecy->reveal());

        // get the files and check we have the right ones
        $files = $adapter->files();
        $this->assertCount(3, $files);
        $this->assertContainsOnlyInstancesOf(FileInfoInterface::class, $files);
        $this->assertCount(1, array_filter($files, function (FileInfoInterface $fileInfo) {
            return $fileInfo->path() === '/file-1.php';
        }));
        $this->assertCount(1, array_filter($files, function (FileInfoInterface $fileInfo) {
            return $fileInfo->path() === '/dir/file-2.php';
        }));
        $this->assertCount(1, array_filter($files, function (FileInfoInterface $fileInfo) {
            return $fileInfo->path() === '/dir/subdir/file-3.php';
        }));
    }

    public function test_files_with_file_path() : void
    {
        // define prophecies for a flysystem has a file at a particular path
        $this->flysystem_prophecy
            ->getMetadata('/dir/file-2.php')
            ->willReturn(['type' => 'file', 'path' => '/dir/file-2.php']);

        // create the source adapter
        $adapter = new FlysystemSource($this->flysystem_prophecy->reveal());

        // get the files at a particular path and check we have the right one
        $files = $adapter->files('/dir/file-2.php');
        $this->assertCount(1, $files);
        $this->assertEquals('/dir/file-2.php', $files[0]->path());
    }

    public function test_files_with_folder_path() : void
    {
        // define prophecies for a flysystem has a folder at a particular path
        $this->flysystem_prophecy
            ->getMetadata('/dir/')
            ->willReturn(['type' => 'dir', 'path' => '/dir/']);

        $this->flysystem_prophecy
            ->listContents('/dir/', true)
            ->willReturn([
                ['type' => 'dir', 'path' => '/dir/'],
                ['type' => 'file', 'path' => '/dir/file-2.php'],
                ['type' => 'dir', 'path' => '/dir/subdir/'],
                ['type' => 'file', 'path' => '/dir/subdir/file-3.php'],
            ]);

        // create the source adapter
        $adapter = new FlysystemSource($this->flysystem_prophecy->reveal());

        // get the files and check we have the right ones
        $files = $adapter->files('/dir/');
        $this->assertCount(2, $files);
        $this->assertContainsOnlyInstancesOf(FileInfoInterface::class, $files);
        $this->assertCount(1, array_filter($files, function (FileInfoInterface $fileInfo) {
            return $fileInfo->path() === '/dir/file-2.php';
        }));
        $this->assertCount(1, array_filter($files, function (FileInfoInterface $fileInfo) {
            return $fileInfo->path() === '/dir/subdir/file-3.php';
        }));
    }

    /**
     * The path is nonexistent / malformed / access denied, etc.
     */
    public function test_files_with_invalid_path() : void
    {
        // define prophecies for a flysystem that fails to find anything at a particular path
        $this->flysystem_prophecy
            ->getMetadata('/dir/')
            ->willReturn(false);

        // create the source adapter
        $adapter = new FlysystemSource($this->flysystem_prophecy->reveal());

        // try to get the files. for now let's just say some kind of exception should be thrown but not be specific
        $this->expectException(\Exception::class);
        $adapter->files('/dir/');
    }
}