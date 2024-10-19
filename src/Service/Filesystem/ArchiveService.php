<?php

declare(strict_types=1);

namespace App\Service\Filesystem;

use App\Entity\Server;
use App\Exception\Backup\BackupAlreadyExists;
use App\Exception\Backup\CouldNotCreateBackupFile;
use App\Exception\Backup\CouldNotUnpackArchive;
use App\Service\Helper\OperatingSystemHelper;
use App\Service\Helper\RunCommandHelper;
use App\UniqueNameInterface\BackupInterface;
use App\UniqueNameInterface\ServerDirectoryInterface;
use App\UniqueNameInterface\ServerUnixCommandsInterface;
use App\UniqueNameInterface\ServerWindowsCommandsInterface;
use Exception;
use SplFileInfo;

class ArchiveService
{

    public function __construct (
        private RunCommandHelper    $commandHelper
    )
    {}

    /**
     * function executes shell comands since it's faster and doesn't mess up with permissions
     * @param string $name
     * @return int that's total file size
     */
    public function createArchive (
        string $name,
        Server $server
    ): int {
        try {
            $filesystem = new FilesystemService($server->getDirectoryPath());
            $backupPath = $filesystem->getAbsoluteBackupPath(). '/'. $name;
            if ($filesystem->exists($backupPath)) {

                throw new BackupAlreadyExists();
            }

            if (!is_dir($filesystem->getAbsoluteBackupPath())) {
                $filesystem->mkdir($filesystem->getAbsoluteBackupPath());
            }

            if (OperatingSystemHelper::isWindows()) {
                $command = str_replace(ServerWindowsCommandsInterface::ARCHIVE_NAME, $name, ServerWindowsCommandsInterface::ARCHIVE_COMMAND);
                $this->commandHelper->runCommand($command, $filesystem->getAbsoluteMinecraftPath(), true);
            } else {
                $command = $this->getUnixZipCommand($name, $server);
                $this->commandHelper->runCommand($command, $filesystem->getPath());
            }

            return $this->getArchiveSize($backupPath);
        } catch (Exception $exception) {

            throw new CouldNotCreateBackupFile(
                $exception->getMessage()
            );
        }
    }

    private function getArchiveSize (
        string  $path
    ): int {

        $file = new SplFileInfo($path);
        $size = (int)round($file->getSize() / 1024);

        return $size;
    }


    public function unpackArchive (
        string  $file,
        string  $backupPath,
        string  $minecraftPath
    ): void {
        try {
            $fileName = str_replace(BackupInterface::FILE_EXTENSION_ZIP, '', $file);
            $zip = new BetterZipArchive();
            $zip->open($backupPath. DIRECTORY_SEPARATOR. $file);

            /** we need to check if archived file is in root or has subdirectory */
            $filePath = $zip->getNameIndex(0);
            if ($filePath === ServerDirectoryInterface::DIRECTORY_MINECRAFT. DIRECTORY_SEPARATOR) {
                if (str_starts_with($filePath, ServerDirectoryInterface::DIRECTORY_MINECRAFT . DIRECTORY_SEPARATOR)) {
                    $zip->extractSubdirTo($minecraftPath, $filePath);
                }
            } elseif ($filePath === "$fileName". DIRECTORY_SEPARATOR) {
                if (strpos($filePath, "$fileName". DIRECTORY_SEPARATOR) === 0) {
                    $zip->extractSubdirTo($minecraftPath, $filePath);
                }
            } else {
                $zip->extractTo($minecraftPath);
            }
            $zip->close();
        } catch (Exception $exception) {

            throw new CouldNotUnpackArchive($exception->getMessage());
        }

    }

    private function getUnixZipCommand (
        string              $name,
        Server              $server,
    ): string {
        // set archive name
        $command = str_replace(
            ServerUnixCommandsInterface::ARCHIVE_NAME,
            $name,
            ServerUnixCommandsInterface::ARCHIVE_COMMAND
        );
        // set path to minecraft directory
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_PATH,
            ServerDirectoryInterface::DIRECTORY_MINECRAFT,
            $command
        );
        // add this to disable log files used in screen
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            $command
        );
        // move created zip to backup files
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_BACKUPPATH,
            ServerDirectoryInterface::DIRECTORY_BACKUPS,
            $command
        );

        return $command;
    }
}
