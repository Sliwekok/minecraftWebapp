<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Config;
use App\Entity\Login;
use App\Entity\Server;
use App\Service\Config\ConfigService;
use App\Service\Server\Commander\ServerCommanderService;
use Symfony\Component\Form\FormInterface;

class ServerService
{

    public function __construct (
        private CreateServerService     $createServerService,
        private ServerCommanderService  $serverCommanderService,
        private ConfigService           $configService
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
        $this->serverCommanderService->startServer($server);
        $this->updateConfig($server->getConfig());
        $this->createServerService->updateEula($server);
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

}
