<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Exception\Server\CouldNotDownloadAndSaveServerFileException;
use App\UniqueNameInterface\ServerDirectoryInterface;
use Exception;
use App\Entity\Server;
use App\Repository\ServerRepository;
use App\Service\Filesystem\FilesystemService;
use App\Service\Mojang\MinecraftVersions;
use App\UniqueNameInterface\MojangInterface;
use App\UniqueNameInterface\ServerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateServerService
{
    public function __construct (
        private ServerRepository    $serverRepository,
        private MinecraftVersions   $minecraftVersions,
    )
    {}

    public function createServer (
        FormInterface $data,
        UserInterface $user
    ): void {
        // add file creation, add server version validation (if exists in array of version from api)
        $version = $data->get(ServerInterface::FORM_STEP1)->get(ServerInterface::FORM_STEP1_VERSION)->getData();
        $serverName = $data->get(ServerInterface::FORM_STEP1)->get(ServerInterface::FORM_STEP1_NAME)->getData();
        $directory = $user->getUsername() . '/' . $serverName;
        $fs = new FilesystemService($directory);

        if ($fs->directoryExists($directory)) {
            $fs->createDirectories();
        }

        $file = $this->getServerFile($version);
        $fs->storeFile(ServerDirectoryInterface::DIRECTORY_MINECRAFT, $file);

        return;
    }

    public function getServerFile (
        string $version,
    ): mixed {
        // double request because server file is in second url
        try {
            $fileUrl = $this->minecraftVersions->getSpecificVersion($version)[MojangInterface::VERSIONS_MANIFEST_URL];
            $specificData = json_decode(file_get_contents($fileUrl), true);

            $serverFileUrl = $specificData[MojangInterface::PACKAGES_DOWNLOADS]
                [MojangInterface::PACKAGES_DOWNLOADS_SERVER][MojangInterface::PACKAGES_DOWNLOADS_SERVER_URL];

            return file_get_contents($serverFileUrl);
        } catch (Exception $exception) {
            throw new CouldNotDownloadAndSaveServerFileException($exception->getMessage());
        }
    }
}