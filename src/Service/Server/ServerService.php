<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Config;
use App\Entity\Login;
use App\Entity\Server;
use App\Service\Config\ConfigService;
use App\Service\Filesystem\FilesystemService;
use App\Service\Helper\ServerFileHelper;
use App\Service\Server\Commander\ServerCommanderService;
use App\UniqueNameInterface\ServerDirectoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class ServerService
{

    public function __construct (
        private CreateServerService     $createServerService,
        private ServerCommanderService  $serverCommanderService,
        private ConfigService           $configService,
        private EntityManagerInterface  $entityManager,
        private ServerFileHelper        $serverFileHelper
    )
    {}

    /**
     * create basic directory structure, entity and all required configs for server
     */
    public function createServer (
        FormInterface $data,
        Login         $user
    ): void {
        $server = $this->createServerService->createServer($data, $user);
        $this->initServer($server);
    }

    /**
     * initialization of new server - along with starting-up updates EULA for game and updates game config as in creation menu
     */
    public function initServer (
        Server  $server
    ): void {
        $fs = new FilesystemService($server->getDirectoryPath());
        $this->startServer($server);
        while (!file_exists($fs->getAbsoluteMinecraftPath() . '/' . ServerDirectoryInterface::MINECRAFT_EULA)) {
            sleep(1); // wait until eula is created to update it
        }
        $this->stopServer($server);

        $this->createServerService->updateEula($server);
        // we need to re-run server due to eula update
        $this->startServer($server);
        while (!dir($fs->getAbsoluteMinecraftPath() . '/' . ServerDirectoryInterface::MINECRAFT_SERVERPROPERTIES)) {
            sleep(1); // wait until config file is created
        }
        $this->stopServer($server);
        $this->updateConfig($server->getConfig());

        return;
    }

    /**
     * start server up and update database
     */
    public function startServer (
        Server  $server
    ): void {
        $this->serverCommanderService->startServer($server);
    }

    /**
     * stop server and update database
     */
    public function stopServer (
        Server  $server
    ): void {
        $this->serverCommanderService->stopServer($server);
    }

    /**
     * alias to update config
     */
    public function updateConfig (
        Config|array $config
    ): bool {
        return $this->configService->updateConfig($config);
    }

    public function updateServerType (
        Server  $server,
        string  $newType
    ): void {
        $fs = new FilesystemService($server->getDirectoryPath());
        $fs->deleteFile($fs->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. ServerDirectoryInterface::MINECRAFT_SERVER_FILE);

        $file = $this->serverFileHelper->getServerFile(
            $server->getVersion(),
            $newType,
            $fs->getAbsoluteMinecraftPath()
        );
        $fs->dumpFile(
            $fs->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. ServerDirectoryInterface::MINECRAFT_SERVER_FILE,
            $file
        );

        $server = $server->setType($newType);
        $this->entityManager->persist($server);
        $this->entityManager->flush();
    }

    public function getServerUsageFile (
        Server  $server
    ): mixed {
        $data = $this->serverCommanderService->getServerUsageFile($server);

        return str_replace('|', '', $data);
    }

    public function getServerUsage (
        Server  $server
    ): array {
        return $this->serverCommanderService->getServerUsage($server);
    }
}
