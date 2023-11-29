<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Config;
use App\Entity\Login;
use App\Entity\Server;
use App\Service\Config\ConfigService;
use Symfony\Component\Form\FormInterface;

class ServerService
{

    public function __construct (
        private CreateServerService     $createServerService,
        private ServerCommanderService  $serverCommanderService,
        private ConfigService           $configService
    )
    {}

    public function createServer (
        FormInterface $data,
        Login         $user
    ): void {
        $server = $this->createServerService->createServer($data, $user);
        $this->initServer($server);
    }

    public function initServer (
        Server  $server
    ): void {
        $this->serverCommanderService->startServer($server);
        $this->updateConfig($server->getConfig());
        $this->createServerService->updateEula($server);
    }

    public function startServer (
        Server  $server
    ): void {
        $this->serverCommanderService->startServer($server);
    }

    public function stopServer (
        Server  $server
    ): void {

    }

    // alias to update config
    public function updateConfig (
        Config|array $config
    ): bool {
        return $this->configService->updateConfig($config);
    }
}