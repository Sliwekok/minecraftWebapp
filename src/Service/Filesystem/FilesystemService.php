<?php

declare(strict_types=1);

namespace App\Service\Filesystem;

use App\UniqueNameInterface\ServerDirectoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

class FilesystemService extends Filesystem
{

    /**
     * Default path is in public directory, so we don't need to initialize absolute path at start.
     * Next we add user path to server.
     * So the path looks like: {symfony}/public/servers/{user}/{serverName}
     */

    private const ACCESS_VALUE = 0755;
    private string $path;
    private string $userServer;
    private Filesystem $filesystem;
    private string $absolutePath;

    public function __construct (
        string $path,
    ) {
        $this->filesystem = new Filesystem();
        $this->path = ServerDirectoryInterface::DIRECTORY. '/' . str_replace(['"',"'"], "", $path);
        $this->userServer = str_replace(['"',"'"], "", $path);
        $this->absolutePath = $this->createAbsolutePath();
    }

    public function getPath (): string {
        return $this->path;
    }

    public function getBackupPath (): string {
        return $this->path. '/'. ServerDirectoryInterface::DIRECTORY_BACKUPS;
    }

    public static function getAbsolutePublicPath (): string {
        $path = explode('\\', realpath(__DIR__));
        // trim last 3 parts of path since it's path to service directory
        $path = implode('/', array_slice($path,0 , count($path) - 3)) . '/public/';

        return $path;
    }

    public function createAbsolutePath (): string {
        $path = $this->getAbsolutePublicPath();

        return Path::makeAbsolute($this->path, $path);
    }

    public function getAbsolutePath (): string {
        return $this->absolutePath;
    }

    public function getAbsoluteMinecraftPath (): string {
        return $this->absolutePath. '/'. ServerDirectoryInterface::DIRECTORY_MINECRAFT;
    }

    public function getAbsoluteBackupPath (): string {
        return $this->absolutePath. '/'. ServerDirectoryInterface::DIRECTORY_BACKUPS;
    }

    public function createDirectories (): void {
        $this->filesystem->mkdir($this->getAbsolutePath(), self::ACCESS_VALUE);
        $this->filesystem->mkdir($this->getAbsolutePath(). "/". ServerDirectoryInterface::DIRECTORY_MINECRAFT, self::ACCESS_VALUE);
        $this->filesystem->mkdir($this->getAbsolutePath(). "/". ServerDirectoryInterface::DIRECTORY_BACKUPS, self::ACCESS_VALUE);
    }

    public function storeFile (
        string $subDirectory,
        mixed  $file
    ): void {
        $totalPath = $this->path . '/'. $subDirectory. '/'. ServerDirectoryInterface::MINECRAFT_SERVER_FILE;
        $this->filesystem->dumpFile($totalPath, $file);
    }

    public function getAllMinecraftFiles (): Finder {
        $finder = new Finder();
        $finder->in($this->getAbsoluteMinecraftPath());

        return $finder;
    }

    public function getAllBackupsFiles (): Finder {
        $finder = new Finder();
        $finder->in($this->getAbsoluteBackupPath());

        return $finder;
    }
}
