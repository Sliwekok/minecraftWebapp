<?php

declare(strict_types=1);

namespace App\Service\Backup;

use App\Entity\Server;
use App\Exception\Backup\BackupAlreadyExists;
use App\UniqueNameInterface\ServerCommandsInterface;
use App\Service\Filesystem\FilesystemService;
use Exception;
use SplFileInfo;

class ArchiveService
{

    public function __construct (
        string              $name,
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
            $backupPath = $filesystem->getAbsoluteBackupPath(). '/'. $name. '.zip';
            if ($filesystem->exists($backupPath)) {

                throw new BackupAlreadyExists();
            }

            $command = str_replace(ServerCommandsInterface::ARCHIVE_NAME, $name, ServerCommandsInterface::ARCHIVE_COMMAND);
            $proc = proc_open($command, [], $pipes, $filesystem->getAbsoluteMinecraftPath());
            $procData = proc_get_status($proc);
            /**
             * wait until file is already stored
             */
            while ($procData[ServerCommandsInterface::PROCESS_RUNNING]) {
                usleep(250);
                $procData = proc_get_status($proc);
            }
            proc_close($proc);

            return $this->getArchiveSize($backupPath);
        } catch (Exception $exception) {

            throw new Exception(
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

}
