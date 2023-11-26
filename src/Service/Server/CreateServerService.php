<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Login;
use App\Exception\Server\CouldNotDownloadAndSaveServerFileException;
use App\Service\Config\ConfigService;
use App\UniqueNameInterface\ServerDirectoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use DateTime;
use App\Entity\Server;
use App\Service\Filesystem\FilesystemService;
use App\Service\Mojang\MinecraftVersions;
use App\UniqueNameInterface\MojangInterface;
use App\UniqueNameInterface\ServerInterface;
use Symfony\Component\Form\FormInterface;

class CreateServerService
{
    public function __construct (
        private MinecraftVersions       $minecraftVersions,
        private ConfigService           $configService,
        private EntityManagerInterface  $entityManager,

    )
    {}

    public function createServer (
        FormInterface   $data,
        Login           $user
    ): void {
        $version = $data->get(ServerInterface::FORM_STEP1)->get(ServerInterface::FORM_STEP1_VERSION)->getData();
        $serverName = $data->get(ServerInterface::FORM_STEP1)->get(ServerInterface::FORM_STEP1_NAME)->getData();
        $seed = $data->get(ServerInterface::FORM_STEP1)->get(ServerInterface::FORM_STEP1_SEED)->getData();
        $directory = $user->getUsername() . '/' . $serverName;
        $fs = new FilesystemService($directory);

        if ($fs->directoryExists($directory)) {
            $fs->createDirectories();
        }

        $file = $this->getServerFile($version);
        $fs->storeFile(ServerDirectoryInterface::DIRECTORY_MINECRAFT, $file);
        $server = $this->createServerEntity($user, $directory, $serverName);
        $config = $this->configService->createConfig($server, $seed);

        $this->entityManager->persist($server);
        $this->entityManager->persist($config);
        $this->entityManager->flush();
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

    public function createServerEntity (
        Login           $user,
        string          $path,
        string          $serverName
    ): ?Server {
        $server = new Server();
        $server ->setCreateAt(new DateTime('now'))
                ->setModifiedAt(new DateTime('now'))
                ->setLogin($user)
                ->setDirectoryPath($path)
                ->setName($serverName)
                ->setStatus(ServerInterface::STATUS_OFFLINE)
            ;

        return $server;
    }
}