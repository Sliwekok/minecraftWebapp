<?php

declare(strict_types=1);

namespace App\Service\Filesystem;

use App\UniqueNameInterface\ServerDirectoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class FilesystemService extends Filesystem
{

    private const ACCESS_VALUE = 0770;
    private string $path;
    private Filesystem $filesystem;

    public function __construct (
        string $path,
    ) {
        $this->filesystem = new Filesystem();
        $this->path = ServerDirectoryInterface::DIRECTORY. '/' . $path;
    }

    public function getPath (): string {
        return $this->path;
    }

    public function getMinecraftPath (): string {
        return $this->path. '/'. ServerDirectoryInterface::DIRECTORY_MINECRAFT;
    }

    public function getBackupPath (): string {
        return $this->path. '/'. ServerDirectoryInterface::DIRECTORY_BACKUPS;
    }

    public function getAbsolutePath (): string {
        $path = explode('\\', realpath(__DIR__));
        $path = implode('/', array_slice($path,0 , count($path) - 3)) . '/public/';

        return Path::makeAbsolute($this->path, $path);
    }

    public function getAbsoluteMinecraftPath (): string {
        return $this->getAbsolutePath(). '/'. ServerDirectoryInterface::DIRECTORY_MINECRAFT;
    }

    public function createDirectories (): void {
        $this->filesystem->mkdir($this->path, self::ACCESS_VALUE);
        $this->filesystem->mkdir($this->path. "/". ServerDirectoryInterface::DIRECTORY_MINECRAFT);
        $this->filesystem->mkdir($this->path. "/". ServerDirectoryInterface::DIRECTORY_BACKUPS);
    }

    public function storeFile (
        string $subDirectory,
        mixed $file
    ): void {
        $totalPath = $this->path . '/'. $subDirectory. '/'. ServerDirectoryInterface::MINECRAFT_SERVER_FILE;
        $this->filesystem->dumpFile($totalPath, $file);
    }

}