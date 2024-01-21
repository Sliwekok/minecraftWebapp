<?php

declare(strict_types=1);

namespace App\Service\Backup;

use App\Entity\Server;
use ZipArchive;
use App\Service\Filesystem\FilesystemService;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Bundle\SecurityBundle\Security;

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
        $name = $name. '.zip';
        $zip = new ZipArchive();
        $filesystem = new FilesystemService($server->getDirectoryPath());
        $zip->open($name, ZipArchive::CREATE);
        $filesystem->setPathToMinecraft();
        $files = $filesystem->getAllFiles();
        $absolutePath = $filesystem->getAbsolutePath();
        $user = $this->security->getUser();

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $zip->addFile($filePath, substr($filePath, strlen($absolutePath) + 1));
        }
//        $zip->setPassword($user->getPassword());
        $zip->close();
        $filesystem->copy(
            $name,
            $filesystem->getBackupPath(). '/'. $name
        );
//        $size = filesize($filesystem->getBackupPath(). '/'. $name);

//        return $size;
        return 2137;
    }

}
