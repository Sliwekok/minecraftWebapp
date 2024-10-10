<?php

declare(strict_types=1);

namespace App\Service\Mods;


use App\Entity\Mods;
use App\Entity\Server;
use App\Exception\Mods\ModWithThatNameAlreadyExistsException;
use App\Service\Filesystem\FilesystemService;
use App\UniqueNameInterface\ModsInterface;
use Doctrine\ORM\EntityManagerInterface;

class ModsService
{

    public function __construct(
        private EntityManagerInterface  $entityManager,
    ){}

    public function saveCustomMods (
        Server  $server,
        array   $mods
    ): void {
        $fs = new FilesystemService($server->getDirectoryPath());
        $modsDirectory = $fs->getAbsoluteModsPath();
        foreach ($mods as $mod) {
            $fileName = $mod->getClientOriginalName();
            if (!str_ends_with($fileName, ModsInterface::FILE_EXTENSION_JAR)) {
                $fileName = $fileName. ModsInterface::FILE_EXTENSION_JAR;
            }

            if ($fs->exists($modsDirectory. DIRECTORY_SEPARATOR. $fileName)) {

                throw new ModWithThatNameAlreadyExistsException();
            }

            $size = (int)round($mod->getSize() / 1024);
            $mod = new Mods();
            $mod
                ->setName($fileName)
                ->setSize($size)
                ->setServer($server)
                ->setAddedAt(new \DateTimeImmutable('now'))
            ;

            $this->entityManager->persist($mod);
        }

        $this->entityManager->flush();
    }

}
