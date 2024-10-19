<?php

declare(strict_types=1);

namespace App\Service\Mods;


use App\Entity\Mods;
use App\Entity\Server;
use App\Exception\Mods\CouldNotDownloadFileFromCurseforge;
use App\Exception\Mods\ModWithThatNameAlreadyExistsException;
use App\Repository\ModsRepository;
use App\Service\Filesystem\FilesystemService;
use App\Service\Mods\Curseforge\CurseforgeService;
use App\UniqueNameInterface\CurseforgeInterface;
use App\UniqueNameInterface\ModsInterface;
use App\UniqueNameInterface\ServerDirectoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ModsService
{

    public function __construct(
        private EntityManagerInterface  $entityManager,
        private CurseforgeService       $curseforgeService,
        private ModsRepository          $modsRepository
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
            $modEntity = new Mods();
            $modEntity
                ->setName($fileName)
                ->setSize($size)
                ->setServer($server)
                ->setAddedAt(new \DateTimeImmutable('now'))
                ->setFilename($fileName)
            ;

            $this->entityManager->persist($modEntity);
            $fs->dumpFile($modsDirectory. DIRECTORY_SEPARATOR. $fileName, $mod);
        }

        $this->entityManager->flush();
    }

    public function getCurseforgeMods (
        Server  $server,
        int     $index,
        string  $sortBy = '',
        string  $category = '',
        string  $searchFilter = '',
    ): array {
        return $this->curseforgeService->getMods($server, $index, $sortBy, $category, $searchFilter);
    }

    public function getCurseforgeCategories (): array {
        return $this->curseforgeService->getCategories();
    }

    public function getCurseforgeSortables (): array {
        return $this->curseforgeService->getSortableFields();
    }

    public function installModFromCurseforge (
        Server  $server,
        int     $id,
    ): mixed {
        try {
            $modFile = $this->curseforgeService->getModSpecific($id);
            $file = file_get_contents($modFile[CurseforgeInterface::API_DATA_LATESTFILES][0][CurseforgeInterface::API_DATA_LATESTFILES_DOWNLOADURL]);
            $fs = new FilesystemService($server->getDirectoryPath());

            $filePath = ServerDirectoryInterface::DIRECTORY_MINECRAFT. DIRECTORY_SEPARATOR. ServerDirectoryInterface::DIRECTORY_MODS. DIRECTORY_SEPARATOR;
            $fileName = $modFile[CurseforgeInterface::API_DATA_LATESTFILES][0][CurseforgeInterface::API_DATA_NAME_FILE];

            if ($fs->exists($fs->getAbsoluteModsPath(). DIRECTORY_SEPARATOR. $fileName)) throw new ModWithThatNameAlreadyExistsException();
            $fs->storeFile(
                $filePath,
                $file,
                $fileName
            );

            /** wait until file is saved on disk */
            $count = 0;
            while ($count < 3) {
                if ($fs->exists($fs->getAbsoluteModsPath(). DIRECTORY_SEPARATOR. $fileName)) break;
                $count++;
                usleep(500);
            }

            $mod = new Mods();
            $mod->setName($modFile[CurseforgeInterface::API_DATA_NAME])
                ->setSize(FilesystemService::getFileSize($fs->getAbsoluteModsPath(). DIRECTORY_SEPARATOR. $fileName))
                ->setServer($server)
                ->setUrl(CurseforgeInterface::BASE_URL_SPECIFIC_MOD. $id)
                ->setAddedAt(new \DateTimeImmutable())
                ->setExternalId((string)$id)
                ->setFilename($fileName)
                ->setSummary($modFile[CurseforgeInterface::API_KEY_SUMMARY])
                ->setThumbnail($modFile[CurseforgeInterface::API_KEY_LOGO][CurseforgeInterface::API_KEY_LOGO_THUMBNAILURL])
                ->setWebsiteUrl($modFile[CurseforgeInterface::API_KEY_LINKS][CurseforgeInterface::API_KEY_LINKS_WEBSITEURL])
            ;
            $server->addMod($mod);

            $this->entityManager->persist($mod);
            $this->entityManager->persist($server);
            $this->entityManager->flush();

            return $mod;
        } catch (\Exception $e) {

            throw new CouldNotDownloadFileFromCurseforge($e->getMessage());
        }
    }

    public function delete (
        Server  $server,
        int     $id
    ): void {
        $mod = $this->modsRepository->find($id);
        $fs = new FilesystemService($server->getDirectoryPath());
        $filePath = $fs->getAbsoluteModsPath(). DIRECTORY_SEPARATOR. $mod->getName();
        $fs->deleteFile($filePath);
        $this->entityManager->remove($mod);
        $this->entityManager->flush();
    }

    public function getModsIds(
        Server  $server
    ): array {
        return $this->modsRepository->getMods($server);
    }
}
