<?php

declare(strict_types=1);

namespace App\Service\Backup;

use App\Entity\Alert;
use App\Entity\Backup;
use App\Entity\Server;
use App\Repository\BackupRepository;
use App\Service\Filesystem\ArchiveService;
use App\Service\Filesystem\FilesystemService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BackupService
{

    public function __construct (
        private EntityManagerInterface  $entityManager,
        private UserBackupService       $userBackupService,
        private BackupRepository        $backupRepository,
        private ArchiveService          $archiveService,
        private LoggerInterface         $backupLogger,
    )
    {}

    public function createNewBackup (
        string  $name,
        Server  $server
    ): Backup {
        $fileSize = $this->archiveService->createArchive($name, $server);
        $backup = $this->makeBackupEntity($name, $server, $fileSize);
        $server->addBackup($backup);
        $this->entityManager->persist($backup);
        $this->entityManager->persist($server);
        $this->entityManager->flush();

        $this->backupLogger->info('Created new backup', [
            'server_id'     => $server->getId(),
            'backup_name'   => $backup->getName(),
            'backup_size'   => $backup->getSize(),
            'user_id'       => $server->getLogin()->getId(),
            'created_at'    => $backup->getCreatedAt()
        ]);

        return $backup;
    }

    public function makeBackupEntity (
        string              $name,
        Server              $server,
        int                 $size
    ): Backup {
        return (new Backup())
            ->setServer($server)
            ->setName($name)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setSize($size)
        ;
    }

    /**
     * check user access to backup, return Alert if doesn't have permission or path to backup
     */
    public function download (
        Server  $server,
        int     $id
    ): Alert|SplFileInfo {
        $backups = $server->getBackups();
        $userBackup = null;
        /**
         * iterate through user server backups
         * @param Backup $backup
         */
        foreach ($backups as $backup) {
            if ($backup->getId() === $id) {
                $userBackup = $backup;
            }
        }

        if ($userBackup === null) {

            return Alert::error("You do not have permission to that backup");
        }

        $filesystem = new FilesystemService($server->getDirectoryPath());
        $backupPath = $filesystem->getAbsoluteBackupPath(). '/'. $backup->getName();
        $backupFile = new SplFileInfo($backupPath);

        return $backupFile;
    }

    public function loadBackup (
        int     $backupId,
        Server  $server
    ): void {
        $backup = $this->backupRepository->find($backupId);
        $fs = new FilesystemService($server->getDirectoryPath());
        $fs->remove($fs->getAllMinecraftFiles());

        $this->archiveService->unpackArchive(
            $backup->getName(),
            $fs->getAbsoluteBackupPath(),
            $fs->getAbsoluteMinecraftPath()
        );

        $this->backupLogger->info('Loaded existing backup', [
            'server_id'     => $server->getId(),
            'backup_name'   => $backup->getName(),
            'backup_size'   => $backup->getSize(),
            'user_id'       => $server->getLogin()->getId(),
            'action_at'     => time()
        ]);
    }

    public function storeCustomBackup (
        UploadedFile    $file,
        Server          $server
    ): string {
        return $this->userBackupService->storeCustomBackup($file, $server);
    }
    public function unpackUserBackup (
        string  $fileName,
        Server  $server
    ): void {
        $this->userBackupService->unpackUserBackup($fileName, $server);
    }
}
