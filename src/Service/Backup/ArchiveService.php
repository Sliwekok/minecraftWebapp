<?php

declare(strict_types=1);

namespace App\Service\Backup;

use App\Entity\Server;
use App\Exception\Backup\CouldNotCreateBackupFile;
use ZipArchive;
use App\Service\Filesystem\FilesystemService;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Bundle\SecurityBundle\Security;
use Exception;

class ArchiveService
{

    public function __construct (
        string              $name,
        private Security    $security
    )
    {}

    /**
     * @param string $name
     * @return int that's total file size
     */
    public function createArchive (
        string $name,
        Server $server
    ): int {
        try {
            $filesystem = new FilesystemService($server->getDirectoryPath());
            $absolutePath = $filesystem->getAbsolutePath();
            $backupPath = $filesystem->makePathRelative($filesystem->getAbsoluteBackupPath(), $absolutePath);
            $name = $absolutePath. '/'.  $name. '.zip';
            $zip = new ZipArchive();
            $zip->open($name, ZipArchive::CREATE|ZipArchive::OVERWRITE);
            $t = $zip;
            $filesystem->setPathToMinecraft();
            $files = $filesystem->getAllFiles();
            $user = $this->security->getUser();

            $ZIP_ERROR = [
                ZipArchive::ER_EXISTS => 'File already exists.',
                ZipArchive::ER_INCONS => 'Zip archive inconsistent.',
                ZipArchive::ER_INVAL => 'Invalid argument.',
                ZipArchive::ER_MEMORY => 'Malloc failure.',
                ZipArchive::ER_NOENT => 'No such file.',
                ZipArchive::ER_NOZIP => 'Not a zip archive.',
                ZipArchive::ER_OPEN => "Can't open file.",
                ZipArchive::ER_READ => 'Read error.',
                ZipArchive::ER_SEEK => 'Seek error.',
            ];

            $result_code = $zip->open($backupPath);
            if( $result_code !== true ){
                $msg = isset($ZIP_ERROR[$result_code])? $ZIP_ERROR[$result_code] : 'Unknown error.';
                die ($msg);
            }

            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                $filePath = $file->getRealPath();
                $zip->addFile($filePath, substr($filePath, strlen($absolutePath) + 1));
            }
//          $zip->setPassword($user->getPassword());
            $zip->close();
            $filesystem->copy(
                $name,
                $filesystem->getBackupPath(). '/'. $name
            );
//          $size = filesize($filesystem->getBackupPath(). '/'. $name);

//          return $size;
            return 2137;
        } catch (Exception $exception) {
            throw new CouldNotCreateBackupFile(
                $exception->getMessage(),
            );
        }
    }

}
