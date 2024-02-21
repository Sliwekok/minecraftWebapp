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
            // delete from database
            $this->entityManager->remove($config);
            foreach ($backups as $backup) {
                $this->entityManager->remove($backup);
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
            $fs->remove($fs->getAllMinecraftFiles());
            $absolutePath = $fs->createAbsolutePath();
            foreach (glob($absolutePath. '/*') as $file) {
                if (is_dir($file)) {
                    rmdir($file);
                }
                else {
                    unlink($file);
                }
            }
            rmdir($absolutePath);

        } catch (Exception $exception) {

            throw new CouldNotDeleteServerException($exception->getMessage());
        }
    }
}
