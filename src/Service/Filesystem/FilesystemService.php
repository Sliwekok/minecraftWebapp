<?php

declare(strict_types=1);

namespace App\Service\Filesystem;

use App\UniqueNameInterface\ServerDirectoryInterface;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemService
{

    private const ACCESS_VALUE = 0700;
    private string $path;
    private Filesystem $filesystem;

    public function __construct (
        string $path,
    ) {
        $this->filesystem = new Filesystem();
        $this->path = ServerDirectoryInterface::DIRECTORY. '/' . $path;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function directoryExists (string $path): bool {
        return $this->filesystem->exists($path);
    }

    public function createDirectories (): void {
        $this->filesystem->mkdir($this->path, self::ACCESS_VALUE);
        $this->filesystem->mkdir($this->path. "\\". ServerDirectoryInterface::DIRECTORY_MINECRAFT);
        $this->filesystem->mkdir($this->path. "\\". ServerDirectoryInterface::DIRECTORY_BACKUPS);
    }

    public function storeFile (
        string $subDirectory,
        mixed $file
    ): void {
        $totalPath = $this->path . '/'. $subDirectory. '/'. ServerDirectoryInterface::MINECRAFT_SERVER_FILE;
        $this->filesystem->dumpFile($totalPath, $file);
    }

}