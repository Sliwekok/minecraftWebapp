<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Login;
use App\Exception\Server\CouldNotDeleteServerException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Entity\Server;
use App\Service\Filesystem\FilesystemService;

class DeleteServerService
{
    public function __construct (
        private EntityManagerInterface  $entityManager,
    )
    {}

    public function deleteServer (
        Server  $server,
    ): void {
        try {
            $config = $server->getConfig();
            $backups = $server->getBackups();
            $mods = $server->getMods();
            // delete from database
            $this->entityManager->remove($config);
            foreach ($backups as $backup) {
                $this->entityManager->remove($backup);
            }
            foreach ($mods as $mod) {
                $this->entityManager->remove($mod);
            }

            $this->entityManager->remove($server);

            // delete local minecraft and backup files
            $this->deleteLocalFiles($server->getDirectoryPath());

            $this->entityManager->flush();
        } catch (Exception $exception) {
            throw new CouldNotDeleteServerException($exception->getMessage());
        }
    }

    // function to delete local files from user directory
    public function deleteLocalFiles (
        string  $path
    ): void {
        try {
            $fs = new FilesystemService($path);
            // check if backups are created yet
            if (is_dir($fs->getAbsoluteBackupPath())) {
                $backupFiles = $fs->getAllBackupsFiles();
                if ($backupFiles->count() !== 0) {
                    $fs->remove($backupFiles);
                }
            }
            $path = $fs->getAbsoluteMinecraftPath();
            if (is_dir($path)) {
                $this->delTree($path);
            }


        } catch (Exception $exception) {

            throw new CouldNotDeleteServerException($exception->getMessage());
        }
    }

    public function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }
}
