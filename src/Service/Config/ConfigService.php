<?php

declare(strict_types=1);

namespace App\Service\Config;

use App\Entity\Config;
use App\Entity\Server;
use App\Repository\ConfigRepository;
use App\UniqueNameInterface\ConfigInterface;
use Symfony\Component\Form\FormInterface;

class ConfigService
{

    public function __construct (
        private ConfigRepository        $configRepository,
        private UpdateConfigService     $updateConfigService
    )
    {}

    public function createConfig (
        Server $server,
        string $seed = null
    ): ?Config {
        $config = new Config();
        $port = $this->generatePort();
        if (!$seed) {
            $seed = bin2hex(random_bytes(16));
        }
        $config
            ->setAllowFlight(true)
            ->setDifficulty(ConfigInterface::DIFFICULTY_EASY)
            ->setHardcore(false)
            ->setMaxPlayers(16)
            ->setMaxRam(4)
            ->setPvp(false)
            ->setWhitelist(false)
            ->setPort($port)
            ->setServer($server)
            ->setSeed($seed)
            ->setMotd($server->getName())
            ->setLevelName($server->getName())
        ;

        return $config;
    }

    public function generatePort (): int {
        $configs = $this->configRepository->getPorts();
        if (0 === count($configs)) {

            return 25565;
        } else {
            $maxPort = max($configs);
            $maxPort++;

            return $maxPort;
        }
    }

    public function updateConfig (
        Config|FormInterface    $config,
        int                     $configId = null
    ): bool {
        return $this->updateConfigService->updateConfigEntity($config, $configId);
    }

    public function createConfigFromPropertyFile (
        Server  $server
    ): Config {
        $newConfig = $this->updateConfigService->updateConfigEntityFromFile($server);
        /** we need to ensure that whole config is properly written - and adjust ip and ports */
        $this->updateConfigService->updateConfigEntity($newConfig);

        return $newConfig;
    }

}
