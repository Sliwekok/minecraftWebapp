<?php

declare(strict_types=1);

namespace App\Service\Filesystem;

use App\UniqueNameInterface\ServerDirectoryInterface;
use App\UniqueNameInterface\ServerUnixCommandsInterface;
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

    private string $userPath;

    public function __construct (
        string $path,
    ) {
        $path = str_replace(['"',"'"], "", $path);
        $this->filesystem = new Filesystem();
        $this->path = ServerDirectoryInterface::DIRECTORY. DIRECTORY_SEPARATOR . $path;
        $this->absolutePath = $this->createAbsolutePath();
        $this->userPath = $path;
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

    public function getAbsoluteModsPath (): string {
        return $this->absolutePath. DIRECTORY_SEPARATOR. ServerDirectoryInterface::DIRECTORY_MINECRAFT. DIRECTORY_SEPARATOR. ServerDirectoryInterface::DIRECTORY_MODS;
    }

    public function createDirectories (): void {
        $this->filesystem->mkdir($this->getAbsolutePath(), self::ACCESS_VALUE);
        $this->filesystem->mkdir($this->getAbsoluteMinecraftPath(), self::ACCESS_VALUE);
        $this->filesystem->mkdir($this->getAbsoluteBackupPath(),  self::ACCESS_VALUE);
        $this->filesystem->mkdir($this->getAbsoluteModsPath(),  self::ACCESS_VALUE);
    }

    public function storeFile (
        string $subDirectory,
        mixed  $file,
        string $filename
    ): void {
        $totalPath = $this->path . DIRECTORY_SEPARATOR. $subDirectory. DIRECTORY_SEPARATOR. $filename;
        $this->filesystem->dumpFile($totalPath, $file);
    }

    public function getAllMinecraftFiles (
        $findLogs = false
    ): Finder {
        $finder = new Finder();
        if ($findLogs) {
            $finder->in($this->getAbsoluteMinecraftPath());
        } else {
            $finder->in($this->getAbsoluteMinecraftPath())
                ->notName(['*.0', ServerUnixCommandsInterface::LOG_SUFFIX])
            ;
        }

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

    public function getLogFilePath (

    ): string {
        $filename = substr($this->userPath, strpos($this->userPath, DIRECTORY_SEPARATOR));
        $path = $this->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. $filename. '_console.log';

        return $path;
    }

    public function getLogFileContent (
        int $amountLastRows = 0
    ): string {
        $path = $this->getLogFilePath();
        $content = file_get_contents($path);
        if ($amountLastRows = 0) {

            return $content;
        } else {
            $lines = explode(PHP_EOL, trim($content));
            $lines = array_slice($lines, -$amountLastRows);
            $content = implode(PHP_EOL, $lines);

            return $content;
        }
    }

    /** default is 2, since there's command exec and command output */
    public function deleteRowsFromLogFile (
        int $amountLastRows = 2
    ): void {
        $path = $this->getLogFilePath();
        $content = file_get_contents($path);
        $lines = explode(PHP_EOL, trim($content));
        if (count($lines) > $amountLastRows) {
            $lines = array_slice($lines, 0, -$amountLastRows);
        }

        file_put_contents($path, implode(PHP_EOL, $lines));
    }

    public static function getFileSize (
        string $path
    ): int {
        $file = new \SplFileInfo($path);

        return $file->getSize();
    }

    /**
     * deletes file in given path
     */
    public function deleteFile (
        string $path
    ): void {
        @unlink($path);
    }
}
