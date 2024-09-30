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
    private Filesystem $filesystem;
    private string $absolutePath;

    public function __construct (
        string $path,
    ) {
        $path = str_replace(['"',"'"], "", $path);
        $this->filesystem = new Filesystem();
        $this->path = ServerDirectoryInterface::DIRECTORY. DIRECTORY_SEPARATOR . $path;
        $this->absolutePath = $this->createAbsolutePath();
    }

    public function getPath (): string {
        return $this->path;
    }

    public function getBackupPath (): string {
        return $this->path. DIRECTORY_SEPARATOR. ServerDirectoryInterface::DIRECTORY_BACKUPS;
    }

    public static function getAbsolutePublicPath (): string {
        $path = explode(DIRECTORY_SEPARATOR, realpath(__DIR__));
        // trim last 3 parts of path since it's path to service directory
        $path = implode(DIRECTORY_SEPARATOR, array_slice($path,0 , count($path) - 3)) . DIRECTORY_SEPARATOR. 'public'. DIRECTORY_SEPARATOR;

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
        return $this->absolutePath. DIRECTORY_SEPARATOR. ServerDirectoryInterface::DIRECTORY_MINECRAFT;
    }

    public function getAbsoluteBackupPath (): string {
        return $this->absolutePath. DIRECTORY_SEPARATOR. ServerDirectoryInterface::DIRECTORY_BACKUPS;
    }

    public function createDirectories (): void {
        $this->filesystem->mkdir($this->getAbsolutePath(), self::ACCESS_VALUE);
        $this->filesystem->mkdir($this->getAbsoluteMinecraftPath(), self::ACCESS_VALUE);
        $this->filesystem->mkdir($this->getAbsoluteBackupPath(),  self::ACCESS_VALUE);
    }

    public function storeFile (
        string $subDirectory,
        mixed  $file
    ): void {
        $totalPath = $this->path . DIRECTORY_SEPARATOR. $subDirectory. DIRECTORY_SEPARATOR. ServerDirectoryInterface::MINECRAFT_SERVER_FILE;
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

    public function createLogFile (
        string  $name
    ): void {
        $this->filesystem->dumpFile(
            $this->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. $name. "_console.log",
            ''
        );
    }

    public function getLogFileContent (
        string $fileName
    ): string {
        $path = $this->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. $fileName. '_console.log';

        return file_get_contents($path);
    }
}
