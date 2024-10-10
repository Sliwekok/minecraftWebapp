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
        $this->createServerService->updateEula($server);
        $this->startServer($server);
        $this->updateConfig($server->getConfig());
        $this->stopServer($server);
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
        $server = $server->setType($newType);
        $this->entityManager->persist($server);
        $this->entityManager->flush();

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
    }

}
