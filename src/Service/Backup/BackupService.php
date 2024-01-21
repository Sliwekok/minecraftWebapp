<?php

declare(strict_types=1);

namespace App\Service\Backup;

use App\Entity\Backup;
use App\Entity\Server;
use App\UniqueNameInterface\BackupInterface;
use DateTimeImmutable;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BackupService
{

    public function __construct (
        private Security    $security
    )
    {}

    public function createNewBackup (
        FormInterface   $form,
        Server          $server
    ): Backup {
        $time = new DateTimeImmutable('now');
        $name = $form->get(BackupInterface::FORM_NAME)->getData();
        $archive = new ArchiveService($name, $this->security);
        $fileSize = $archive->createArchive($name, $server);

        $backup = $this->makeBackupEntity($name, $time, $server, $fileSize);

        return $backup;
    }

    public function makeBackupEntity(
        string              $name,
        DateTimeImmutable   $time,
        Server              $server,
        int                 $size
    ): Backup {
        return (new Backup())
            ->setServer($server)
            ->setName($name)
            ->setCreatedAt($time)
            ->setSize($size)
        ;
    }
}
