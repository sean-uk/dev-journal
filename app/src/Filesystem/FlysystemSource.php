<?php

namespace App\Filesystem;

use League\Flysystem;
use Prophecy\Prophet;

/**
 * Class FlysystemSource
 *
 * A Flysystem file source adaptor for this package.
 * @see https://flysystem.thephpleague.com/docs/
 *
 * @package App\Filesystem
 */
class FlysystemSource implements SourceInterface
{
    /** @var Flysystem\FilesystemInterface */
    private $filesystem;

    /**
     * FlysystemSource constructor.
     * @param Flysystem\FilesystemInterface $filesystem
     */
    public function __construct(Flysystem\FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritDoc
     *
     * @throws Flysystem\FileNotFoundException
     * @throws FilesystemException
     */
    public function files(?string $path = null): array
    {
        // check the type of what the path points to.
        $pathMetadata = $this->filesystem->getMetadata($path);

        // if nothing was found, throw an exception. returning an empty array might be misleading.
        if (!$pathMetadata) {
            throw new FilesystemException();
        }

        // if it's a file, just return the metadata for that
        if ($pathMetadata['type'] === 'file') {
            return [new FileInfo($pathMetadata['path'])];
        }

        // list the contents of the flysystem
        // contents should be arrays of metadata: https://flysystem.thephpleague.com/docs/architecture/
        /** @var array[] $contents */
        $contents = $this->filesystem->listContents($path, true);

        // reduce the list to just files
        $files = [];
        foreach ($contents as $item) {
            if ($item['type'] === 'file') {
                $files[] = $item;
            }
        }

        // construct metadata objects for each file
        $metadata = [];
        foreach ($files as $file) {
            $metadata[] = new FileInfo($file['path']);
        }

        return $metadata;
    }

    /**
     * @inheritDoc
     */
    public function content(string $path): string
    {
        // TODO: Implement content() method.
    }
}