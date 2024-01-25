<?php

declare(strict_types=1);

namespace App\Service\Backup;

use App\Entity\Backup;
use App\Entity\Server;
use App\Exception\Backup\BackupAlreadyExists;
use App\Exception\Backup\CouldNotCreateBackupFile;
use App\Service\Filesystem\ArchiveService;
use App\Service\Filesystem\FilesystemService;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserBackupService
{

    public function __construct (
        private ArchiveService          $archiveService,
        private BackupService           $backupService,
        private EntityManagerInterface  $entityManager,
    )
    {}


    public function storeCustomBackup (
        UploadedFile    $file,
        Server          $server
    ): string {
        $fs = new FilesystemService($server->getDirectoryPath());
        $backupPath = $fs->getBackupPath();
        $fileName = $file->getClientOriginalName();

        if ($fs->exists($backupPath. '/'. $fileName)) {

            throw new BackupAlreadyExists();
        }

        try {
            $size = (int)round($file->getSize() / 1024);
            $file->move($backupPath, $fileName);

            $backup = (new Backup())
                ->setServer($server)
                ->setSize($size)
                ->setCreatedAt(new DateTimeImmutable('now'))
                ->setName($fileName)
            ;
            $this->entityManager->persist($backup);
            $this->entityManager->flush();
        } catch (Exception $exception) {

            throw new CouldNotCreateBackupFile($exception->getMessage());
        }

        return $backupPath. '/'. $fileName;
    }

    public function unpackUserBackup (
        string  $newArchiveName,
        Server  $server
    ): void {
        $fs = new FilesystemService($server->getDirectoryPath());
        /** create current world archive and delete current world*/
        $oldWorldName = 'backup_' . $server->getName(). '_' . (new DateTime('now'))->format('Y-m-d_H.i.s');
        $size = $this->archiveService->createArchive($oldWorldName, $server);
        $backup = $this->backupService->makeBackupEntity($oldWorldName, $server, $size);
        $this->entityManager->persist($backup);
        $this->entityManager->flush();
        /** remove all current minecraft files */
        $fs->remove($fs->getAllFiles());
        $this->archiveService->unpackArchive(
            $newArchiveName,
            $fs->getAbsoluteBackupPath(),
            $fs->getAbsoluteMinecraftPath()
        );
    }
}
